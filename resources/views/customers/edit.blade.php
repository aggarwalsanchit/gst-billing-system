@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-edit"></i> Edit Customer</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer->customer_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if(isset($bill_id))
                        <input type="hidden" name="bill_id" value="{{ $bill_id }}">
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ $customer->address }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="{{ $customer->state }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst" class="form-control" value="{{ $customer->gstnumber }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="panno" class="form-control" value="{{ $customer->panno }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="adharno" class="form-control" value="{{ $customer->adharno }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection