<?php

declare(strict_types=1);

namespace App\Services\PaymentService;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ParcelOrder;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Models\SelcomPayment;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Services\CoreService;
use App\Library\Selcom\Selcom;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SelcomService extends CoreService
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    /**
     * Process a payment transaction
     *
     * @param array $data
     * @return PaymentProcess|\Illuminate\Database\Eloquent\Model
     * @throws Throwable
     */
    public function orderProcessTransaction(array $data)
    {
        Log::info('Processing Selcom payment transaction', $data);

        $payment = Payment::where('tag', Payment::TAG_SELCOM)->first();
        
        if (!$payment) {
            throw new Exception('Selcom payment method is not configured');
        }

        $paymentPayload = PaymentPayload::where('payment_id', $payment->id)->first();
        $payload = $paymentPayload?->payload;

        if (!$payload) {
            throw new Exception('Selcom payment configuration is missing');
        }

        // Determine the primary model for which payment is being initiated
        $order = $this->getOrderModel($data);
        $totalPrice = $this->calculateTotalPrice($data, $order);

        // Generate a unique transaction reference
        $trxRef = $this->generateTransactionReference($order, $data);
        
        // Set up URLs for callbacks
        $host = request()->getSchemeAndHttpHost();
        $redirectUrl = "$host/selcom-result?status=success&trxRef=$trxRef";
        $cancelUrl = "$host/selcom-result?status=error&trxRef=$trxRef";
        $webhookUrl = "$host/api/v1/webhook/selcom/payment?trxRef=$trxRef";

        // Initialize Selcom API
        $api = new Selcom($payload, $redirectUrl, $cancelUrl, $webhookUrl);
        
        // Prepare payment data
        $paymentData = $this->preparePaymentData($data, $order, $totalPrice, $trxRef);
        
        // Create payment order with Selcom
        $response = $api->cardCheckoutUrl($paymentData);

        // Handle API response
        if (data_get($response, 'result') === 'FAIL') {
            throw new Exception(data_get($response, 'message', 'Payment processing failed'));
        }

        if (!isset($response['data'][0])) {
            throw new Exception('Payment gateway URL not found in response');
        }

        $paymentUrl = base64_decode(data_get($response['data'][0], 'payment_gateway_url'));
        Log::info("Selcom payment URL generated: $paymentUrl");

        // Save payment process
        return $this->savePaymentProcess($data, $trxRef, $paymentUrl, $totalPrice, $payment->id);
    }

    /**
     * Get the order model based on request data
     */
    protected function getOrderModel(array $data)
    {
        if (data_get($data, 'parcel_id')) {
            return ParcelOrder::findOrFail(data_get($data, 'parcel_id'));
        }
        
        if (data_get($data, 'cart_id')) {
            return Cart::findOrFail(data_get($data, 'cart_id'));
        }
        
        return null;
    }

    /**
     * Calculate total price for the order
     */
    protected function calculateTotalPrice(array $data, $order): float
    {
        if ($order) {
            return (float) ($order->total_price ?? $order->price ?? 0);
        }
        
        return (float) (data_get($data, 'total_price', 0));
    }

    /**
     * Generate a unique transaction reference
     */
    protected function generateTransactionReference($order, array $data): string
    {
        $prefix = $order ? ($order->id . '-') : '';
        return $prefix . time() . '-' . Str::random(6);
    }

    /**
     * Prepare payment data for Selcom API
     */
    protected function preparePaymentData(array $data, $order, float $totalPrice, string $trxRef): array
    {
        $user = auth('sanctum')->user();
        
        return [
            'name' => $order?->username ?? ($user ? "{$user->firstname} {$user->lastname}" : 'Customer'),
            'email' => $order?->user?->email ?? ($user?->email ?? 'customer@example.com'),
            'phone' => $this->formatPhone($order?->phone ?? $user?->phone ?? ''),
            'amount' => $totalPrice,
            'transaction_id' => $trxRef,
            'address' => $order?->user?->address?->address ?? 'N/A',
            'postcode' => $order?->user?->address?->postcode ?? '',
            'user_id' => $user?->id,
            'country_code' => $order?->user?->address?->country?->code ?? 'TZ',
            'state' => $order?->user?->address?->region?->title ?? 'Dar es Salaam',
            'city' => $order?->user?->address?->city?->title ?? 'Dar es Salaam',
            'billing_phone' => $this->formatPhone($order?->phone ?? $user?->phone ?? ''),
            'currency' => data_get($data, 'currency', 'TZS'),
            'items' => 1,
        ];
    }

    /**
     * Save payment process
     */
    protected function savePaymentProcess(array $data, string $trxRef, string $url, float $amount, int $paymentId): PaymentProcess
    {
        return PaymentProcess::updateOrCreate(
            [
                'user_id' => auth('sanctum')->id(),
                'model_id' => data_get($data, 'cart_id', data_get($data, 'parcel_id')),
                'model_type' => data_get($data, 'parcel_id') ? ParcelOrder::class : Cart::class,
            ],
            [
                'id' => $trxRef,
                'data' => [
                    'url' => $url,
                    'price' => $amount,
                    'cart' => $data,
                    'shop_id' => data_get($data, 'shop_id'),
                    'payment_id' => $paymentId,
                ]
            ]
        );
    }

    /**
     * Format phone number for Selcom
     */
    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading 0 if present
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        
        // Add country code if not present
        if (!str_starts_with($phone, '255')) {
            $phone = '255' . $phone;
        }
        
        return $phone;
    }

    /**
     * Process subscription payment
     */
    public function subscriptionProcessTransaction(array $data, Shop $shop, string $currency)
    {
        // Implementation for subscription payments
        // Similar to orderProcessTransaction but tailored for subscriptions
    }

    /**
     * Handle payment result
     */
    public function resultTransaction(string $transId)
    {
        $payment = SelcomPayment::where('transid', $transId)->first();
        
        if (!$payment) {
            return null;
        }

        $paymentConfig = $this->getPaymentConfig();
        $api = new Selcom($paymentConfig);
        
        $response = $api->orderStatus($payment->order_id);

        if ($response && $response['result'] === 'SUCCESS' && $response['data'][0]['payment_status'] === 'COMPLETED') {
            $this->updateOrderStatus($payment->order_id);
            
            return [
                'status' => data_get($response['data'][0], 'payment_status'),
                'token' => data_get($response['data'][0], 'transid')
            ];
        }

        return null;
    }

    /**
     * Get payment configuration
     */
    protected function getPaymentConfig(): array
    {
        $payment = Payment::where('tag', Payment::TAG_SELCOM)->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment->id)->first();
        
        return $paymentPayload?->payload ?? [];
    }

    /**
     * Update order status after successful payment
     */
    protected function updateOrderStatus(string $orderId): void
    {
        $order = Order::find($orderId);
        
        if ($order) {
            $order->update(['status' => Order::STATUS_PAID]);
            // Trigger any additional order completion logic here
        }
    }

    /**
     * Process after payment hook
     */
    public function afterHook(string $transactionId, string $status): void
    {
        // Handle post-payment logic
        // Update order status, send notifications, etc.
    }
}
