<?php

namespace App\Library\Selcom\Traits;

use App\Library\Selcom\Exceptions\MissingDataException;

trait ValidatesData
{
    /**
     * Validate required fields
     *
     * @param array $data
     * @param array $required
     * @throws MissingDataException
     */
    protected function validateRequired(array $data, array $required): void
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new MissingDataException("The $field field is required.");
            }
        }
    }

    /**
     * Validate amount
     *
     * @param float $amount
     * @throws \InvalidArgumentException
     */
    protected function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than 0.');
        }
    }

    /**
     * Validate currency
     *
     * @param string $currency
     * @param array $allowedCurrencies
     * @throws \InvalidArgumentException
     */
    protected function validateCurrency(string $currency, array $allowedCurrencies = ['TZS', 'USD', 'KES', 'UGX']): void
    {
        if (!in_array(strtoupper($currency), $allowedCurrencies)) {
            throw new \InvalidArgumentException(
                'Invalid currency. Must be one of: ' . implode(', ', $allowedCurrencies)
            );
        }
    }

    /**
     * Validate phone number
     *
     * @param string $phone
     * @throws \InvalidArgumentException
     */
    protected function validatePhone(string $phone): void
    {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Check if the phone number is valid (minimum 9 digits, maximum 15)
        if (strlen($phone) < 9 || strlen($phone) > 15) {
            throw new \InvalidArgumentException('Invalid phone number format.');
        }
    }

    /**
     * Validate email
     *
     * @param string $email
     * @throws \InvalidArgumentException
     */
    protected function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address.');
        }
    }
}
