@extends('SuperAdmin.index')
@section('content')
 <div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($training) ? 'Edit Training' : 'Create Training' }}</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('SuperAdmin.training.Detail') }}" class="text-white text-decoration-none">
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

    <div class="card-body p-4 bg-body rounded-md shadow-lg">
        <form class="row g-3" action="{{ isset($training) ? route('SuperAdmin.training.update', $training->id) : route('SuperAdmin.training.store') }}" method="POST">
            @csrf
            @if(isset($training))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif

            <div class="col-md-6">
                <label for="id" class="form-label">Unique Identifier</label>
                <input name="id" type="text" class="form-control track-change @error('id') is-invalid @enderror" placeholder="Unique Identifier" value="{{ old('id', isset($training) ? $training->id : '') }}" required>
                @error('id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="training_code" class="form-label">Training Code</label>
                <input name="training_code" type="text" class="form-control track-change @error('training_code') is-invalid @enderror" placeholder="Training Code" value="{{ old('training_code', isset($training) ? $training->training_code : '') }}" required>
                @error('training_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="training name" class="form-label">Training Name</label>
                <input name="training_name" type="text" class="form-control track-change @error('training_name') is-invalid @enderror" placeholder="Training Name" value="{{ old('training_name', isset($training) ? $training->training_name : '') }}" required>
                @error('training_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="Mode of delivery" class="form-label">Mode Of Delivery</label>
                <input name="mode_of_delivery" type="text" class="form-control track-change @error('mode_of_delivery') is-invalid @enderror" placeholder="Mode of Delivery" value="{{ old('mode_of_delivery', isset($training) ? $training->mode_of_delivery : '') }}" required>
                @error('training')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="training period from" class="form-label">Training Period From</label>
                <input name="training_period_from" type="date" class="form-control track-change @error('training_period_from') is-invalid @enderror" value="{{ old('training_period_from', isset($training) ? $training->training_period_from : '') }}" required>
                @error('training_period_from')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="training_period_to" class="form-label">Training Period To</label>
                <input name="training_period_to" type="date" class="form-control track-change @error('training_period_to') is-invalid @enderror" value="{{ old('training_period_to', isset($training) ? $training->training_period_to : '') }}" required>
                @error('training_period_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="total_training_hours" class="form-label">Total Training Hours</label>
                <input name="total_training_hours" type="text" class="form-control track-change @error('total_training_hours') is-invalid @enderror" placeholder="Total Training Hours" value="{{ old('total_training_hours', isset($training) ? $training->total_training_hours : '') }}" required>
                @error('total_training_hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="total_program_cost" class="form-label">Total Program Cost</label>
                <input name="total_program_cost" type="text" class="form-control track-change @error('total_program_cost') is-invalid @enderror" placeholder="Total Program Cost" value="{{ old('total_program_cost', isset($training) ? $training->total_program_cost : '') }}" required>
                @error('total_program_cost')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="course_type" class="form-label">Course Type</label>
                <input name="course_type" type="text" class="form-control track-change @error('course_type') is-invalid @enderror" placeholder="Course Type" value="{{ old('course_type', isset($training) ? $training->course_type : '') }}" required>
                @error('course_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--country selection-->
            <div class="col-md-6">
                <label for="country" class="form-label">Country</label>
                <select name="country" id="country" class="form-control track-change @error('country') is-invalid @enderror" required>
                    <option value="">Select a country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->name }}"
                            {{ old('country', isset($training) ? $training->country : '') == $country->name ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--divisions selection-->
            <div class="col-md-6">
                <label for="division" class="form-label">Divisions</label>
                <select name="division_id" id="division" class="form-select track-change @error('division_id') is-invalid @enderror" required>
                    <option selected disabled>Choose...</option>
                    <option value="1" {{ old('division_id', isset($training) && $training->division_id == 1 ? 'selected' : '') }}>HR</option>
                    <option value="2" {{ old('division_id', isset($training) && $training->division_id == 2 ? 'selected' : '') }}>CATC</option>
                    <option value="3" {{ old('division_id', isset($training) && $training->division_id == 3 ? 'selected' : '') }}>IT</option>
                    <option value="4" {{ old('division_id', isset($training) && $training->division_id == 4 ? 'selected' : '') }}>FINANCE</option>
                    <option value="5" {{ old('division_id', isset($training) && $training->division_id == 5 ? 'selected' : '') }}>SCM</option>
                    <option value="6" {{ old('division_id', isset($training) && $training->division_id == 6 ? 'selected' : '') }}>MARKETING</option>
                    <option value="7" {{ old('division_id', isset($training) && $training->division_id == 7 ? 'selected' : '') }}>SECURITY</option>
                </select>
                @error('division_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--sections selection-->
            <div class="col-md-6" id="sectionContainer" style="display: {{ isset($training) && $training->division_id == 2 ? 'block' : 'true' }};">
                <label for="section_id" class="form-label">Sections</label>
                <select name="section_id" id="section" class="form-select track-change @error('section_id') is-invalid @enderror" {{ isset($training) && $training->division_id != 2 ? 'enable' : '' }}>
                    <option disabled selected>Choose...</option>
                    <option value="1" {{ old('section_id', isset($training) && $training->section_id == 1 ? 'selected' : '') }}>WING 1</option>
                    <option value="2" {{ old('section_id', isset($training) && $training->section_id == 2 ? 'selected' : '') }}>WING 2</option>
                    <option value="3" {{ old('section_id', isset($training) && $training->section_id == 3 ? 'selected' : '') }}>WING 3</option>
                    <option value="4" {{ old('section_id', isset($training) && $training->section_id == 4 ? 'selected' : '') }}>WING 4</option>
                    <option value="5" {{ old('section_id', isset($training) && $training->section_id == 5 ? 'selected' : '') }}>WING 5</option>
                    <option value="6" {{ old('section_id', isset($training) && $training->section_id == 6 ? 'selected' : '') }}>WING 6</option>
                    <option value="7" {{ old('section_id', isset($training) && $training->section_id == 7 ? 'selected' : '') }}>WING 7</option>
                    <option value="8" {{ old('section_id', isset($training) && $training->section_id == 8 ? 'selected' : '') }}>WING 8</option>
                </select>
                @error('section_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary">{{ isset($training) ? 'Update' : 'Create' }}</button>
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
