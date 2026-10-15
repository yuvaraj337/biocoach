<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(15);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:vouchers,code'],
            'discount_type' => ['required', 'string', 'in:percent,fixed_paise'],
            'discount_value' => ['required', 'numeric', 'min:1'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if ($validated['discount_type'] === 'fixed_paise') {
            $validated['discount_value'] = (int) ($validated['discount_value'] * 100);
        }

        $voucher = Voucher::create($validated);

        AdminAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action' => 'voucher_created',
            'subject_type' => Voucher::class,
            'subject_id' => $voucher->id,
            'reason' => "Voucher '{$voucher->code}' created with discount {$voucher->discount_value} ({$voucher->discount_type})",
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', "Voucher '{$voucher->code}' created successfully.");
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:vouchers,code,' . $voucher->id],
            'discount_type' => ['required', 'string', 'in:percent,fixed_paise'],
            'discount_value' => ['required', 'numeric', 'min:1'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if ($validated['discount_type'] === 'fixed_paise') {
            $validated['discount_value'] = (int) ($validated['discount_value'] * 100);
        }

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')->with('success', "Voucher '{$voucher->code}' updated.");
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted successfully.');
    }
}
