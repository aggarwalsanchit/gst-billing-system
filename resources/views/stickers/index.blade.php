@extends('layouts.app')

@section('title', 'Sticker Label Generator')
@section('page-title', 'Sticker Label Generator')

@section('content')
<style>
    .sticker-builder {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }
    
    .sticker-list {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .sticker-item-row {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        padding: 8px 10px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 6px;
        border: 1px solid #e9ecef;
        transition: all 0.2s;
        flex-wrap: wrap;
    }
    
    .sticker-item-row:hover {
        background: #e9ecef;
        border-color: #007bff;
    }
    
    .sticker-item-row .index {
        font-size: 11px;
        color: #999;
        min-width: 28px;
        font-weight: bold;
        padding-top: 4px;
    }
    
    .sticker-item-row textarea {
        flex: 1;
        padding: 5px 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 12px;
        min-width: 100px;
        resize: vertical;
        font-family: Arial, sans-serif;
        min-height: 38px;
        max-height: 120px;
        line-height: 1.4;
    }
    
    .sticker-item-row .qty-input {
        width: 50px;
        padding: 5px 4px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 12px;
        text-align: center;
        background: #fff;
        margin-top: 2px;
    }
    
    .sticker-item-row .qty-label {
        font-size: 11px;
        color: #666;
        font-weight: 500;
        padding-top: 4px;
    }
    
    .sticker-item-row .btn-remove {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 18px;
        cursor: pointer;
        padding: 0 5px;
        transition: all 0.2s;
        line-height: 1;
        padding-top: 4px;
    }
    
    .sticker-item-row .btn-remove:hover {
        color: #bd2130;
        transform: scale(1.2);
    }
    
    .sticker-item-row .btn-remove:disabled {
        color: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    .sticker-item-row .row-controls {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    .sticker-controls {
        display: flex;
        gap: 8px;
        margin: 12px 0;
        flex-wrap: wrap;
    }
    
    .sticker-controls .btn {
        padding: 6px 16px;
        font-size: 13px;
    }
    
    .preview-container {
        border: 2px solid #333;
        border-radius: 8px;
        padding: 10px;
        background: #fff;
        position: sticky;
        top: 20px;
    }
    
    .preview-grid {
        display: grid;
        grid-template-rows: repeat(13, 1fr);
        grid-template-columns: repeat(5, 1fr);
        gap: 2px;
    }
    
    .preview-item {
        border: 1px solid #ddd;
        padding: 4px 2px;
        min-height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 9px;
        font-family: Arial, sans-serif;
        background: #fafafa;
        line-height: 1.2;
        word-break: break-word;
        font-weight: bold;
        color: #333;
        white-space: pre-wrap;
    }
    
    .preview-item.empty {
        background: #f5f5f5;
        color: #ccc;
        font-weight: normal;
    }
    
    .preview-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e9ecef;
        font-size: 13px;
    }
    
    .preview-stats .count {
        font-weight: bold;
    }
    
    .preview-stats .count.valid {
        color: #28a745;
    }
    
    .preview-stats .count.invalid {
        color: #dc3545;
    }
    
    .preview-stats .limit {
        color: #6c757d;
    }
    
    .sticker-count-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .sticker-count-badge.valid {
        background: #d4edda;
        color: #155724;
    }
    
    .sticker-count-badge.invalid {
        background: #f8d7da;
        color: #721c24;
    }
    
    .btn-add-more {
        width: 100%;
        padding: 8px;
        border: 2px dashed #007bff;
        background: #f8f9fa;
        border-radius: 6px;
        color: #007bff;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 5px;
        font-size: 13px;
    }
    
    .btn-add-more:hover {
        background: #e8f4fd;
        border-color: #0056b3;
        color: #0056b3;
    }
    
    .btn-add-more:disabled {
        border-color: #ccc;
        color: #ccc;
        cursor: not-allowed;
        background: #f5f5f5;
    }
    
    .preview-label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    
    .preview-label small {
        font-weight: normal;
        color: #999;
        font-size: 11px;
    }
    
    .total-stickers-info {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    
    .hint-text {
        font-size: 11px;
        color: #999;
        margin-top: 2px;
        font-style: italic;
    }
    
    .sticker-dimensions {
        font-size: 12px;
        color: #666;
        background: #f0f0f0;
        padding: 5px 12px;
        border-radius: 4px;
    }
    
    @media (max-width: 1200px) {
        .sticker-builder { grid-template-columns: 1fr; }
        .preview-container { position: static; }
    }
    
    @media (max-width: 768px) {
        .sticker-item-row textarea { min-width: 100%; order: 2; }
        .sticker-item-row .row-controls { order: 3; width: 100%; }
        .sticker-item-row .index { order: 1; }
        .preview-item { min-height: 35px; font-size: 7px; }
    }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-tags"></i> Sticker Label Generator</h5>
        <div>
            <span class="sticker-dimensions me-2">
                <i class="fas fa-ruler"></i> 38.1mm × 21.2mm
            </span>
            <span class="sticker-count-badge valid" id="countBadge">
                <span id="stickerCount">0</span> / 65 Stickers
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>13 rows × 5 columns = 65 stickers</strong> &nbsp;|&nbsp;
            Each sticker: <strong>38.1mm × 21.2mm</strong> &nbsp;|&nbsp;
            Press <kbd>Shift + Enter</kbd> to add new line in text.
        </div>

        <form action="{{ route('stickers.generate') }}" method="POST" id="stickerForm">
            @csrf

            <div class="sticker-builder">
                <!-- Left: Input Section -->
                <div>
                    <div class="sticker-controls">
                        <button type="button" class="btn btn-success" onclick="addSticker()">
                            <i class="fas fa-plus"></i> Add Row
                        </button>
                        <button type="button" class="btn btn-danger" onclick="clearAll()">
                            <i class="fas fa-trash"></i> Clear All
                        </button>
                        <button type="button" class="btn btn-warning" onclick="addSampleData()">
                            <i class="fas fa-file-import"></i> Load Sample
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print"></i> Generate & Print
                        </button>
                    </div>

                    <div class="sticker-list" id="stickerList">
                        <!-- Stickers will be added here -->
                    </div>

                    <button type="button" class="btn-add-more" id="addMoreBtn" onclick="addSticker()">
                        <i class="fas fa-plus-circle"></i> Add More Sticker Row
                    </button>
                </div>

                <!-- Right: Preview Section -->
                <div>
                    <div class="preview-container">
                        <div class="preview-label">
                            <i class="fas fa-eye"></i> Live Preview 
                            <small>(13 Rows × 5 Columns)</small>
                            <span class="total-stickers-info" id="totalStickersInfo">Total: 0 stickers</span>
                        </div>
                        <div class="preview-grid" id="previewGrid">
                            <!-- 65 preview items -->
                        </div>
                        <div class="preview-stats">
                            <span>
                                <span class="count valid" id="previewCount">0</span> / 65 used
                            </span>
                            <span class="limit" id="remainingSlots">Remaining: 65</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let stickers = [];
    let nextId = 0;

    function init() {
        addSticker();
        renderPreview();
    }

    function addSticker() {
        if (stickers.length >= 65) {
            alert('Maximum 65 sticker rows allowed!');
            return;
        }
        
        stickers.push({
            id: nextId++,
            text: '',
            qty: 1
        });
        
        renderStickerList();
        renderPreview();
        updateStats();
    }

    function removeSticker(id) {
        if (stickers.length <= 1) {
            alert('You need at least one sticker row!');
            return;
        }
        
        if (!confirm('Remove this sticker row?')) return;
        
        stickers = stickers.filter(s => s.id !== id);
        renderStickerList();
        renderPreview();
        updateStats();
    }

    function updateSticker(id, field, value) {
        const sticker = stickers.find(s => s.id === id);
        if (sticker) {
            sticker[field] = value;
            renderPreview();
            updateStats();
        }
    }

    function renderStickerList() {
        const container = document.getElementById('stickerList');
        container.innerHTML = '';
        
        stickers.forEach((sticker, index) => {
            const div = document.createElement('div');
            div.className = 'sticker-item-row';
            div.innerHTML = `
                <span class="index">#${index + 1}</span>
                <textarea 
                    placeholder="Enter sticker text...&#10;Use Shift+Enter for new line"
                    oninput="updateSticker(${sticker.id}, 'text', this.value)"
                    rows="2">${escapeHtml(sticker.text)}</textarea>
                <div class="row-controls">
                    <span class="qty-label">Qty:</span>
                    <input type="number" class="qty-input" 
                           value="${sticker.qty || 1}" 
                           placeholder="Qty" 
                           min="1"
                           max="65"
                           oninput="updateSticker(${sticker.id}, 'qty', this.value)">
                    <button type="button" class="btn-remove" 
                            onclick="removeSticker(${sticker.id})"
                            ${stickers.length <= 1 ? 'disabled' : ''}>
                        ×
                    </button>
                </div>
            `;
            container.appendChild(div);
        });
        
        document.getElementById('addMoreBtn').disabled = stickers.length >= 65;
    }

    function renderPreview() {
        const grid = document.getElementById('previewGrid');
        grid.innerHTML = '';
        
        let expandedStickers = [];
        stickers.forEach(sticker => {
            const qty = parseInt(sticker.qty) || 1;
            for (let i = 0; i < qty; i++) {
                if (expandedStickers.length < 65) {
                    expandedStickers.push(sticker);
                }
            }
        });
        
        for (let i = 0; i < 65; i++) {
            const div = document.createElement('div');
            
            if (i < expandedStickers.length) {
                const sticker = expandedStickers[i];
                const text = sticker.text || '';
                
                if (text) {
                    div.className = 'preview-item';
                    div.innerHTML = text.replace(/\n/g, '<br>');
                } else {
                    div.className = 'preview-item empty';
                    div.textContent = 'Empty';
                }
            } else {
                div.className = 'preview-item empty';
                div.textContent = 'Empty';
            }
            
            grid.appendChild(div);
        }
        
        document.getElementById('totalStickersInfo').textContent = 
            `Total: ${expandedStickers.length} stickers`;
        
        const remaining = 65 - expandedStickers.length;
        document.getElementById('remainingSlots').textContent = `Remaining: ${remaining}`;
        document.getElementById('previewCount').textContent = expandedStickers.length;
    }

    function updateStats() {
        let totalQty = 0;
        stickers.forEach(s => {
            totalQty += parseInt(s.qty) || 1;
        });
        
        document.getElementById('stickerCount').textContent = stickers.length;
        
        const badge = document.getElementById('countBadge');
        if (stickers.length <= 65) {
            badge.className = 'sticker-count-badge valid';
        } else {
            badge.className = 'sticker-count-badge invalid';
        }
    }

    function clearAll() {
        if (!confirm('Clear all stickers?')) return;
        stickers = [];
        nextId = 0;
        addSticker();
        renderStickerList();
        renderPreview();
        updateStats();
    }

    function addSampleData() {
        if (stickers.length > 1) {
            if (!confirm('This will replace all current stickers. Continue?')) return;
        }
        
        const sampleData = [
            { text: 'SHAWL NO.7424\nRs. 271/-', qty: 2 },
            { text: 'SHAWL NO.5700\nRs. 318/-', qty: 1 },
            { text: 'SHAWL NO.5452\nRs. 331/-', qty: 3 },
            { text: 'SHAWL NO.6443\nRs. 215/-', qty: 2 },
            { text: 'SHAWL NO.6431\nRs. 214/-', qty: 1 },
            { text: 'SHAWL NO.7383\nRs. 227/-', qty: 2 },
            { text: 'SHAWL NO.6384\nRs. 210/-', qty: 1 },
            { text: 'SHAWL NO.6402\nRs. 196/-', qty: 2 },
            { text: 'SHAWL NO.4404\nRs. 185/-', qty: 1 },
            { text: 'SHAWL NO.4361K\nRs. 176/-', qty: 2 },
            { text: 'SHAWL NO.4303\nRs. 122/-', qty: 1 },
            { text: 'SHAWL NO.4252\nRs. 105/-', qty: 2 },
            { text: 'SHAWL NO.1252\nRs. 107/-', qty: 1 },
            { text: 'SHAWL NO.1162\nRs. 68/-', qty: 2 },
            { text: 'SHAWL NO.1122\nRs. 51/-', qty: 1 },
            { text: 'SHAWL NO.2358\nRs. 157/-', qty: 2 },
            { text: 'SHAWL NO.2301\nRs. 122/-', qty: 1 },
            { text: 'SHAWL NO.2251\nRs. 113/-', qty: 2 },
            { text: 'SHAWL NO.2241\nRs. 71/-', qty: 1 },
            { text: 'SHAWL NO.2221\nRs. 110/-', qty: 2 },
            { text: 'SHAWL NO.2181\nRs. 101/-', qty: 1 },
            { text: 'SHAWL NO.2162\nRs. 82/-', qty: 2 },
            { text: 'SHAWL NO.3381\nRs. 208/-', qty: 1 },
            { text: 'SHAWL NO.3354\nRs. 171/-', qty: 2 },
            { text: 'SHAWL NO.3261\nRs. 114/-', qty: 1 },
            { text: 'SHAWL NO.3341\nRs. 171/-', qty: 2 },
            { text: 'SHAWL NO.3281\nRs. 136/-', qty: 1 },
            { text: 'SHAWL NO.3255\nRs. 119/-', qty: 2 },
            { text: 'SHAWL NO.3253\nRs. 123/-', qty: 1 },
            { text: 'SHAWL NO.3211\nRs. 101/-', qty: 2 },
            { text: 'SHAWL NO.3222\nRs. 85/-', qty: 1 },
            { text: 'SHAWL NO.3221\nRs. 74/-', qty: 2 },
            { text: 'SHAWL NO.3191\nRs. 68/-', qty: 1 },
            { text: 'SHAWL NO.3175A\nRs. 54/-', qty: 2 },
            { text: 'SHAWL NO.3131\nRs. 46/-', qty: 1 },
            { text: 'SHAWL NO.3122\nRs. 45/-', qty: 2 }
        ];
        
        stickers = [];
        nextId = 0;
        
        sampleData.forEach(item => {
            if (stickers.length < 65) {
                stickers.push({
                    id: nextId++,
                    text: item.text,
                    qty: item.qty || 1
                });
            }
        });
        
        renderStickerList();
        renderPreview();
        updateStats();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.getElementById('stickerForm').addEventListener('submit', function(e) {
        if (stickers.length === 0) {
            e.preventDefault();
            alert('Please add at least one sticker row!');
            return;
        }
        
        let totalQty = 0;
        stickers.forEach(s => {
            totalQty += parseInt(s.qty) || 1;
        });
        
        if (totalQty > 65) {
            e.preventDefault();
            alert(`Too many stickers! Total quantity (${totalQty}) exceeds 65. Please reduce quantities.`);
            return;
        }
        
        stickers.forEach((sticker, index) => {
            const inputText = document.createElement('input');
            inputText.type = 'hidden';
            inputText.name = `stickers[${index}][text]`;
            inputText.value = sticker.text || '';
            this.appendChild(inputText);
            
            const inputQty = document.createElement('input');
            inputQty.type = 'hidden';
            inputQty.name = `stickers[${index}][qty]`;
            inputQty.value = sticker.qty || 1;
            this.appendChild(inputQty);
        });
    });

    document.addEventListener('DOMContentLoaded', init);
</script>
@endpush
@endsection