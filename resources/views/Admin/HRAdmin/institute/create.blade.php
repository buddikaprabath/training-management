@extends('Admin.HRAdmin.index')
@section('content')
 <div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($institute) ? 'Edit institute' : 'Create New institute' }}</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('Admin.HRAdmin.institute.Detail') }}" class="text-white text-decoration-none">
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
        <form class="row g-3" action="{{ isset($institute) ? route('Admin.HRAdmin.institute.update', $institute->id) : route('Admin.HRAdmin.institute.store') }}" method="POST">
            @csrf
            @if(isset($institute))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif
            <p class="fw-bold">{{ isset($institute) ? 'Edit institute' : 'institute info' }}</p>
            <!-- Institute Name -->
            <div class="col-md-6">
                <label for="name" class="form-label">Institute Name</label>
                <input name="name" type="text" class="form-control track-change @error('name') is-invalid @enderror" placeholder="Institute Name" value="{{ old('name', isset($institute) ? $institute->name : '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--Type selection-->
            <div class="col-md-6">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-select track-change @error('type') is-invalid @enderror" required>
                    <option selected disabled>Choose Type</option>
                    <option value="AASL" {{ old('type', isset($institute) && $institute->type == 1 ? 'selected' : '') }}>AASL</option>
                    <option value="Type 2" {{ old('type', isset($institute) && $institute->type == 2 ? 'selected' : '') }}>Type 2</option>
                    <option value="Type 3" {{ old('type', isset($institute) && $institute->type == 3 ? 'selected' : '') }}>Type 3</option>
                    <option value="Type 4" {{ old('type', isset($institute) && $institute->type == 4 ? 'selected' : '') }}>Type 4</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">{{ isset($institute) ? 'Update' : 'Save institute' }}</button>
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
