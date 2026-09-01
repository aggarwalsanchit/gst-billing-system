<?php

namespace App\Http\Controllers;

use App\Models\GstSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GstSettingController extends Controller
{
    /**
     * Show GST settings.
     */
    public function index()
    {
        $gst = GstSetting::first();
        return view('settings.gst', compact('gst'));
    }

    /**
     * Update GST settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gst1' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gst = GstSetting::first();
        if ($gst) {
            $gst->update(['gst1' => $request->gst1]);
        } else {
            GstSetting::create(['gst1' => $request->gst1]);
        }

        return redirect()->route('settings.gst')
            ->with('success', 'GST rate updated successfully!');
    }
}