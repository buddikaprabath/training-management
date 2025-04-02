@extends('Admin.HRAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            Designation Wise Summary
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="{{route('Admin.HRAdmin.report.pdf.download-Designation-Wise-Summery')}}" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
    </div>
    <div class="card-body">
        <form action="{{route('Admin.HRAdmin.report.DesignationWiseSummery')}}" method="GET">
            @csrf
            <div class="d-flex flex-wrap justify-content-between align-item-center gap-2">
                <div class="mb-3">
                    <label for="designation" class="form-label">Employee Designation</label>
                    <input type="text" name="designation" id="Designation" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="Year" class="form-label">Year</label>
                    <input type="number" name="year" id="year" class="form-control">
                </div>
                 <!-- Course Type -->
                 <div class="mb-3">
                    <label for="course_type" class="form-label">Course Type</label>
                    <select name="course_type" id="course_type" class="form-select track-change">
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
        <div class="d-flex justify-content-between">
            <span>Employee Designation : {{$designation ?? 'N/A'}}</span>
            <span>Course Type : {{$course_type ?? 'N/A'}}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Period : {{$year ?? 'N/A'}}</span>
        </div>
        <table class="table table-hover table-checkable" id="kt_datatable">
            <thead>
                <tr>
                    <th class="text-center align-top">S/N</th>
                    <th class="text-center align-top">EPF</th>
                    <th class="text-center align-top">Employee Name</th>
                    <th class="text-center align-top">Training Code</th>
                    <th class="text-center align-top">Training Program</th>
                    <th class="text-center align-top">Category</th>
                    <th class="text-center align-top">Mode of Delivery</th>
                    <th class="text-center align-top">Division</th>

                </tr>
            </thead>
            <tbody>
                @if($trainings->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center">No records found.</td>
                    </tr>
                @else
                    @foreach($trainings as $training)
                        @foreach($training->participants as $participant)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{$participant->epf_number}}</td>
                                <td class="text-center">{{$participant->name}}</td>
                                <td class="text-center">{{$training->training_code}}</td>
                                <td class="text-center">{{$training->training_name}}</td>
                                <td class="text-center">{{$training->category}}</td>
                                <td class="text-center">{{$training->mode_of_delivery}}</td>
                                <td class="text-center">{{$participant->division->division_name}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </tbody>
            
        </table>
    </div>

</div>
@endsection