<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $bill->bill_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 10px; background: #fff; }
        .invoice-page { page-break-after: always; padding: 10px; max-width: 1000px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; }
        .border { border: 1px solid #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .p-1 { padding: 4px 5px; }
        .p-2 { padding: 8px; }
        .mt-1 { margin-top: 5px; }
        .mb-1 { margin-bottom: 5px; }
        .bg-gray { background: #f0f0f0; }
        .page-break { page-break-after: always; }
        .continued { text-align: center; font-style: italic; font-size: 11px; border: 1px dashed #000; padding: 3px; margin-top: 5px; }
        .header-image { max-height: 100px; width: auto; display: block; margin: 0 auto; }
        @media print {
            body { padding: 0; }
            .invoice-page { padding: 5px; }
        }
    </style>
</head>
<body>
    @include('invoices.partials.invoice-content')
</body>
</html>