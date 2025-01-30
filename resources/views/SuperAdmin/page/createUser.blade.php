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
    <div class="card-body p-4 bg-body rounded-md shadow-lg">
        <form class="row g-3" action="{{ isset($user) ? route('SuperAdmin.page.user.update', $user->id) : route('SuperAdmin.page.user.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif
            
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input name="name" type="text" class="form-control track-change" placeholder="Name" value="{{ isset($user) ? $user->name : '' }}" required>
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">User Name</label>
                <input name="username" type="text" class="form-control track-change" placeholder="UserName" value="{{ isset($user) ? $user->username : '' }}" required>
            </div>
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Email</label>
                <input name="email" type="email" class="form-control track-change" id="inputEmail4" value="{{ isset($user) ? $user->email : '' }}" required>
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input name="password" type="password" class="form-control track-change" id="password" {{ isset($user) ? '' : 'required' }}>
            </div>
            <div class="col-md-6">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control track-change" id="confirmPassword" name="password_confirmation" {{ isset($user) ? '' : 'required' }}>
                <small id="passwordError" class="text-danger"></small>
            </div>
            <div class="col-md-6">
                <label for="role" class="form-label">User Role</label>
                <select name="role" class="form-select track-change" required>
                    <option selected disabled>Choose...</option>
                    <option value="superadmin" {{ isset($user) && $user->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="hradmin" {{ isset($user) && $user->role == 'hradmin' ? 'selected' : '' }}>HR Admin</option>
                    <option value="catcadmin" {{ isset($user) && $user->role == 'catcadmin' ? 'selected' : '' }}>CATC Admin</option>
                    <option value="user" {{ isset($user) && $user->role == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="division" class="form-label">Divisions</label>
                <select name="division_id" id="division" class="form-select track-change" required>
                    <option selected disabled>Choose...</option>
                    <option value="1" {{ isset($user) && $user->division_id == 1 ? 'selected' : '' }}>HR</option>
                    <option value="2" {{ isset($user) && $user->division_id == 2 ? 'selected' : '' }}>CATC</option>
                    <option value="3" {{ isset($user) && $user->division_id == 3 ? 'selected' : '' }}>IT</option>
                    <option value="4" {{ isset($user) && $user->division_id == 4 ? 'selected' : '' }}>FINANCE</option>
                    <option value="5" {{ isset($user) && $user->division_id == 5 ? 'selected' : '' }}>SCM</option>
                    <option value="6" {{ isset($user) && $user->division_id == 6 ? 'selected' : '' }}>MARKETING</option>
                    <option value="7" {{ isset($user) && $user->division_id == 7 ? 'selected' : '' }}>SECURITY</option>
                </select>
            </div>
            
            <div class="col-md-6" id="sectionContainer" style="display: {{ isset($user) && $user->division_id == 2 ? 'block' : 'none' }};">
                <label for="section_id" class="form-label">Sections</label>
                <select name="section_id" id="section" class="form-select track-change" {{ isset($user) && $user->division_id != 2 ? 'disabled' : '' }}>
                    <option disabled selected>Choose...</option>
                    <option value="1" {{ isset($user) && $user->section_id == 1 ? 'selected' : '' }}>WING 1</option>
                    <option value="2" {{ isset($user) && $user->section_id == 2 ? 'selected' : '' }}>WING 2</option>
                    <option value="3" {{ isset($user) && $user->section_id == 3 ? 'selected' : '' }}>WING 3</option>
                    <option value="4" {{ isset($user) && $user->section_id == 4 ? 'selected' : '' }}>WING 4</option>
                    <option value="5" {{ isset($user) && $user->section_id == 5 ? 'selected' : '' }}>WING 5</option>
                    <option value="6" {{ isset($user) && $user->section_id == 6 ? 'selected' : '' }}>WING 6</option>
                    <option value="7" {{ isset($user) && $user->section_id == 7 ? 'selected' : '' }}>WING 7</option>
                    <option value="8" {{ isset($user) && $user->section_id == 8 ? 'selected' : '' }}>WING 8</option>
                </select>
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
