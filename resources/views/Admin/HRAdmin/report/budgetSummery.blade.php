@extends('Admin.HRAdmin.index')
@section('content')
<div class="card card-custom">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h3 class="card-title">
            Budget Summary
        </h3>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <!-- Download Button -->
        <a href="{{route('Admin.HRAdmin.report.pdf.download-Budget-Summery')}}" class="btn btn-primary d-flex align-items-center px-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download PDF
        </a>
    </div>
    <div class="card-body">
        <form action="{{route('Admin.HRAdmin.report.BudgetSummery')}}" method="GET">
            @csrf
            <div class="d-flex flex-wrap justify-content-between align-item-center gap-2">
                <div class="mb-3">
                    <label for="Duration" class="form-label">Monthly</label>
                    <select name="duration_monthly" id="duration_monthly" class="form-select track-change">
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
                <div class="mb-3">
                    <label for="Duration" class="form-label">Quartely</label>
                    <select name="duration_quarterly" id="duration_quarterly" class="form-select track-change">
                        <option selected disabled>Choose...</option>
                        <option value="1">Q 1</option>
                        <option value="2">Q 2</option>
                        <option value="3">Q 3</option>
                        <option value="4">Q 4</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="Year" class="form-label">Year</label>
                    <input type="number" name="year" id="year" class="form-control">
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
    <div class="card-body p-4 rounded-3 shadow-lg">
        <table class="table table-hover table-checkable" id="kt_datatable">
            <thead>
                <tr>
                    <th class="text-center align-top"></th>
                    <th class="text-center align-top">No. of Training</th>
                    <th class="text-center align-top">No. of Participants</th>
                    <th class="text-center align-top">Total No. of Hours</th>
                    <th class="text-center align-top">Total Cost</th>
                    <th class="text-center align-top">Available Rs.</th>
                    <th class="text-center align-top">Initial Budget Allocations</th>
                    <th class="text-center align-top">Budget Utilization</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center"><strong>Local</strong></td>
                    <td class="text-center">{{ $local['training_count'] ?? 0 }}</td>
                    <td class="text-center">{{ $local['participant_count'] ?? 0 }}</td>
                    <td class="text-center">{{ $local['total_hours'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format($local['total_cost'] ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($local['budget_amount'] ?? 0) - ($local['total_cost'] ?? 0), 2) }}</td>
                    <td class="text-center">{{ number_format($local['budget_amount'] ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($local['total_cost'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-center"><strong>Foreign</strong></td>
                    <td class="text-center">{{ $foreign['training_count'] ?? 0 }}</td>
                    <td class="text-center">{{ $foreign['participant_count'] ?? 0 }}</td>
                    <td class="text-center">{{ $foreign['total_hours'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format($foreign['total_cost'] ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($foreign['budget_amount'] ?? 0) - ($foreign['total_cost'] ?? 0), 2) }}</td>
                    <td class="text-center">{{ number_format($foreign['budget_amount'] ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($foreign['total_cost'] ?? 0, 2) }}</td>
                </tr>
            </tbody>
            
        </table>
    </div>

</div>
<script>
    document.getElementById("year").value = new Date().getFullYear();
</script>
@endsection