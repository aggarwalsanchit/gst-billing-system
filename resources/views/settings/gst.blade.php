@extends('layouts.app')

@section('title', 'GST Settings')
@section('page-title', 'GST Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-percent"></i> GST Rate</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.gst.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Current GST Rate</label>
                        <div class="input-group">
                            <input type="number" name="gst1" class="form-control" value="{{ $gst->gst1 ?? 5 }}" step="0.1" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">This rate applies to: IGST (for interstate), CGST + SGST (for Punjab)</small>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update GST Rate
                        </button>
                    </div>
                </form>

                <hr>

                <div class="mt-3">
                    <h6>How GST is Applied:</h6>
                    <ul>
                        <li><strong>Punjab Customers with GST:</strong> CGST ({{ ($gst->gst1 ?? 5) / 2 }}%) + SGST ({{ ($gst->gst1 ?? 5) / 2 }}%)</li>
                        <li><strong>Other States with GST:</strong> IGST ({{ $gst->gst1 ?? 5 }}%)</li>
                        <li><strong>Without GST:</strong> No tax applied</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection