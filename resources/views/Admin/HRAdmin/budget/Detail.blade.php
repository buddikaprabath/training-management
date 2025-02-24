@extends('Admin.HRAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0">Budget Details</p>
        <!-- Search Form -->
        <form class="d-flex" method="GET" action="#">
            <input class="form-control me-2" type="search" name="query" placeholder="Search here....." value="{{ request('query') }}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <a href="{{route('Admin.HRAdmin.budget.Create')}}" style="text-decoration: none">
         <button type="button" class="btn btn-primary d-flex align-items-center">
            Create New Budget
         </button>

        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success fw-bold text-success ml-5">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger fw-bold text-danger ml-5">
            {{ session('error') }}
        </div>
    @endif
    <div class="card p-2">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center align-top">Year</th>
                    <th class="text-center align-top">Type</th>
                    <th class="text-center align-top">Category</th>
                    <th class="text-center align-top">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($budgets as $budget)
                    <tr>
                        <td class="text-center">{{ $budget->created_year }}</td>
                        <td class="text-center">{{ $budget->type }}</td>
                        <td class="text-center">{{ $budget->provide_type }}</td>
                        <td class="text-center">{{ $budget->amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination d-flex align-items-end flex-column mb-3">
                {{ $budgets->links('pagination::bootstrap-4') }}
            </ul>
        </nav>
    </div>
</div>


</div>

@endsection
