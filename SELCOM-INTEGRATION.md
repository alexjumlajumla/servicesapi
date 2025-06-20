# Selcom Payment Gateway Integration

This document provides instructions for setting up and using the Selcom payment gateway integration in the JumlaJumla application.

## Prerequisites

- Laravel 9.x or higher
- PHP 8.0 or higher
- Composer
- Selcom merchant account

## Installation

1. **Publish the configuration file**

   Run the following command to publish the Selcom configuration file:

   ```bash
   php artisan vendor:publish --provider="App\Providers\SelcomServiceProvider" --tag=config
   ```

2. **Run migrations**

   Run the database migrations to create the necessary tables:

   ```bash
   php artisan migrate
   ```

3. **Configure environment variables**

   Add the following environment variables to your `.env` file:

   ```env
   # Selcom Payment Gateway Configuration
   SELCOM_VENDOR_ID=your_vendor_id
   SELCOM_API_KEY=your_api_key
   SELCOM_API_SECRET=your_api_secret
   SELCOM_IS_LIVE=false
   SELCOM_PREFIX=JJ
   SELCOM_REDIRECT_URL=
   SELCOM_CANCEL_URL=
   SELCOM_PAYMENT_EXPIRY=30
   SELCOM_HEADER_COLOR=#000000
   SELCOM_LINK_COLOR=#0000FF
   SELCOM_BUTTON_COLOR=#008000
   ```

   Replace `your_vendor_id`, `your_api_key`, and `your_api_secret` with your actual Selcom credentials.

## Usage

### Making a Payment

To process a payment using Selcom, make a POST request to the following endpoint:

```
POST /api/v1/selcom/process
```

**Request Body:**

```json
{
    "cart_id": 1,
    "total_price": 100.00,
    "currency_id": 1,
    "shop_id": 1
}
```

**Response:**

```json
{
    "status": true,
    "code": 200,
    "message": "Redirect to payment",
    "data": {
        "url": "https://apigw.selcommobile.com/v1/checkout/create-order/...",
        "process": {
            "id": "1-1621234567-abc123",
            "user_id": 1,
            "model_type": "App\\Models\\Cart",
            "model_id": 1,
            "data": {
                "url": "https://apigw.selcommobile.com/v1/checkout/create-order/...",
                "price": 100,
                "cart": {
                    "cart_id": 1,
                    "total_price": 100,
                    "currency_id": 1,
                    "shop_id": 1
                },
                "shop_id": 1,
                "payment_id": 1
            }
        }
    }
}
```

### Handling the Payment Result

After the payment is completed, the user will be redirected to the `SELCOM_REDIRECT_URL` with the following query parameters:

- `status`: The status of the payment (`success` or `error`)
- `trxRef`: The transaction reference
- `cart_id` or `parcel_id`: The ID of the cart or parcel

### Webhook

Selcom will send a webhook to the following endpoint when a payment is completed:

```
POST /api/v1/webhook/selcom/payment?trxRef={transaction_reference}
```

Make sure to configure the webhook URL in your Selcom merchant dashboard.

## Testing

1. Set `SELCOM_IS_LIVE=false` in your `.env` file for testing.
2. Use the test card details provided by Selcom.
3. Test different payment scenarios (success, failure, cancellation).

## Troubleshooting

### Common Issues

1. **Invalid API credentials**
   - Verify that your `SELCOM_VENDOR_ID`, `SELCOM_API_KEY`, and `SELCOM_API_SECRET` are correct.
   - Make sure your account is active and has the necessary permissions.

2. **Webhook not working**
   - Check that the webhook URL is correctly configured in your Selcom dashboard.
   - Verify that your server is accessible from the internet.
   - Check the Laravel logs for any errors.

3. **Payment not being processed**
   - Check the Laravel logs for any errors.
   - Verify that the payment method is active in the database.
   - Make sure the payment amount is within the allowed limits.

## Support

For any issues or questions, please contact the development team.
