<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function allSettings(): array
    {
        return [
            'business_name' => static::get('business_name', "Jerin's Coaching"),
            // Bangladesh Mobile Banking
            'bkash_number' => static::get('bkash_number', '01712-345678'),
            'bkash_type' => static::get('bkash_type', 'Merchant'),
            'nagad_number' => static::get('nagad_number', '01812-345678'),
            'nagad_type' => static::get('nagad_type', 'Personal'),
            'rocket_number' => static::get('rocket_number', '01912-345678-9'),
            'manual_bkash_enabled' => static::get('manual_bkash_enabled', '1') === '1',
            'manual_nagad_enabled' => static::get('manual_nagad_enabled', '1') === '1',
            'manual_rocket_enabled' => static::get('manual_rocket_enabled', '1') === '1',

            // Bangladesh Bank Account
            'account_holder' => static::get('account_holder', 'Dr. Tanjina Jerin'),
            'bank_name' => static::get('bank_name', 'Dutch-Bangla Bank PLC'),
            'branch_name' => static::get('branch_name', 'Dhanmondi Branch, Dhaka'),
            'account_number' => static::get('account_number', '115.101.0029345'),
            'routing_number' => static::get('routing_number', '090271234'),
            'manual_bank_enabled' => static::get('manual_bank_enabled', '1') === '1',

            // SSLCommerz Gateway
            'sslcommerz_enabled' => static::get('sslcommerz_enabled', '1') === '1',
            'sslcommerz_store_id' => static::get('sslcommerz_store_id', env('SSLCOMMERZ_STORE_ID', 'testbox')),
            'sslcommerz_sandbox' => static::get('sslcommerz_sandbox', env('SSLCOMMERZ_SANDBOX', true) ? '1' : '0') === '1',

            // Instructions
            'payment_instructions' => static::get('payment_instructions', "১. বিকাশ / নগদ / রকেটের মাধ্যমে নির্ধারিত ফি সেন্ড মানি বা পেমেন্ট করুন।\n২. ট্রানজেকশন আইডি (TrxID) ও যে নম্বর থেকে পাঠিয়েছেন তা নিচে লিখুন।\n৩. পেমেন্টের স্ক্রিনশট বা রসিদের ছবি আপলোড করে 'যাচাইয়ের জন্য জমা দিন' বাটনে ক্লিক করুন।\n৪. অ্যাডমিন ভেরিফিকেশনের পর কোর্সটি তাৎক্ষণিক সক্রিয় হয়ে যাবে।"),
            'payment_instructions_en' => static::get('payment_instructions_en', "1. Transfer course fee via bKash, Nagad, Rocket or Bank.\n2. Note down your Transaction ID (TrxID) and sender mobile number.\n3. Upload your payment screenshot or slip.\n4. Click 'Submit for Verification' to activate your course."),

            // Backward compatibility
            'upi_id' => static::get('upi_id', 'drjerin@bkash'),
            'ifsc_code' => static::get('ifsc_code', static::get('routing_number', '090271234')),
            'manual_upi_enabled' => static::get('manual_upi_enabled', '1') === '1',
            'gateway_enabled' => static::get('gateway_enabled', '1') === '1',
        ];
    }
}
