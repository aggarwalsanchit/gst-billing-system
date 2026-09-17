@extends('layouts.app')

@section('title', 'Import Catalog PDF')
@section('page-title', 'Import Product Catalog')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-import"></i> Import Products from PDF</h5>
            </div>
            <div class="card-body">

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Upload the PDF catalog exported from this system (or one matching the same format).
                    Existing product numbers will be <strong>skipped</strong> — no duplicates.
                </div>

                <form action="{{ route('catalog.import-pdf') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">PDF File <span class="text-danger">*</span></label>
                        <input type="file" name="pdf" class="form-control" accept=".pdf" required>
                        <small class="text-muted">Max size: 20MB</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('catalog.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import Products
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection