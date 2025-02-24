@extends('Admin.HRAdmin.index')
@section('content')
 <div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($trainer) ? 'Edit Trainer' : 'Create New Trainer' }}</p>
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
        <form class="row g-3" action="{{ isset($trainer) ? route('Admin.HRAdmin.trainer.update', $trainer->id) : route('Admin.HRAdmin.trainer.store') }}" method="POST">
            @csrf
            @if(isset($trainer))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif
            <p class="fw-bold">{{ isset($trainer) ? 'Edit Trainer' : 'Trainer info' }}</p>
            <!--training ID-->

            <!-- Hidden institute_id field for both create and edit -->
            @if(isset($trainer))
                <input type="hidden" name="institute_id" value="{{ $trainer->institute->id }}">
            @else
                <input type="hidden" name="institute_id" value="{{ $institute->id }}">
            @endif
            <!-- Trainer Name -->
            <div class="col-md-6">
                <label for="name" class="form-label">Trainer Name</label>
                <input name="name" type="text" class="form-control track-change @error('name') is-invalid @enderror" placeholder="Trainer Name" value="{{ old('name', isset($trainer) ? $trainer->name : '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--email-->
            <div class="col-md-6">
                <label for="email" class="form-label">Trainer Email</label>
                <input name="email" type="email" class="form-control track-change @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email', isset($trainer) ? $trainer->email : '') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--mobile-->
            <div class="col-md-6">
                <label for="mobile" class="form-label">mobile</label>
                <input name="mobile" type="number" class="form-control track-change @error('mobile') is-invalid @enderror" placeholder="Mobile" value="{{ old('mobile', isset($trainer) ? $trainer->mobile : '') }}" required>
                @error('mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">{{ isset($trainer) ? 'Update' : 'Save Trainer' }}</button>
            </div>
        </form>
    </div>
 </div>
@endsection
