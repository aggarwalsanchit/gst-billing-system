@extends('layouts.app')

@section('title', 'Invoice #' . $bill->bill_id)
@section('page-title', 'Invoice #' . $bill->bill_id)

@section('content')
<div class="invoice-container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center no-print">
            <h5><i class="fas fa-file-invoice"></i> Tax Invoice</h5>
            <div>
                <a href="#" onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fas fa-print"></i> Print
                </a>
                <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Bills
                </a>
                <a href="{{ route('bills.edit', $bill->bill_id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit Bill
                </a>
            </div>
        </div>
        <div class="card-body p-0" style="background: #fff;">
            @include('invoices.partials.invoice-content')
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border: none !important; }
        .card-header { display: none !important; }
        .card-body { padding: 0 !important; }
        .invoice-container { margin: 0 !important; padding: 0 !important; }
    }
</style>
@endpush