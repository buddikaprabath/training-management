@extends('Admin.HRAdmin.index')
@section('content')
<div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($participant) ? 'Edit Participant' : 'Create Participant' }}</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('Admin.HRAdmin.training.Detail') }}" class="text-white text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle w-75 h-75">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 8 8 12 12 16"></polyline>
                    <line x1="16" y1="12" x2="8" y2="12"></line>
                </svg>
            </a>
        </button>
    </div>
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        @unless(isset($participant))
            <!-- Search Form -->
            <form class="d-flex" method="GET" action="{{route('Admin.HRAdmin.participant.create',$training->id ?? '')}}" style="max-width: 250px;">
                <input class="form-control me-2" type="search" name="epf_number" placeholder="Search here..." value="">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        
            <!-- Download Button -->
            <a href="{{ route('Admin.HRAdmin.participant.export-participant-columns') }}" class="btn btn-primary d-flex align-items-center px-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Download Excel
            </a>
        
            <!-- File Upload -->
            <form action="{{ route('Admin.HRAdmin.participant.import-participants') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center" id="importForm">
                @csrf
                <!-- Hidden input to pass the training_id -->
                <input type="hidden" name="training_id" value="{{ $training->id ?? '' }}"> <!-- Add training ID here -->

                <input class="form-control d-none" type="file" id="formFile" name="file" onchange="fileSelected()">
                <button type="button" class="btn btn-primary d-flex align-items-center px-3" onclick="document.getElementById('formFile').click();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload me-2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Import Excel
                </button>
            </form>
        @endunless
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
        <form class="row g-3" action="{{ isset($participant) ? route('Admin.HRAdmin.participant.update', $participant->id) : route('Admin.HRAdmin.participant.store') }}" method="POST">
            @csrf
            @if(isset($participant))
                @method('PUT') <!-- HTTP method spoofing for PUT request -->
            @endif
            
            <!-- EPF Number -->
            <div class="col-md-6">
                <label for="epf_number" class="form-label">EPF Number</label>
                <input name="epf_number" type="text" class="form-control track-change @error('epf_number') is-invalid @enderror" placeholder="EPF_Number" value="{{ old('epf_number', isset($employee['epf_number']) ? $employee['epf_number'] : (isset($participant) ? $participant->epf_number : '')) }}" required>
                @error('epf_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Participant Name -->
            <div class="col-md-6">
                <label for="name" class="form-label">Participant Name</label>
                <input name="name" type="text" class="form-control track-change @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name', isset($employee['name']) ? $employee['name'] : (isset($participant) ? $participant->name : '')) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Designation -->
            <div class="col-md-6">
                <label for="designation" class="form-label">Designation</label>
                <input name="designation" type="text" class="form-control track-change @error('designation') is-invalid @enderror" placeholder="Designation" value="{{ old('designation', isset($employee['Designation'])? $employee['Designation'] : (isset($participant) ? $participant->designation : '')) }}" required>
                @error('designation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <!-- salary_scale -->
            <div class="col-md-6">
                <label for="salary_scale" class="form-label">Salary scale</label>
                <input name="salary_scale" type="text" class="form-control track-change @error('salary_scale') is-invalid @enderror" 
                       placeholder="e.g. S1, S2" 
                       value="{{ old('salary_scale', isset($employee['salary_scale_id']) ? $employee['salary_scale_id'] : (isset($participant) ? $participant->salary_scale : '')) }}" required>
                @error('salary_scale')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- location -->
            <div class="col-md-6">
                <label for="location" class="form-label">Location</label>
                <input name="location" type="text" class="form-control track-change @error('location') is-invalid @enderror" placeholder="Location" value="{{ old('location', isset($participant) ? $participant->location : '') }}" required>
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Obligatory Period -->
            <div class="col-md-6">
                <label for="obligatory_period" class="form-label">Obligatory Period</label>
                <input name="obligatory_period" type="date" class="form-control track-change @error('obligatory_period') is-invalid @enderror" placeholder="Obligatory Period" value="{{ old('obligatory_period', isset($participant) ? $participant->obligatory_period : '') }}" required>
                @error('obligatory_period')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Cost Per head -->
            <div class="col-md-6">
                <label for="cost_per_head" class="form-label">Cost per Head</label>
                <input name="cost_per_head" type="number" class="form-control track-change @error('cost_per_head') is-invalid @enderror" placeholder="Cost per head" value="{{ old('cost_per_head', isset($participant) ? $participant->cost_per_head : '') }}" required>
                @error('cost_per_head')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- bond_completion_date -->
            <div class="col-md-6">
                <label for="bond_completion_date" class="form-label">Bond Completion Date</label>
                <input name="bond_completion_date" type="date" class="form-control track-change @error('bond_completion_date') is-invalid @enderror" placeholder="Bond Completion Date" value="{{ old('bond_completion_date', isset($participant) ? $participant->bond_completion_date : '') }}" required>
                @error('bond_completion_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!--bond_value-->
            <div class="col-md-6">
                <label for="bond_value" class="form-label">Bond Value</label>
                <input name="bond_value" type="number" class="form-control track-change @error('bond_value') is-invalid @enderror" placeholder="Bond Value" value="{{ old('bond_value', isset($participant) ? $participant->bond_value : '') }}" required>
                @error('bond_value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- date_of_signing -->
            <div class="col-md-6">
                <label for="date_of_signing" class="form-label">Date of signing</label>
                <input name="date_of_signing" type="date" class="form-control track-change @error('date_of_signing') is-invalid @enderror" placeholder="date_of_signing" value="{{ old('date_of_signing', isset($participant) ? $participant->date_of_signing : '') }}" required>
                @error('date_of_signing')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- date_of_birth -->
            <div class="col-md-6">
                <label for="date_of_birth" class="form-label">Date Of Birth</label>
                <input id="date_of_birth" name="date_of_birth" type="date" class="form-control track-change @error('date_of_birth') is-invalid @enderror" placeholder="Date of Birth" value="{{ old('date_of_birth', isset($participant) ? $participant->date_of_birth : '') }}" required>
                @error('date_of_birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            

            <!-- date_of_appointment -->
            <div class="col-md-6">
                <label for="date_of_appointment" class="form-label">Date of Appointment</label>
                <input name="date_of_appointment" type="date" class="form-control track-change @error('date_of_appointment') is-invalid @enderror" placeholder="date_of_appointment" value="{{ old('date_of_appointment', isset($participant) ? $participant->date_of_appointment : '') }}" required>
                @error('date_of_appointment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- date_of_appointment_to_the_present_post -->
            <div class="col-md-6">
                <label for="date_of_appointment_to_the_present_post" class="form-label">Date of Appointment to the Present Post</label>
                <input name="date_of_appointment_to_the_present_post" type="date" class="form-control track-change @error('date_of_appointment_to_the_present_post') is-invalid @enderror" placeholder="Date of Appointment to the Present Post" value="{{ old('date_of_appointment_to_the_present_post', isset($participant) ? $participant->date_of_appointment_to_the_present_post : '') }}" required>
                @error('date_of_appointment_to_the_present_post')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- age_as_at_commencement_date -->
            <div class="col-md-6">
                <label for="age_as_at_commencement_date" class="form-label">Age as at commencement date</label>
                <input id="age_as_at_commencement_date" name="age_as_at_commencement_date" type="number" class="form-control track-change @error('age_as_at_commencement_date') is-invalid @enderror" placeholder="Age as at commencement date" value="{{ old('age_as_at_commencement_date', isset($participant) ? $participant->age_as_at_commencement_date : '') }}" required>
                @error('age_as_at_commencement_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Division Selection -->
            <div class="col-md-6">
                <label for="division_id" class="form-label">Division</label>
                <select name="division_id" id="division" class="form-select track-change @error('division_id') is-invalid @enderror" required>
                    <option selected disabled>Choose...</option>
                    <option value="1" {{ old('division_id', isset($participant) && $participant->division_id == 1 ? 'selected' : '') }}>HR</option>
                    <option value="2" {{ old('division_id', isset($participant) && $participant->division_id == 2 ? 'selected' : '') }}>CATC</option>
                    <option value="3" {{ old('division_id', isset($participant) && $participant->division_id == 3 ? 'selected' : '') }}>IT</option>
                    <option value="4" {{ old('division_id', isset($participant) && $participant->division_id == 4 ? 'selected' : '') }}>FINANCE</option>
                    <option value="5" {{ old('division_id', isset($participant) && $participant->division_id == 5 ? 'selected' : '') }}>SCM</option>
                    <option value="6" {{ old('division_id', isset($participant) && $participant->division_id == 6 ? 'selected' : '') }}>MARKETING</option>
                    <option value="7" {{ old('division_id', isset($participant) && $participant->division_id == 7 ? 'selected' : '') }}>SECURITY</option>
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
                    <option value="1" {{ old('section_id', isset($participant) && $participant->section_id == 1 ? 'selected' : '') }}>WING 1</option>
                    <option value="2" {{ old('section_id', isset($participant) && $participant->section_id == 2 ? 'selected' : '') }}>WING 2</option>
                    <option value="3" {{ old('section_id', isset($participant) && $participant->section_id == 3 ? 'selected' : '') }}>WING 3</option>
                    <option value="4" {{ old('section_id', isset($participant) && $participant->section_id == 4 ? 'selected' : '') }}>WING 4</option>
                    <option value="5" {{ old('section_id', isset($participant) && $participant->section_id == 5 ? 'selected' : '') }}>WING 5</option>
                    <option value="6" {{ old('section_id', isset($participant) && $participant->section_id == 6 ? 'selected' : '') }}>WING 6</option>
                    <option value="7" {{ old('section_id', isset($participant) && $participant->section_id == 7 ? 'selected' : '') }}>WING 7</option>
                    <option value="8" {{ old('section_id', isset($participant) && $participant->section_id == 8 ? 'selected' : '') }}>WING 8</option>
                </select>
                @error('section_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Add Surety Checkbox -->
            <div class="col-md-6 mt-4 d-flex align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="addSuretyCheckbox">
                    <label class="form-check-label ms-2" for="addSuretyCheckbox">
                        Add Surety
                    </label>
                </div>
            </div>
            <!-- Surety Fields (Initially Hidden) -->
            <div id="suretyFields" style="display: none;">
                @for($i = 0; $i < 2; $i++)
                    @php
                        $surety = isset($participant->sureties[$i]) ? $participant->sureties[$i] : null;
                    @endphp

                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label for="sureties[{{ $i }}][name]" class="form-label">Surety {{ $i + 1 }} Name</label>
                            <input name="sureties[{{ $i }}][name]" type="text" class="form-control @error('sureties.'.$i.'.name') is-invalid @enderror" placeholder="Surety Name" value="{{ old('sureties.'.$i.'.name', $surety ? $surety->name : '') }}">
                            @error('sureties.'.$i.'.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="sureties[{{ $i }}][nic]" class="form-label">NIC</label>
                            <input name="sureties[{{ $i }}][nic]" type="text" class="form-control @error('sureties.'.$i.'.nic') is-invalid @enderror" placeholder="NIC" value="{{ old('sureties.'.$i.'.nic', $surety ? $surety->nic : '') }}">
                            @error('sureties.'.$i.'.nic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="sureties[{{ $i }}][mobile]" class="form-label">Mobile</label>
                            <input name="sureties[{{ $i }}][mobile]" type="number" class="form-control @error('sureties.'.$i.'.mobile') is-invalid @enderror" placeholder="Mobile" value="{{ old('sureties.'.$i.'.mobile', $surety ? $surety->mobile : '') }}">
                            @error('sureties.'.$i.'.mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="sureties[{{ $i }}][address]" class="form-label">Address</label>
                            <input name="sureties[{{ $i }}][address]" type="text" class="form-control @error('sureties.'.$i.'.address') is-invalid @enderror" placeholder="Address" value="{{ old('sureties.'.$i.'.address', $surety ? $surety->address : '') }}">
                            @error('sureties.'.$i.'.address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="sureties[{{ $i }}][salary_scale]" class="form-label">Salary Scale</label>
                            <input name="sureties[{{ $i }}][salary_scale]" type="text" class="form-control @error('sureties.'.$i.'.salary_scale') is-invalid @enderror" placeholder="Salary Scale" value="{{ old('sureties.'.$i.'.salary_scale', $surety ? $surety->salary_scale : '') }}">
                            @error('sureties.'.$i.'.salary_scale')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="sureties[{{ $i }}][suretydesignation]" class="form-label">Designation</label>
                            <input name="sureties[{{ $i }}][suretydesignation]" type="text" class="form-control @error('sureties.'.$i.'.suretydesignation') is-invalid @enderror" placeholder="Designation" value="{{ old('sureties.'.$i.'.suretydesignation', $surety ? $surety->designation : '') }}">
                            @error('sureties.'.$i.'.suretydesignation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- EPF Number -->
                        <div class="col-md-6 mt-3">
                            <label for="sureties[{{ $i }}][epf_number]" class="form-label">EPF Number</label>
                            <input name="sureties[{{ $i }}][epf_number]" type="text" class="form-control @error('sureties.'.$i.'.epf_number') is-invalid @enderror" placeholder="EPF Number" value="{{ old('sureties.'.$i.'.epf_number', $surety ? $surety->epf_number : '') }}">
                            @error('sureties.'.$i.'.epf_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Divider between Surety 1 and Surety 2 -->
                    @if ($i == 0)
                        <hr class="my-4">
                    @endif
                @endfor
            </div>
            <!-- Other Comments -->
            <div class="col-md-12">
                <label for="other_comments" class="form-label">Other Comments</label>
                <textarea name="remarks[]" class="form-control track-change @error('remarks') is-invalid @enderror" 
                        placeholder="Other Comments" rows="3">
                </textarea>
                @error('remarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!--training ID-->

            <input type="hidden" name="training_id" value="{{ old('training_id', isset($participant) ? $participant->training_id : $training->id) }}">

            <!-- Submit Button -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">{{ isset($participant) ? 'Update' : 'Create' }}</button>
            </div>
            
        </form>
    </div>
</div>
<!-- Include jQuery and Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    // Function to calculate the age
    function calculateAge() {
        var dob = document.getElementById('date_of_birth').value;
        if (dob) {
            var birthDate = new Date(dob);
            var currentDate = new Date();
            var age = currentDate.getFullYear() - birthDate.getFullYear();
            var monthDiff = currentDate.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && currentDate.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById('age_as_at_commencement_date').value = age;
        }
    }

    // Attach event listener to the date_of_birth input
    document.getElementById('date_of_birth').addEventListener('change', calculateAge);

    // Add event listener for the addSuretyCheckbox change
    document.getElementById('addSuretyCheckbox').addEventListener('change', function () {
        var suretyFields = document.getElementById('suretyFields');
        suretyFields.style.display = this.checked ? 'block' : 'none';
    });

    // Add event listener for the division change
    document.getElementById('division').addEventListener('change', function () {
        var sectionContainer = document.getElementById('sectionContainer');
        if (this.value === '2') {
            sectionContainer.style.display = 'block';
            document.getElementById('section').disabled = false;
            var suretyFields = document.getElementById('suretyFields');
            suretyFields.style.display = document.getElementById('addSuretyCheckbox').checked ? 'block' : 'none'; // Maintain the state of surety fields
        } else {
            sectionContainer.style.display = 'none';
            document.getElementById('section').disabled = true;
            var suretyFields = document.getElementById('suretyFields');
            suretyFields.style.display = 'none'; // Hide surety fields when division is not HR
        }
    });

    // Function to handle file selection
    function fileSelected() {
        let fileInput = document.getElementById('formFile');
        if (fileInput.files.length > 0) {
            document.getElementById('importForm').submit();
        }
    }
</script>
@endsection