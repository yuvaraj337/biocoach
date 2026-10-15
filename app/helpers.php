<?php

if (!function_exists('bn_number')) {
    /**
     * Convert digits to Bangla numerals if active locale is Bangla.
     */
    function bn_number(mixed $value, bool $force = false): string
    {
        $str = (string) $value;
        if ($force || app()->getLocale() === 'bn') {
            $digits = [
                '0' => '০',
                '1' => '১',
                '2' => '২',
                '3' => '৩',
                '4' => '৪',
                '5' => '৫',
                '6' => '৬',
                '7' => '৭',
                '8' => '৮',
                '9' => '৯',
            ];
            return strtr($str, $digits);
        }
        return $str;
    }
}

if (!function_exists('bdt')) {
    /**
     * Format money in Bangladeshi Taka (৳).
     *
     * @param float|int|string $amount
     * @param bool $isPaise (true if amount is in paise/poisha)
     */
    function bdt(mixed $amount, bool $isPaise = false): string
    {
        $num = (float) $amount;
        if ($isPaise) {
            $num = $num / 100;
        }

        $formatted = number_format($num);
        if (app()->getLocale() === 'bn') {
            return '৳' . bn_number($formatted, true);
        }

        return '৳' . $formatted;
    }
}

if (!function_exists('is_bangla')) {
    /**
     * Check if active locale is Bangla.
     */
    function is_bangla(): bool
    {
        return app()->getLocale() === 'bn';
    }
}

if (!function_exists('bangla_date')) {
    /**
     * Format date with Bangla month and digits if locale is bn.
     */
    function bangla_date(mixed $date, string $format = 'd F Y'): string
    {
        if (empty($date)) {
            return '';
        }

        $carbon = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        $formatted = $carbon->format($format);

        if (app()->getLocale() === 'bn') {
            $months = [
                'January' => 'জানুয়ারি',
                'February' => 'ফেব্রুয়ারি',
                'March' => 'মার্চ',
                'April' => 'এপ্রিল',
                'May' => 'মে',
                'June' => 'জুন',
                'July' => 'জুলাই',
                'August' => 'আগস্ট',
                'September' => 'সেপ্টেম্বর',
                'October' => 'অক্টোবর',
                'November' => 'নভেম্বর',
                'December' => 'ডিসেম্বর',
                'Jan' => 'জানু',
                'Feb' => 'ফেব্রু',
                'Mar' => 'মার্চ',
                'Apr' => 'এপ্রিল',
                'May' => 'মে',
                'Jun' => 'জুন',
                'Jul' => 'জুলাই',
                'Aug' => 'আগস্ট',
                'Sep' => 'সেপ্টে',
                'Oct' => 'অক্টো',
                'Nov' => 'নভে',
                'Dec' => 'ডিসে',
            ];
            $formatted = strtr($formatted, $months);
            return bn_number($formatted, true);
        }

        return $formatted;
    }
}
