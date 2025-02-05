@extends('SuperAdmin.index')
@section('content')
 <div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($budgets) ? 'Edit Budget' : 'Create New Budget' }}</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('SuperAdmin.budget.Detail') }}" class="text-white text-decoration-none">
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
        <form class="row g-3" action="{{ isset($budgets) ? route('SuperAdmin.budget.update', $budgets->id) : route('SuperAdmin.budget.store') }}" method="POST">
            @csrf
            @if(isset($training))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif
            <p class="fw-bold">{{ isset($budgets) ? 'Edit Budget' : 'Budget info' }}</p>
            <!--divisions selection-->
            <div class="col-md-6">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-select track-change @error('type') is-invalid @enderror" required>
                    <option selected disabled>Choose Type</option>
                    <option value="1" {{ old('type', isset($budgets) && $budgets->type == 1 ? 'selected' : '') }}>Initial</option>
                    <option value="2" {{ old('type', isset($budgets) && $budgets->type == 2 ? 'selected' : '') }}>Transfer</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="provide_type" class="form-label">Category</label>
                <select name="provide_type" id="provide_type" class="form-select track-change @error('provide_type') is-invalid @enderror" required>
                    <option selected disabled>Choose Category</option>
                    <option value="1" {{ old('provide_type', isset($budgets) && $budgets->provide_type == 1 ? 'selected' : '') }}>Foreign</option>
                    <option value="2" {{ old('provide_type', isset($budgets) && $budgets->provide_type == 2 ? 'selected' : '') }}>Local</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="division" class="form-label">Divisions</label>
                <select name="division_id" id="division" class="form-select track-change @error('division_id') is-invalid @enderror" required>
                    <option selected disabled>Choose...</option>
                    <option value="1" {{ old('division_id', isset($user) && $user->division_id == 1 ? 'selected' : '') }}>HR</option>
                    <option value="2" {{ old('division_id', isset($user) && $user->division_id == 2 ? 'selected' : '') }}>CATC</option>
                    <option value="3" {{ old('division_id', isset($user) && $user->division_id == 3 ? 'selected' : '') }}>IT</option>
                    <option value="4" {{ old('division_id', isset($user) && $user->division_id == 4 ? 'selected' : '') }}>FINANCE</option>
                    <option value="5" {{ old('division_id', isset($user) && $user->division_id == 5 ? 'selected' : '') }}>SCM</option>
                    <option value="6" {{ old('division_id', isset($user) && $user->division_id == 6 ? 'selected' : '') }}>MARKETING</option>
                    <option value="7" {{ old('division_id', isset($user) && $user->division_id == 7 ? 'selected' : '') }}>SECURITY</option>
                </select>
                @error('division_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="amount" class="form-label">Amount</label>
                <input name="amount" type="text" class="form-control track-change @error('amount') is-invalid @enderror" placeholder="amount" value="{{ old('amount', isset($budgets) ? $budgets->amount : '') }}" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">{{ isset($budgets) ? 'Update' : 'Save Budget' }}</button>
            </div>
        </form>
    </div>
 </div>

 <!-- Include jQuery and Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#country').select2({
            placeholder: "Select a country",
            allowClear: true
        });
    });
</script>
@endsection
