@extends('SuperAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            Training Summary
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="{{ route('SuperAdmin.report.pdf.download-training-summary-pdf') }}" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
    </div>
    <div class="card-body">
        <form action="{{route('SuperAdmin.report.trainingSummary')}}" method="GET">
            @csrf
            <div class="d-flex flex-wrap justify-content-between align-item-center gap-2">
                <div class="mb-3">
                    <label for="start date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="StartDate" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="EndDate" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="endDate" class="form-control">
                </div>
                <!-- Division id -->
                <div class="mb-3">
                    <label for="division_id" class="form-label">Division</label>
                    <select name="division_id" id="division" class="form-select track-change">
                        <option selected disabled>Choose...</option>
                        <option value="1" {{ request('division_id') == 1 ? 'selected' : '' }}>HR</option>
                        <option value="2" {{ request('division_id') == 2 ? 'selected' : '' }}>CATC</option>
                        <option value="3" {{ request('division_id') == 3 ? 'selected' : '' }}>IT</option>
                        <option value="4" {{ request('division_id') == 4 ? 'selected' : '' }}>FINANCE</option>
                        <option value="5" {{ request('division_id') == 5 ? 'selected' : '' }}>SCM</option>
                        <option value="6" {{ request('division_id') == 6 ? 'selected' : '' }}>MARKETING</option>
                        <option value="7" {{ request('division_id') == 7 ? 'selected' : '' }}>SECURITY</option>
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
        <table class="table table-hover table-checkable" id="kt_datatable">
            <thead>
                <tr>
                    <th class="text-center align-top">Course Type</th>
                    <th class="text-center align-top">No. of Programs</th>
                    <th class="text-center align-top">No. of Participants</th>
                    <th class="text-center align-top">Training Hours</th>
                    <th class="text-center align-top">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($combinedSummary as $summary)
                    <tr class="hover:bg-gray-100 transition">
                        <td class="ps-4 text-center py-2">{{ $summary->course_type }}</td>
                        <td class="ps-4 text-center py-2">{{ $summary->no_of_programs }}</td>
                        <td class="ps-4 text-center py-2">{{ $summary->no_of_participants }}</td>
                        <td class="ps-4 text-center py-2">{{ $summary->training_hours }}</td>
                        <td class="ps-4 text-center py-2">{{ number_format($summary->total_cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            
        </table>
    </div>

</div>
@endsection