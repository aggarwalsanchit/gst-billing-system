<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceSettingController extends Controller
{
    /**
     * Display invoice settings.
     */
    public function index()
    {
        $settings = InvoiceSetting::getSettings();
        return view('settings.invoice', compact('settings'));
    }

    /**
     * Update invoice settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_gst' => 'nullable|string|max:100',
            'company_phone' => 'nullable|string|max:50',
            'company_phone2' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:100',
            'bank_ifsc' => 'nullable|string|max:50',
            'bank_branch' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'default_gst_rate' => 'nullable|numeric|min:0|max:100',
            'invoice_notes' => 'nullable|string',
            'terms' => 'nullable|array',
            'terms.*' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $settings = InvoiceSetting::getSettings();

        $settings->update([
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_gst' => $request->company_gst,
            'company_phone' => $request->company_phone,
            'company_phone2' => $request->company_phone2,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'bank_ifsc' => $request->bank_ifsc,
            'bank_branch' => $request->bank_branch,
            'footer_text' => $request->footer_text,
            'default_gst_rate' => $request->default_gst_rate ?? 5.00,
            'invoice_notes' => $request->invoice_notes,
            'terms_list' => $request->terms ? array_filter($request->terms) : null
        ]);

        return redirect()->route('settings.invoice')
            ->with('success', 'Invoice settings updated successfully!');
    }

    /**
     * Reset to default settings.
     */
    public function reset()
    {
        $settings = InvoiceSetting::getSettings();
        
        // Delete current settings
        $settings->delete();
        
        // Create default
        InvoiceSetting::createDefault();

        return redirect()->route('settings.invoice')
            ->with('success', 'Invoice settings reset to default!');
    }
}