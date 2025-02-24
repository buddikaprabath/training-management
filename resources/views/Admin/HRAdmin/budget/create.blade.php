@extends('Admin.HRAdmin.index')
@section('content')
 <div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($budgets) ? 'Edit Budget' : 'Create New Budget' }}</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('Admin.HRAdmin.budget.Detail') }}" class="text-white text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle w-75 h-75">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 8 8 12 12 16"></polyline>
                    <line x1="16" y1="12" x2="8" y2="12"></line>
                </svg>
            </a>
        </button>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card-body p-4 rounded-3 shadow-lg" style="background-color: #A8BDDB;">
        <form class="row g-3" action="{{ route('Admin.HRAdmin.budget.store') }}" method="POST">
            @csrf
            <!-- Divisions Selection -->
            <div class="col-md-6">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-select track-change @error('type') is-invalid @enderror" required>
                    <option value="" disabled selected>Choose Type</option>
                    <option value="Initial">Initial</option>
                    <option value="Transfer">Transfer</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        
            <div class="col-md-6">
                <label for="provide_type" class="form-label">Category</label>
                <select name="provide_type" id="provide_type" class="form-select track-change @error('provide_type') is-invalid @enderror" required>
                    <option value="" disabled selected>Choose Category</option>
                    <option value="Foreign">Foreign</option>
                    <option value="Local">Local</option>
                </select>
                @error('provide_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        
            <div class="col-md-6">
                <label for="amount" class="form-label">Amount</label>
                <input name="amount" type="number" class="form-control track-change @error('amount') is-invalid @enderror" required min="0">
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
        
    </div>
 </div>
@endsection
