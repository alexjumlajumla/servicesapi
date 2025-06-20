<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\v1\Payment;

use App\Http\Controllers\API\v1\PaymentController;
use App\Models\Order;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Services\PaymentService\SelcomService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SelcomController extends PaymentController
{
    /**
     * @param SelcomService $selcomService
     */
    public function __construct(
        private SelcomService $selcomService
    )
    {
        parent::__construct();
    }

    /**
     * Process payment via Selcom
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function processTransaction(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Validate required fields
            $required = ['cart_id', 'total_price', 'currency_id'];
            
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    throw new Exception("The $field field is required");
                }
            }
            
            $paymentProcess = $this->selcomService->orderProcessTransaction($data);

            return $this->successResponse(__('web.redirect_to_payment'), [
                'url' => $paymentProcess->data['url'] ?? null,
                'process' => $paymentProcess
            ]);
            
        } catch (Throwable $e) {
            Log::error('Selcom payment error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->onErrorResponse([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle successful payment
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function resultTransaction(Request $request): JsonResponse
    {
        try {
            $trxRef = $request->query('trxRef');
            
            if (!$trxRef) {
                throw new Exception('Transaction reference not found');
            }
            
            $result = $this->selcomService->resultTransaction($trxRef);
            
            if (!$result) {
                throw new Exception('Payment verification failed');
            }
            
            // Get the payment process
            $paymentProcess = PaymentProcess::where('id', $trxRef)->first();
            
            if (!$paymentProcess) {
                throw new Exception('Payment process not found');
            }
            
            // Update order status or perform other post-payment actions
            $this->updateOrder($paymentProcess);
            
            return $this->successResponse('Payment successful', [
                'status' => 'success',
                'order_id' => $paymentProcess->model_id ?? null,
                'payment_status' => $result['status'] ?? 'completed',
                'token' => $result['token'] ?? null
            ]);
            
        } catch (Throwable $e) {
            Log::error('Selcom payment result error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->onErrorResponse([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Handle Selcom webhook
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            
            Log::info('Selcom webhook received', $payload);
            
            $trxRef = $request->query('trxRef');
            
            if (!$trxRef) {
                throw new Exception('Transaction reference not found in webhook');
            }
            
            // Verify the webhook signature if needed
            // $this->verifyWebhookSignature($request);
            
            // Process the webhook
            $status = $payload['data'][0]['payment_status'] ?? null;
            
            if ($status === 'COMPLETED') {
                $paymentProcess = PaymentProcess::where('id', $trxRef)->first();
                
                if ($paymentProcess) {
                    $this->updateOrder($paymentProcess);
                }
            }
            
            return response()->json(['status' => 'success']);
            
        } catch (Throwable $e) {
            Log::error('Selcom webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Update order after successful payment
     * 
     * @param PaymentProcess $paymentProcess
     * @return void
     */
    protected function updateOrder(PaymentProcess $paymentProcess): void
    {
        $data = $paymentProcess->data;
        
        if (isset($data['cart_id'])) {
            // Handle cart order
            $order = Order::find($data['cart_id']);
            
            if ($order) {
                $order->update([
                    'status' => Order::STATUS_PAID,
                    'transaction_id' => $paymentProcess->id
                ]);
                
                // Trigger order created event or other post-payment actions
                event(new OrderCreated($order));
            }
        } elseif (isset($data['parcel_id'])) {
            // Handle parcel order
            $parcelOrder = ParcelOrder::find($data['parcel_id']);
            
            if ($parcelOrder) {
                $parcelOrder->update([
                    'status' => ParcelOrder::STATUS_PAID,
                    'transaction_id' => $paymentProcess->id
                ]);
            }
        }
    }
    
    /**
     * Verify webhook signature
     * 
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    protected function verifyWebhookSignature(Request $request): bool
    {
        // Get the signature from the request header
        $signature = $request->header('X-Signature');
        
        if (!$signature) {
            throw new Exception('Missing signature header');
        }
        
        // Get the raw payload
        $payload = $request->getContent();
        
        // Get the Selcom API secret from config
        $apiSecret = config('selcom.api_secret');
        
        if (!$apiSecret) {
            throw new Exception('Selcom API secret is not configured');
        }
        
        // Generate the expected signature
        $expectedSignature = hash_hmac('sha256', $payload, $apiSecret);
        
        // Compare signatures
        if (!hash_equals($expectedSignature, $signature)) {
            throw new Exception('Invalid webhook signature');
        }
        
        return true;
    }
}
