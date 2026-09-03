<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sticker Labels</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: #fff;
            padding: 0;
            margin: 0;
            width: 100%;
        }
        
        /* ===== PAGE - A4 ===== */
        .page {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 0 auto;
            padding: 0;
            background: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* ===== STICKER GRID ===== */
        .sticker-grid {
            display: grid;
            grid-template-rows: repeat(13, 21.2mm);
            grid-template-columns: repeat(5, 38.1mm);
            gap: 0;
            width: 100%;
            height: calc(13 * 21.2mm);
            padding: 0;
            background: #fff;
            margin: 10.6mm auto;
        }
        
        /* ===== INDIVIDUAL STICKER - NO BORDER ===== */
        .sticker-item {
            width: 38.1mm;
            height: 21.2mm;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.3;
            word-break: break-word;
            background: #fff;
            font-family: 'Arial', 'Helvetica', sans-serif;
            white-space: pre-wrap;
            padding: 2px 3px;
            overflow: hidden;
        }
        
        .sticker-item.empty {
            background: #f9f9f9;
            color: #ddd;
            font-weight: normal;
            font-size: 10px;
        }
        
        /* ===== PRINT BUTTONS - SCREEN ONLY ===== */
        .no-print {
            text-align: center;
            padding: 15px 0;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .no-print .btn {
            padding: 10px 30px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            background: #007bff;
            color: #fff;
            border-radius: 4px;
            margin: 0 5px;
        }
        
        .no-print .btn:hover {
            background: #0056b3;
        }
        
        .no-print .btn-secondary {
            background: #6c757d;
        }
        
        .no-print .btn-secondary:hover {
            background: #5a6268;
        }
        
        .no-print .info {
            margin-top: 8px;
            color: #666;
            font-size: 13px;
        }
        
        .no-print .info strong {
            color: #333;
        }
        
        /* ===== PRINT STYLES ===== */
        @media print {
            * {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background: #fff !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page {
                width: 210mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: avoid !important;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
            }
            
            .sticker-grid {
                width: 100% !important;
                height: calc(13 * 21.2mm) !important;
                padding: 0 !important;
                gap: 0 !important;
                margin: 10.6mm auto !important;
            }
            
            .sticker-item {
                width: 38.1mm !important;
                height: 21.2mm !important;
                border: none !important;
                font-size: 12px !important;
                padding: 2px 3px !important;
                line-height: 1.3 !important;
            }
            
            .sticker-item.empty {
                background: #f9f9f9 !important;
                color: #ddd !important;
            }
        }
        
        @page {
            margin: 0 !important;
            padding: 0 !important;
            size: A4 portrait !important;
        }
    </style>
</head>
<body>

<!-- ===== PRINT BUTTONS ===== -->
<div class="no-print">
    <button class="btn" onclick="window.print()">
        <i class="fas fa-print"></i> Print Stickers
    </button>
    <button class="btn btn-secondary" onclick="window.location.href='{{ route('stickers.index') }}'">
        <i class="fas fa-arrow-left"></i> Back to Edit
    </button>
    <div class="info">
        <strong>Sticker Size:</strong> 38.1mm × 21.2mm &nbsp;|&nbsp; 
        <strong>Grid:</strong> 13 Rows × 5 Columns = 65 Stickers &nbsp;|&nbsp; 
        <strong>Page:</strong> A4 (210mm × 297mm) &nbsp;|&nbsp; 
        <strong>Border:</strong> None
    </div>
</div>

<!-- ===== A4 PAGE ===== -->
<div class="page">
    <div class="sticker-grid">
        @php
            $expandedStickers = [];
            foreach ($stickers as $sticker) {
                $qty = isset($sticker['qty']) ? intval($sticker['qty']) : 1;
                for ($i = 0; $i < $qty; $i++) {
                    if (count($expandedStickers) < 65) {
                        $expandedStickers[] = $sticker;
                    }
                }
            }
        @endphp

        @for($i = 0; $i < 65; $i++)
            @php
                $hasData = isset($expandedStickers[$i]);
                $text = $hasData ? trim($expandedStickers[$i]['text'] ?? '') : '';
                $hasContent = !empty($text);
            @endphp
            <div class="sticker-item {{ $hasContent ? '' : 'empty' }}">
                @if($hasContent)
                    {{ $text }}
                @else
                    &nbsp;
                @endif
            </div>
        @endfor
    </div>
</div>

</body>
</html>