@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5><i class="fas fa-users"></i> Customer List</h5>
        </div>
        <div>
            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Customer
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>GST</th>
                        <th>State</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->customer_id }}</td>
                        <td><strong>{{ $customer->name }}</strong></td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ Str::limit($customer->address, 30) }}</td>
                        <td>{{ $customer->gstnumber ?: '-' }}</td>
                        <td>{{ $customer->state ?: '-' }}</td>
                        <td>
                            <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('customers.show', $customer->customer_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" onclick="confirmDelete('{{ route('customers.destroy', $customer->customer_id) }}')" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection