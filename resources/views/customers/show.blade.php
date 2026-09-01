@extends('layouts.app')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user"></i> {{ $customer->name }}</h5>
            </div>
            <div class="card-body">
                <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                <p><strong>Address:</strong> {{ $customer->address }}</p>
                <p><strong>State:</strong> {{ $customer->state }}</p>
                <p><strong>GST:</strong> {{ $customer->gstnumber }}</p>
                <p><strong>PAN:</strong> {{ $customer->panno }}</p>
                <p><strong>Aadhar:</strong> {{ $customer->adharno }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice"></i> Bills</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bill No.</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->bills as $bill)
                            <tr>
                                <td>#{{ $bill->bill_id }}</td>
                                <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                                <td>{{ $bill->items->count() }}</td>
                                <td>₹{{ number_format($bill->grand_total, 2) }}</td>
                                <td>
                                    <a href="{{ route('invoices.show', $bill->bill_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No bills found for this customer</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection