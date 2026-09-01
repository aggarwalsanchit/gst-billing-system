@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5><i class="fas fa-file-invoice"></i> Total Bills</h5>
                <h2 class="mb-0">{{ $totalBills }}</h2>
                <small>Bills Created</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5><i class="fas fa-users"></i> Customers</h5>
                <h2 class="mb-0">{{ $totalCustomers }}</h2>
                <small>Total Customers</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5><i class="fas fa-boxes"></i> Products</h5>
                <h2 class="mb-0">{{ $totalProducts }}</h2>
                <small>Inventory Items</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5><i class="fas fa-rupee-sign"></i> Sales</h5>
                <h2 class="mb-0">₹{{ number_format($totalSales, 2) }}</h2>
                <small>Total Revenue</small>
            </div>
        </div>
    </div>
</div>

<!-- Last Bill & Quick Actions -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5><i class="fas fa-receipt"></i> Last Bill</h5>
                <h3 class="mb-2">#{{ $lastBillNumber }}</h3>
                <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create New Bill
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('customers.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Add Customer
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-box"></i> Add Product
                    </a>
                    <a href="{{ route('bills.index') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-list"></i> View All Bills
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bills -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-clock"></i> Recent Bills</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Bill No.</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBills as $bill)
                    <tr>
                        <td><strong>#{{ $bill->bill_id }}</strong></td>
                        <td>{{ $bill->customer->name ?? 'N/A' }}</td>
                        <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                        <td>₹{{ number_format($bill->grand_total, 2) }}</td>
                        <td>
                            <a href="{{ route('invoices.show', $bill->bill_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('bills.edit', $bill->bill_id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No bills found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection