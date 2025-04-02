@extends('Admin.HRAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            Course Code-Wise Summary
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="{{route('Admin.HRAdmin.report.pdf.download-Course-code-wise-summery')}}" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
    </div>
    <div class="card-body">
        <form action="{{route('Admin.HRAdmin.report.CourseCode-wise_summary')}}" method="GET">
            @csrf
            <div class="d-flex flex-wrap justify-content-between align-item-center gap-2">
                <!-- Training Code selection -->
                <div class="mb-3">
                    <label for="course_code" class="form-label">Course Code</label>
                    <select name="course_code" id="course_code" class="form-select track-change">
                        <option disabled selected>Choose training Code</option>
                        @foreach($training_codes as $code)
                            <option value="{{ $code->training_codes }}">
                                {{ $code->training_codes }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="Duration" class="form-label">Monthly</label>
                    <select name="duration" id="duration" class="form-select track-change">
                        <option selected disabled>Choose...</option>
                        <?php
                            for ($month = 1; $month <= 12; $month++) {
                                $monthName = DateTime::createFromFormat('!m', $month)->format('F'); // Get full month name
                                $monthValue = str_pad($month, 2, '0', STR_PAD_LEFT); // Ensure two-digit format (e.g., 01, 02)
                                echo "<option value='$monthValue'>$monthName</option>";
                            }
                        ?>
                    </select>
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
            <span>Course Code : {{$course_code ?? 'N/A'}}</span>
            <span>Category : {{$course_type ?? 'N/A'}}</span>
        </div>
        <table class="table table-hover table-checkable" id="kt_datatable">
            <thead>
                <tr>
                    <th class="text-center align-top">S/N</th>
                    <th class="text-center align-top">Category</th>
                    <th class="text-center align-top">Course/Training Name</th>
                    <th class="text-center align-top">No. Of Participants</th>
                    <th class="text-center align-top">Training Hours</th>
                    <th class="text-center align-top">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @if ($trainings->isempty())
                    <tr>
                        <td colspan="6" class="text-center">No records found.</td>
                    </tr>
                @else
                    @foreach ($trainings as $training)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="text-center">{{$training->category}}</td>
                            <td class="text-center">{{$training->training_name}}</td>
                            <td class="text-center">{{$training->participants_count }}</td>
                            <td class="text-center">{{$training->total_training_hours}}</td>
                            <td class="text-center">{{$training->total_program_cost}}</td>
                        </tr>
                    @endforeach
                @endif
                
            </tbody>
            
        </table>
    </div>

</div>
@endsection