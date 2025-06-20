<?php

namespace App\Library\Selcom\Traits;

trait HandlesCheckout
{
    /**
     * Generate a card checkout URL
     *
     * @param array $data
     * @return array
     */
    public function cardCheckoutUrl(array $data): array
    {
        $required = [
            'amount', 'currency', 'transaction_id', 'name', 'email', 'phone'
        ];

        $this->validateRequired($data, $required);

        $payload = [
            'vendor' => $this->vendor,
            'order_id' => $data['transaction_id'],
            'buyer_name' => $data['name'],
            'buyer_email' => $data['email'],
            'buyer_phone' => $data['phone'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'redirect_url' => $this->redirectUrl(),
            'cancel_url' => $this->cancelUrl(),
            'webhook' => $this->webhookUrl(),
            'no_of_items' => $data['items'] ?? 1,
            'header_color' => $this->paymentGatewayColors()['header'] ?? '#000000',
            'link_color' => $this->paymentGatewayColors()['link'] ?? '#0000FF',
            'button_color' => $this->paymentGatewayColors()['button'] ?? '#008000',
            'expiry_time' => $this->paymentExpiry(),
        ];

        if (isset($data['address'])) {
            $payload['buyer_address'] = $data['address'];
        }

        if (isset($data['postcode'])) {
            $payload['buyer_postcode'] = $data['postcode'];
        }

        if (isset($data['user_id'])) {
            $payload['buyer_user_id'] = $data['user_id'];
        }

        $response = $this->makeRequest('checkout/create-order', 'POST', $payload);

        return $response->json();
    }

    /**
     * Check order status
     *
     * @param string $orderId
     * @return array
     */
    public function orderStatus(string $orderId): array
    {
        $payload = [
            'order_id' => $orderId,
        ];

        $response = $this->makeRequest('checkout/order-status', 'GET', $payload);

        return $response->json();
    }
}
