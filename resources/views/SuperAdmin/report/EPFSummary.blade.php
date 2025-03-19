@extends('SuperAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            EPF Report
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="#" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
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
    <div class="card-body rounded-3 shadow-lg">
        <div class="card card-custom mb-3">
            <div class="card-header bg-light">
                <h3>Particular Course Completed Summery</h3>
                <form action="#" method="GET">
                    <div class="d-flex justify-content-between align-item-center gap-2">
                        <div class="mb-3">
                            <label for="Course name" class="form-lable">Course Name:</label>
                            <input type="text" name="name" id="course_Name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="institution/trainer" class="form-lable">Institution/Trainer:</label>
                            <input type="text" name="institute" id="institute" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="total attendence" class="form-lable">Total Attendence:</label>
                            <input type="text" name="attendence" id="total_Attendence" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="period" class="form-lable">Period:(From-To)</label>
                            <input type="date" name="Period" id="period" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="training days" class="form-lable">Traininig Days:</label>
                            <input type="text" name="training_days" id="training_days" class="form-control">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" style="margin-top:30%"> <i data-feather="filter" class="m-1"></i>Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body bg-light">
                <table class="table table-hover table-checkable">
                    <thead>
                        <tr>
                            <th class="text-center align-top">S/N</th>
                            <th class="text-center align-top">Service No.</th>
                            <th class="text-center align-top">Name</th>
                            <th class="text-center align-top">Designation</th>
                            <th class="text-center align-top">Division</th>
                            <th class="text-center align-top">Cost Per Head</th>
                            <th class="text-center align-top">Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ddadsd</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card card-custom mb-3">
            <div class="card-header bg-light">
                <h3>Local Training Full Summery</h3>
                <form action="#" method="GET">
                    <div class="d-flex justify-content-between align-item-center gap-2">
                        <div class="mb-3">
                            <label for="Year" class="form-lable">Year:</label>
                            <input type="date" name="Year" id="period" class="form-control">
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" style="margin-top:30%"> <i data-feather="filter" class="m-1"></i>Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body bg-light">
                <table class="table table-hover table-checkable" id="kt_datatable">
                    <thead>
                        <tr>
                            <th class="text-center align-top">S/N</th>
                            <th class="text-center align-top">Service No.</th>
                            <th class="text-center align-top">Name</th>
                            <th class="text-center align-top">Designation</th>
                            <th class="text-center align-top">Division</th>
                            <th class="text-center align-top">Cost Per Head</th>
                            <th class="text-center align-top">Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ddadsd</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card card-custom mb-3">
            <div class="card-header bg-light">
                <h3>Training Custodian-Wise Summery</h3>
                <form action="#" method="GET">
                    <div class="d-flex justify-content-between align-item-center">
                        <div class="mb-3">
                            <label for="Course name" class="form-lable">Course Name</label>
                            <input type="text" name="name" id="course_Name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="period" class="form-lable">Period:</label>
                            <input type="date" name="Period" id="period" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="course_type" class="form-label">Course Type</label>
                            <select name="course_type" id="course_type" class="form-select">
                                <option selected disabled>Choose Type...</option>
                                <option value="Local In-house">Local In-house</option>
                                <option value="Local Outside">Local Outside</option>
                                <option value="Local-Tailor Made">Local-Tailor Made</option>
                                <option value="Foreign">Foreign</option>
                                <option value="CATC">CATC</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" style="margin-top:30%"> <i data-feather="filter" class="m-1"></i>Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body bg-light">
                <table class="table table-hover table-checkable" id="kt_datatable">
                    <thead>
                        <tr>
                            <th class="text-center align-top">S/N</th>
                            <th class="text-center align-top">Service No.</th>
                            <th class="text-center align-top">Name</th>
                            <th class="text-center align-top">Designation</th>
                            <th class="text-center align-top">Division</th>
                            <th class="text-center align-top">Cost Per Head</th>
                            <th class="text-center align-top">Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ddadsd</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection