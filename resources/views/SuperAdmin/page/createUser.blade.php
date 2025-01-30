@extends('SuperAdmin.index')
@section('content')
 <div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($user) ? 'Edit User' : 'Create User' }}</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('SuperAdmin.page.UserDetails') }}" class="text-white text-decoration-none">
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
        <form class="row g-3" action="{{ isset($user) ? route('SuperAdmin.page.user.update', $user->id) : route('SuperAdmin.page.user.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif
            
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input name="name" type="text" class="form-control track-change @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">User Name</label>
                <input name="username" type="text" class="form-control track-change @error('username') is-invalid @enderror" placeholder="UserName" value="{{ old('username', isset($user) ? $user->username : '') }}" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Email</label>
                <input name="email" type="email" class="form-control track-change @error('email') is-invalid @enderror" id="inputEmail4" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input name="password" type="password" class="form-control track-change @error('password') is-invalid @enderror" id="password" {{ isset($user) ? '' : 'required' }}>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control track-change @error('password_confirmation') is-invalid @enderror" id="confirmPassword" name="password_confirmation" {{ isset($user) ? '' : 'required' }}>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small id="passwordError" class="text-danger"></small>
            </div>
            <div class="col-md-6">
                <label for="role" class="form-label">User Role</label>
                <select name="role" class="form-select track-change @error('role') is-invalid @enderror" required>
                    <option selected disabled>Choose...</option>
                    <option value="superadmin" {{ old('role', isset($user) && $user->role == 'superadmin' ? 'selected' : '') }}>Super Admin</option>
                    <option value="hradmin" {{ old('role', isset($user) && $user->role == 'hradmin' ? 'selected' : '') }}>HR Admin</option>
                    <option value="catcadmin" {{ old('role', isset($user) && $user->role == 'catcadmin' ? 'selected' : '') }}>CATC Admin</option>
                    <option value="user" {{ old('role', isset($user) && $user->role == 'user' ? 'selected' : '') }}>User</option>
                </select>
                @error('role')
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
            
            <div class="col-md-6" id="sectionContainer" style="display: {{ isset($user) && $user->division_id == 2 ? 'block' : 'none' }};">
                <label for="section_id" class="form-label">Sections</label>
                <select name="section_id" id="section" class="form-select track-change @error('section_id') is-invalid @enderror" {{ isset($user) && $user->division_id != 2 ? 'disabled' : '' }}>
                    <option disabled selected>Choose...</option>
                    <option value="1" {{ old('section_id', isset($user) && $user->section_id == 1 ? 'selected' : '') }}>WING 1</option>
                    <option value="2" {{ old('section_id', isset($user) && $user->section_id == 2 ? 'selected' : '') }}>WING 2</option>
                    <option value="3" {{ old('section_id', isset($user) && $user->section_id == 3 ? 'selected' : '') }}>WING 3</option>
                    <option value="4" {{ old('section_id', isset($user) && $user->section_id == 4 ? 'selected' : '') }}>WING 4</option>
                    <option value="5" {{ old('section_id', isset($user) && $user->section_id == 5 ? 'selected' : '') }}>WING 5</option>
                    <option value="6" {{ old('section_id', isset($user) && $user->section_id == 6 ? 'selected' : '') }}>WING 6</option>
                    <option value="7" {{ old('section_id', isset($user) && $user->section_id == 7 ? 'selected' : '') }}>WING 7</option>
                    <option value="8" {{ old('section_id', isset($user) && $user->section_id == 8 ? 'selected' : '') }}>WING 8</option>
                </select>
                @error('section_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                        
            <div class="col-12">
              <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>    
 </div>

 <script>
    // Password confirmation validation
    document.getElementById("confirmPassword").addEventListener("input", function () {
        let password = document.getElementById("password").value;
        let confirmPassword = this.value;
        let errorMsg = document.getElementById("passwordError");

        if (password !== confirmPassword) {
            errorMsg.textContent = "Passwords do not match!";
        } else {
            errorMsg.textContent = ""; // Clear the error message if they match
        }
    });

    // Track if any input field has changed
    let isFormChanged = false;
    document.querySelectorAll(".track-change").forEach(input => {
        input.addEventListener("input", function () {
            isFormChanged = true;
        });
    });

    // Back button confirmation
    document.getElementById("backButton").addEventListener("click", function(event) {
        if (isFormChanged) {
            event.preventDefault(); // Stop default navigation
            let confirmLeave = confirm("You have unsaved changes. Are you sure you want to go back?");
            if (confirmLeave) {
                window.location.href = "{{ route('SuperAdmin.page.UserDetails') }}";
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        let divisionSelect = document.getElementById("division");
        let sectionSelect = document.getElementById("section");
        let sectionContainer = document.getElementById("sectionContainer"); // Get the section container

        divisionSelect.addEventListener("change", function () {
            if (this.value === "2") { // If "CATC" is selected
                sectionContainer.style.display = "block"; // Show the section dropdown
                sectionSelect.disabled = false; // Enable selection
            } else {
                sectionContainer.style.display = "none"; // Hide the section dropdown
                sectionSelect.disabled = true; // Disable selection
                sectionSelect.value = ""; // Reset selection
            }
        });
    });
 </script>
@endsection
