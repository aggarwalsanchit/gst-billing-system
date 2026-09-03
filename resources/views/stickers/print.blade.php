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
            padding: 8mm 2mm;
            background: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* ===== STICKER GRID - FIXED ===== */
        .sticker-grid {
            display: grid !important;
            grid-template-columns: repeat(5, 38.1mm) !important;
            grid-template-rows: repeat(13, 21.2mm) !important;
            gap: 1.5mm 3mm !important;
            width: fit-content !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
            background: #fff !important;
        }
        
        /* ===== INDIVIDUAL STICKER - FIXED ===== */
        .sticker-item {
            width: 38.1mm !important;
            height: 21.2mm !important;
            min-width: 38.1mm !important;
            min-height: 21.2mm !important;
            max-width: 38.1mm !important;
            max-height: 21.2mm !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            font-size: 16px !important;
            font-weight: bold !important;
            line-height: 1.3 !important;
            word-break: break-word !important;
            background: #fff !important;
            font-family: 'Arial', 'Helvetica', sans-serif !important;
            padding: 2px 2px !important;
            overflow: hidden !important;
        }
        
        /* Text inside sticker - handles multi-line */
        .sticker-item .sticker-text {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            white-space: pre-wrap !important;
            word-wrap: break-word !important;
            line-height: 1.3 !important;
            padding: 0 2px !important;
        }
        
        .sticker-item.empty {
            background: #f9f9f9 !important;
            color: #ddd !important;
            font-weight: normal !important;
            font-size: 14px !important;
        }
        
        .sticker-item.empty .sticker-text {
            color: #ddd !important;
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
                width: 100% !important;
                min-height: 100% !important;
                max-height: 100% !important;
                margin: 0 !important;
                padding: 8mm 2mm !important;
                page-break-after: avoid !important;
                overflow: visible !important;
                display: block !important;
            }
            
            .sticker-grid {
                display: grid !important;
                grid-template-columns: repeat(5, 38.1mm) !important;
                grid-template-rows: repeat(13, 21.2mm) !important;
                gap: 1.5mm 3mm !important;
                width: fit-content !important;
                margin: 0 auto !important;
                padding: 0 !important;
                background: #fff !important;
            }
            
            .sticker-item {
                width: 38.1mm !important;
                height: 21.2mm !important;
                min-width: 38.1mm !important;
                min-height: 21.2mm !important;
                max-width: 38.1mm !important;
                max-height: 21.2mm !important;
                border: none !important;
                font-size: 16px !important;
                padding: 2px 2px !important;
                line-height: 1.3 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                background: #fff !important;
            }
            
            .sticker-item .sticker-text {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                white-space: pre-wrap !important;
                word-wrap: break-word !important;
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
        <strong>Font Size:</strong> 16px &nbsp;|&nbsp; 
        <strong>Spacing:</strong> 8mm Top/Bottom, 2mm Left/Right
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
                    <span class="sticker-text">{{ $text }}</span>
                @else
                    <span class="sticker-text">&nbsp;</span>
                @endif
            </div>
        @endfor
    </div>
</div>

</body>
</html>