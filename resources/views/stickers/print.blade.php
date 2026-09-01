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
            font-family: 'Courier New', monospace;
            background: #fff;
            padding: 0;
            margin: 0;
            width: 100%;
            min-height: 100vh;
        }
        
        .page {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        
        /* ===== 13 ROWS x 5 COLUMNS - FULL A4 ===== */
        .sticker-grid {
            display: grid;
            grid-template-rows: repeat(13, 1fr);
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5px;
            width: 100%;
            height: 100vh;
            min-height: 100vh;
            background: #fff;
            padding: 2px;
        }
        
        .sticker-item {
            border: 1px solid #000;
            padding: 2px 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 10px;
            line-height: 1.3;
            word-break: break-word;
            background: #fff;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            white-space: pre-wrap;
            overflow: hidden;
        }
        
        .sticker-item.empty {
            background: #f9f9f9;
            color: #ddd;
            font-weight: normal;
        }
        
        /* ===== HEADER & FOOTER - ONLY FOR SCREEN ===== */
        .sticker-header {
            display: none;
        }
        
        .sticker-footer {
            display: none;
        }
        
        /* ===== PRINT BUTTONS - SCREEN ONLY ===== */
        .no-print {
            text-align: center;
            padding: 15px 0;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
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
        
        /* ===== PRINT STYLES ===== */
        @media print {
            * {
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }
            
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: #fff !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                min-height: 100vh !important;
                page-break-after: avoid !important;
            }
            
            .sticker-grid {
                width: 100% !important;
                height: 100vh !important;
                min-height: 100vh !important;
                gap: 1px !important;
                padding: 1px !important;
                border: none !important;
            }
            
            .sticker-item {
                border: 1px solid #000 !important;
                font-size: 9px !important;
                padding: 2px 2px !important;
                min-height: auto !important;
            }
            
            .sticker-item.empty {
                background: #f9f9f9 !important;
                border-color: #ccc !important;
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

<div class="no-print">
    <button class="btn" onclick="window.print()">
        <i class="fas fa-print"></i> Print Stickers
    </button>
    <button class="btn btn-secondary" onclick="window.location.href='{{ route('stickers.index') }}'">
        <i class="fas fa-arrow-left"></i> Back to Edit
    </button>
    <p style="margin-top: 8px; color: #666; font-size: 12px;">
        <strong>Note:</strong> Print will use full A4 page with NO margins.
    </p>
</div>

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