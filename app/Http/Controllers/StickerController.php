<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StickerController extends Controller
{
    public function index()
    {
        return view('stickers.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'stickers' => 'required|array',
            'stickers.*.text' => 'nullable|string|max:500',
            'stickers.*.qty' => 'nullable|integer|min:1|max:65',
        ]);

        $stickers = $request->input('stickers');
        
        return redirect()->route('stickers.print')->with('stickers', $stickers);
    }

    public function print()
    {
        $stickers = session('stickers', []);
        return view('stickers.print', compact('stickers'));
    }

    /**
     * Export stickers to PDF (Optional - for PDF download)
     */
    public function exportPdf()
    {
        $stickers = session('stickers', []);
        
        $pdf = Pdf::loadView('stickers.print', compact('stickers'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'Courier New',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);
        
        return $pdf->download('stickers.pdf');
    }
}