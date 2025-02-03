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

    <!-- Display success/error messages -->
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
        <form class="row g-3" action="{{ isset($training) ? route('SuperAdmin.training.update', $training->id) : route('SuperAdmin.training.store') }}" method="POST">
            @csrf
            @if(isset($training))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif

            <!-- Training Code selection -->
            <div class="col-md-6">
                <label for="training_code" class="form-label">Training Code</label>
                <select name="training_code" id="training_code" class="form-select track-change @error('training_code') is-invalid @enderror">
                    <option disabled selected>Choose training Code</option>
                    <option value="1" {{ old('training_code', isset($training) && $training->training_code == 1 ? 'selected' : '') }}>Code 1</option>
                    <option value="2" {{ old('training_code', isset($training) && $training->training_code == 2 ? 'selected' : '') }}>Code 2</option>
                    <option value="3" {{ old('training_code', isset($training) && $training->training_code == 3 ? 'selected' : '') }}>Code 3</option>
                    <option value="4" {{ old('training_code', isset($training) && $training->training_code == 4 ? 'selected' : '') }}>Code 4</option>
                </select>
                @error('training_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Training Name -->
            <div class="col-md-6">
                <label for="training_name" class="form-label">Training Name</label>
                <input name="training_name" type="text" class="form-control track-change @error('training_name') is-invalid @enderror" placeholder="Training Name" value="{{ old('training_name', isset($training) ? $training->training_name : '') }}" required>
                @error('training_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mode of Delivery -->
            <div class="col-md-6">
                <label for="mode_of_delivery" class="form-label">Mode of Delivery</label>
                <input name="mode_of_delivery" type="text" class="form-control track-change @error('mode_of_delivery') is-invalid @enderror" placeholder="Mode of Delivery" value="{{ old('mode_of_delivery', isset($training) ? $training->mode_of_delivery : '') }}" required>
                @error('mode_of_delivery')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Training Period From -->
            <div class="col-md-6">
                <label for="training_period_from" class="form-label">Training Period From</label>
                <input name="training_period_from" type="date" class="form-control track-change @error('training_period_from') is-invalid @enderror" value="{{ old('training_period_from', isset($training) ? $training->training_period_from : '') }}" required>
                @error('training_period_from')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Training Period To -->
            <div class="col-md-6">
                <label for="training_period_to" class="form-label">Training Period To</label>
                <input name="training_period_to" type="date" class="form-control track-change @error('training_period_to') is-invalid @enderror" value="{{ old('training_period_to', isset($training) ? $training->training_period_to : '') }}" required>
                @error('training_period_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Total Training Hours -->
            <div class="col-md-6">
                <label for="total_training_hours" class="form-label">Total Training Hours</label>
                <input name="total_training_hours" type="number" class="form-control track-change @error('total_training_hours') is-invalid @enderror" placeholder="0" value="{{ old('total_training_hours', isset($training) ? $training->total_training_hours : '') }}" required>
                @error('total_training_hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Total Program Cost -->
            <div class="col-md-6">
                <label for="total_program_cost" class="form-label">Total Program Cost</label>
                <input name="total_program_cost" type="number" step="any" class="form-control track-change @error('total_program_cost') is-invalid @enderror" placeholder="0.00" value="{{ old('total_program_cost', isset($training) ? $training->total_program_cost : '') }}" required>
                @error('total_program_cost')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Awarding Institute -->
            <div class="col-md-6">
                <label for="name" class="form-label">Awarding Institute</label>
                <select name="name" id="name" class="form-select track-change @error('name') is-invalid @enderror" required>
                    <option selected disabled>Choose Institute...</option>
                    <option value="1" {{ old('name', isset($institutes) && $institutes->name == 1 ? 'selected' : '') }}>SLIIT</option>
                    <option value="2" {{ old('name', isset($institutes) && $institutes->name == 2 ? 'selected' : '') }}>NIBM</option>
                    <option value="3" {{ old('name', isset($institutes) && $institutes->name == 3 ? 'selected' : '') }}>ESOFT</option>
                    <option value="4" {{ old('name', isset($institutes) && $institutes->name == 4 ? 'selected' : '') }}>APIT</option>
                </select>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Course Type -->
            <div class="col-md-6">
                <label for="course_type" class="form-label">Course Type</label>
                <select name="course_type" id="course_type" class="form-select track-change @error('course_type') is-invalid @enderror" required>
                    <option selected disabled>Choose Type...</option>
                    <option value="1" {{ old('course_type', isset($institutes) && $institutes->course_type == 1 ? 'selected' : '') }}>Foreign</option>
                    <option value="2" {{ old('course_type', isset($institutes) && $institutes->course_type == 2 ? 'selected' : '') }}>Local</option>
                    <option value="3" {{ old('course_type', isset($institutes) && $institutes->course_type == 3 ? 'selected' : '') }}>Test 1</option>
                    <option value="4" {{ old('course_type', isset($institutes) && $institutes->course_type == 4 ? 'selected' : '') }}>Test 2</option>
                </select>
                @error('course_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Country Selection -->
            <div class="col-md-6">
                <label for="country" class="form-label">Country</label>
                <select name="country" id="country" class="form-control track-change @error('country') is-invalid @enderror" required disabled>
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

            <!-- Training Structure -->
            <div class="col-md-6">
                <label for="training_structure" class="form-label">Training Structure</label>
                <input name="training_structure" type="text" class="form-control track-change @error('training_structure') is-invalid @enderror" placeholder="Training Structure" value="{{ old('training_structure', isset($training) ? $training->training_structure : '') }}" required>
                @error('training_structure')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Expire Date -->
            <div class="col-md-6">
                <label for="exp_date" class="form-label">Expire Date</label>
                <input name="exp_date" type="date" class="form-control track-change @error('exp_date') is-invalid @enderror" value="{{ old('exp_date', isset($training) ? $training->exp_date : '') }}" required>
                @error('exp_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Category -->
            <div class="col-md-6">
                <label for="category" class="form-label">Category</label>
                <input name="category" type="text" class="form-control track-change @error('category') is-invalid @enderror" placeholder="Category" value="{{ old('category', isset($training) ? $training->category : '') }}" required>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Training Custodian -->
            <div class="col-md-6">
                <label for="training_custodian" class="form-label">Training Custodian</label>
                <input name="training_custodian" type="text" class="form-control track-change @error('training_custodian') is-invalid @enderror" placeholder="Training Custodian" value="{{ old('training_custodian', isset($training) ? $training->training_custodian : '') }}" required>
                @error('training_custodian')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Batch Size -->
            <div class="col-md-6">
                <label for="batch_size" class="form-label">Batch Size</label>
                <input name="batch_size" type="number" class="form-control track-change @error('batch_size') is-invalid @enderror" placeholder="Batch Size" value="{{ old('batch_size', isset($training) ? $training->batch_size : '') }}" required>
                @error('batch_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Division Selection -->
            <div class="col-md-6">
                <label for="division_id" class="form-label">Division</label>
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

            <!-- Section Selection (Conditional) -->
            <div class="col-md-6" id="sectionContainer" style="display: none;">
                <label for="section_id" class="form-label">Sections</label>
                <select name="section_id" id="section" class="form-select track-change @error('section_id') is-invalid @enderror" disabled>
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

            <!-- Other Comments -->
            <div class="col-md-12">
                <label for="other_comments" class="form-label">Other Comments</label>
                <textarea name="other_comments" class="form-control track-change @error('other_comments') is-invalid @enderror" placeholder="Other Comments" rows="3">{{ old('other_comments', isset($training) ? $training->other_comments : '') }}</textarea>
                @error('other_comments')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
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
        // Initialize Select2 for country dropdown
        $('#country').select2({
            placeholder: "Select a country",
            allowClear: true
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        //
        const divisionSelect = document.getElementById('division');
        const sectionContainer = document.getElementById('sectionContainer');
        const sectionSelect = document.getElementById('section');

        // Function to toggle the Section dropdown based on Division selection
        function toggleSectionDropdown() {
            if (divisionSelect.value == '2') {  // '2' corresponds to 'CATC'
                sectionContainer.style.display = 'block'; // Show the Section dropdown
                sectionSelect.disabled = false; // Enable the Section dropdown
            } else {
                sectionContainer.style.display = 'none'; // Hide the Section dropdown
                sectionSelect.disabled = true; // Disable the Section dropdown
            }
        }

        // Initial check in case the division is already selected
        toggleSectionDropdown();

        // Listen for changes on the Division dropdown
        divisionSelect.addEventListener('change', toggleSectionDropdown);

        //
        const courseTypeSelect = document.getElementById('course_type');
        const countryContainer = document.querySelector('[for="country"]').parentElement;  // Get the country container
        const countrySelect = document.getElementById('country');

        // Function to toggle the Country dropdown based on Course Type selection
        function toggleCountryDropdown() {
            if (courseTypeSelect.value == '1') {  // '1' corresponds to 'Foreign'
                countryContainer.style.display = 'block'; // Show the Country dropdown
                countrySelect.disabled = false; // Enable the Country dropdown
            } else {
                countryContainer.style.display = 'none'; // Hide the Country dropdown
                countrySelect.disabled = true; // Disable the Country dropdown
            }
        }

        // Initial check in case the course type is already selected
        toggleCountryDropdown();

        // Listen for changes on the Course Type dropdown
        courseTypeSelect.addEventListener('change', toggleCountryDropdown);
    });

</script>

@endsection
