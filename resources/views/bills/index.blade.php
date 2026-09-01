@extends('layouts.app')

@section('title', 'All Bills')
@section('page-title', 'All Bills')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-file-invoice"></i> Bill List</h5>
        <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Bill
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Bill No.</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bills as $bill)
                    <tr>
                        <td><strong>#{{ $bill->bill_id }}</strong></td>
                        <td>{{ $bill->customer->name ?? 'N/A' }}</td>
                        <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                        <td>{{ $bill->items->count() }}</td>
                        <td>₹{{ number_format($bill->grand_total, 2) }}</td>
                        <td>
                            <a href="{{ route('invoices.show', $bill->bill_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('bills.edit', $bill->bill_id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            {{-- Use a form for DELETE instead of an <a> tag --}}
                            <form action="{{ route('bills.destroy', $bill->bill_id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Delete this bill? This action cannot be undone!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection