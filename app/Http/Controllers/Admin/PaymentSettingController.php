<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function edit()
    {
        $settings = PaymentSetting::allSettings();
        $sslcommerzConfigured = !empty(env('SSLCOMMERZ_STORE_ID')) && !empty(env('SSLCOMMERZ_STORE_PASSWORD'));

        return view('admin.settings.payment', compact('settings', 'sslcommerzConfigured'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            // Mobile Banking
            'bkash_number' => ['nullable', 'string', 'max:50'],
            'bkash_type' => ['nullable', 'string', 'max:30'],
            'nagad_number' => ['nullable', 'string', 'max:50'],
            'nagad_type' => ['nullable', 'string', 'max:30'],
            'rocket_number' => ['nullable', 'string', 'max:50'],
            // Bank Transfer
            'account_holder' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'branch_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:30'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'ifsc_code' => ['nullable', 'string', 'max:30'],
            'manual_upi_enabled' => ['nullable'],
            // SSLCommerz
            'sslcommerz_store_id' => ['nullable', 'string', 'max:100'],
            // Instructions
            'payment_instructions' => ['nullable', 'string', 'max:2000'],
            'payment_instructions_en' => ['nullable', 'string', 'max:2000'],
        ]);

        $before = PaymentSetting::allSettings();

        PaymentSetting::set('business_name', $validated['business_name']);

        // bKash
        PaymentSetting::set('bkash_number', $validated['bkash_number'] ?? '');
        PaymentSetting::set('bkash_type', $validated['bkash_type'] ?? 'Merchant');
        PaymentSetting::set('manual_bkash_enabled', $request->boolean('manual_bkash_enabled') ? '1' : '0');

        // Nagad
        PaymentSetting::set('nagad_number', $validated['nagad_number'] ?? '');
        PaymentSetting::set('nagad_type', $validated['nagad_type'] ?? 'Personal');
        PaymentSetting::set('manual_nagad_enabled', $request->boolean('manual_nagad_enabled') ? '1' : '0');

        // Rocket
        PaymentSetting::set('rocket_number', $validated['rocket_number'] ?? '');
        PaymentSetting::set('manual_rocket_enabled', $request->boolean('manual_rocket_enabled') ? '1' : '0');

        // Bank
        PaymentSetting::set('account_holder', $validated['account_holder'] ?? '');
        PaymentSetting::set('bank_name', $validated['bank_name'] ?? '');
        PaymentSetting::set('branch_name', $validated['branch_name'] ?? '');
        PaymentSetting::set('account_number', $validated['account_number'] ?? '');
        PaymentSetting::set('routing_number', $validated['routing_number'] ?? '');
        PaymentSetting::set('manual_bank_enabled', $request->boolean('manual_bank_enabled') ? '1' : '0');

        // SSLCommerz
        PaymentSetting::set('sslcommerz_enabled', $request->boolean('sslcommerz_enabled') ? '1' : '0');
        PaymentSetting::set('sslcommerz_sandbox', $request->boolean('sslcommerz_sandbox') ? '1' : '0');
        if (!empty($validated['sslcommerz_store_id'])) {
            PaymentSetting::set('sslcommerz_store_id', $validated['sslcommerz_store_id']);
        }

        // Instructions
        PaymentSetting::set('payment_instructions', $validated['payment_instructions'] ?? '');
        PaymentSetting::set('payment_instructions_en', $validated['payment_instructions_en'] ?? '');

        // Backward compatibility
        if ($request->filled('upi_id')) {
            PaymentSetting::set('upi_id', $request->input('upi_id'));
        } else {
            PaymentSetting::set('upi_id', $validated['bkash_number'] ?? '01712-345678');
        }

        if ($request->filled('ifsc_code')) {
            PaymentSetting::set('ifsc_code', $request->input('ifsc_code'));
        } else {
            PaymentSetting::set('ifsc_code', $validated['routing_number'] ?? '090271234');
        }

        if ($request->has('manual_upi_enabled')) {
            PaymentSetting::set('manual_upi_enabled', $request->boolean('manual_upi_enabled') ? '1' : '0');
        } else {
            PaymentSetting::set('manual_upi_enabled', $request->boolean('manual_bkash_enabled') ? '1' : '0');
        }

        $after = PaymentSetting::allSettings();

        AdminAuditLog::create([
            'admin_user_id' => $request->user()->id,
            'action' => 'payment_settings.updated',
            'subject_type' => PaymentSetting::class,
            'subject_id' => 1,
            'reason' => 'Updated Bangladesh payment methods (bKash, Nagad, Rocket, Bank, SSLCommerz).',
            'before_json' => $before,
            'after_json' => $after,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.payment-settings.edit')->with('success', 'Bangladesh payment settings updated successfully.');
    }
}
