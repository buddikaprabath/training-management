@extends('Admin.HRAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            Training Custodian-Wise Summary
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="{{route('Admin.HRAdmin.report.pdf.dowload-Training-Custodian-Wise-Summery')}}" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
    </div>
    <div class="card-body">
        <form action="{{route('Admin.HRAdmin.report.TrainingCustodianWiseSummery')}}" method="GET">
            @csrf
            <div class="d-flex flex-wrap justify-content-between align-item-center gap-2">
                <div class="mb-3">
                    <label for="Custodian" class="form-label">Custodian Name</label>
                    <input type="text" name="custodian" id="custodian" class="form-control">
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
            <span>Custodian Name : {{$custodian ?? 'N/A'}}</span>
            <span>Course Type : {{$course_type ?? 'N/A'}}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Period : {{$year ?? 'N/A'}}</span>
        </div>
        <table class="table table-hover table-checkable" id="kt_datatable">
            <thead>
                <tr>
                    <th class="text-center align-top">S/N</th>
                    <th class="text-center align-top">Course/Training Programme Name</th>
                    <th class="text-center align-top">Institute</th>
                    <th class="text-center align-top">Trainer</th>
                    <th class="text-center align-top">No. of Days</th>
                    <th class="text-center align-top">No.Of Participants</th>
                    <th class="text-center align-top">Total Cost</th>
                    <th class="text-center align-top">Training Hours</th>
                    <th class="text-center align-top">Month</th>

                </tr>
            </thead>
            <tbody>
                @if($trainings->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center">No records found.</td>
                    </tr>
                @else
                    @foreach($trainings as $training)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $training->training_name }}</td>
                            <td class="text-center">
                                @foreach ($training->institutes as $institute)
                                    {{$institute->name}}
                                @endforeach
                            </td>
                            <td class="text-center">
                                @foreach ($training->trainers as $trainer)
                                    {{$trainer->name}}
                                @endforeach
                            </td>
                            <td class="text-center">{{ $training->duration }}</td>
                            <td class="text-center">{{ $training->participants_count }}</td> <!-- Participant Count -->
                            <td class="text-center">{{$training->total_program_cost}}</td>
                            <td class="text-center">{{$training->total_training_hours}}</td>
                            <td class="text-center">{{ $training->training_period_to->format('M') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            
        </table>
    </div>

</div>
@endsection