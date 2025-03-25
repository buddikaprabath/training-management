@extends('Admin.HRAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            Trainings Required to be Renewed/Recurrent
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="{{route('Admin.HRAdmin.report.pdf.download-Trainings-Required-to-be-Renewed-Recurrent')}}" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
    </div>
    <div class="card-body">
        <form action="{{route('Admin.HRAdmin.report.TrainingsRequiredtobeRenewed_Recurrent')}}" method="GET">
            @csrf
            <div class="d-flex flex-wrap justify-content-between align-item-center gap-2">
                <div class="mb-3">
                    <label for="course name" class="form-label">Course Training Name</label>
                    <input type="text" name="training_name" id="training_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="epf_number" class="form-label">Epf Number</label>
                    <input type="text" name="epf_number" id="epf_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="division_id" class="form-label">Division</label>
                    <select name="division_id" id="division" class="form-select track-change">
                        <option selected disabled>Choose...</option>
                        <option value="1">HR</option>
                        <option value="2">CATC</option>
                        <option value="3">IT</option>
                        <option value="4">FINANCE</option>
                        <option value="5">SCM</option>
                        <option value="6">MARKETING</option>
                        <option value="7">SECURITY</option>
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
            <span>Course Name : {{$training_name ?? 'N/A'}}</span>
            <span>Employee Name : {{$employee_name ?? 'N/A'}}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Employee EPF : {{$epf_number ?? 'N/A'}}</span>
            <span>Division : {{$division_name ?? 'N/A'}}</span>
        </div>
        <table class="table table-hover table-checkable" id="kt_datatable">
            <thead>
                <tr>
                    <th class="text-center align-top">S/N</th>
                    <th class="text-center align-top">Employee EPF</th>
                    <th class="text-center align-top">Name</th>
                    <th class="text-center align-top">Designation</th>
                    <th class="text-center align-top">Division</th>
                    <th class="text-center align-top">Title of Training Required to Renew/Recurrent</th>
                    <th class="text-center align-top">Validity Period</th>
                    <th class="text-center align-top">Expiration Date</th>

                </tr>
            </thead>
            <tbody>
                @if ($participants->isempty())
                    <tr>
                        <td colspan="8" class="text-center">No Record Found</td>
                    </tr>
                @else
                    @foreach ($participants as $participant)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="text-center">{{$participant->epf_number}}</td>
                            <td class="text-center">{{$participant->name}}</td>
                            <td class="text-center">{{$participant->designation}}</td>
                            <td class="text-center">{{$participant->division->division_name}}</td>
                            <td class="text-center">{{$participant->training->training_name}}</td>
                            <td class="text-center">{{ date('Y-m-d', strtotime($participant->training->training_period_from)) }}-{{ date('Y-m-d', strtotime($participant->training->training_period_to)) }}</td>
                            <td class="text-center">{{ $participant->training->exp_date}}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            
        </table>
    </div>

</div>
@endsection