<?php

namespace App\Helpers;

class LuhnHelper
{
    /**
     * Generate a Luhn-compliant account number
     *
     * @param int $length Length of the account number (12-16 digits)
     * @return string
     */
    public static function generateAccountNumber($length = 12)
    {
        if ($length < 12 || $length > 16) {
            throw new \InvalidArgumentException("Account number length must be between 12 and 16 digits.");
        }

        // Generate a random number of given length minus 1 (last digit will be the Luhn checksum)
        $number = '';
        for ($i = 0; $i < $length - 1; $i++) {
            $number .= mt_rand(0, 9);
        }

        // Calculate and append the Luhn check digit
        $number .= self::calculateLuhnCheckDigit($number);

        return $number;
    }

    /**
     * Validate a Luhn-compliant account number
     *
     * @param string $number
     * @return bool
     */
    public static function validateAccountNumber($number)
    {
        $digits = str_split($number);
        $sum = 0;
        $isEven = false;

        for ($i = count($digits) - 1; $i >= 0; $i--) {
            $digit = (int)$digits[$i];

            if ($isEven) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $isEven = !$isEven;
        }

        return ($sum % 10) === 0;
    }

    /**
     * Calculate the Luhn check digit for a given number
     *
     * @param string $number
     * @return int
     */
    private static function calculateLuhnCheckDigit($number)
    {
        $digits = str_split($number);
        $sum = 0;
        $isEven = true;

        for ($i = count($digits) - 1; $i >= 0; $i--) {
            $digit = (int)$digits[$i];

            if ($isEven) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $isEven = !$isEven;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit;
    }
}
