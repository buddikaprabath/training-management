@extends('Admin.HRAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            Bond Summery
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="{{route('Admin.HRAdmin.report.pdf.download-Bond-Summery')}}" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('Admin.HRAdmin.report.BONDSummary') }}" method="GET">
            @csrf
            <div class="d-flex flex-wrap justify-content-between align-item-center gap-2">
                <div class="mb-3">
                    <label for="Employee name" class="form-label">Employee Name</label>
                    <input type="text" name="name" id="employee_name" class="form-control" value="{{ request('name') }}">
                </div>
                <div class="mb-3">
                    <label for="Service Number" class="form-label">Service Number</label>
                    <input type="text" name="epf_number" id="epf_number" class="form-control" value="{{ request('epf_number') }}">
                </div>
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
                    <label for="training_name" class="form-label">Training Name</label>
                    <input type="text" name="training_name" id="training_name" class="form-control" value="{{ request('training_name') }}">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary"> <i data-feather="filter" class="m-1"></i>Filter</button>
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
    <div class="card-body p-4 rounded-3 shadow-lg">
        @if($bondsummery->isEmpty())
            <p>No records found. Please apply filters.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover table-checkable" id="kt_datatable">
                <thead>
                    <tr>
                        <th class="text-center align-top">S/N</th>
                        <th class="text-center align-top">Training Code</th>
                        <th class="text-center align-top">Employee EPF</th>
                        <th class="text-center align-top">Employee Name</th>
                        <th class="text-center align-top">Designation</th>
                        <th class="text-center align-top">Division</th>
                        <th class="text-center align-top">Title Of Bonded Training</th>
                        <th class="text-center align-top">Obligatory Period(from-to)</th>
                        <th class="text-center align-top">Date Of Agreement</th>
                        <th class="text-center align-top">Programme Expired date of bond/agreement Name</th>
                        <th class="text-center align-top">Bond Value</th>
                        <th class="text-center align-top">Surety 01 Name</th>
                        <th class="text-center align-top">Surety 02 Name</th>
                        <th class="text-center align-top">Program Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counter = 1;
                    @endphp
                    @foreach ($bondsummery as $item)
                        @foreach ($item->participants as $participant)
                            <tr>
                                <td class="text-center">{{$counter++}}</td>
                                <td class="text-center">{{ $participant->training->id ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->epf_number ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->designation ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->division->division_name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->training->training_name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->obligatory_period ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->date_of_signing ?? 'N/A' }}</td>
                                <td class="text-center">{{ date('Y-m-d',strtotime($participant->training->exp_date ?? 'N/A')) }}</td>
                                <td class="text-center">{{ $participant->bond_value ?? 'N/A' }}</td>
                                <td class="text-center">{{ $participant->sureties[0]->name ?? 'No Surety' }}</td>
                                <td class="text-center">{{ $participant->sureties[1]->name ?? 'No Surety' }}</td>
                                <td class="text-center">{{ $participant->training->total_program_cost ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination d-flex align-items-end flex-column mb-3">
                    {{ $bondsummery->appends(request()->query())->links() }}
                </ul>
            </nav>
        </div>
        @endif
        
    </div>
</div>
@endsection