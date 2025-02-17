@extends('SuperAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0">User Details</p>

        <!-- Search Form -->
        <form class="d-flex" method="GET" action="{{ route('SuperAdmin.trainer.search') }}">
            <input class="form-control me-2" type="search" name="query" placeholder="Search Here...." value="{{ request('query') }}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('SuperAdmin.institute.Detail') }}" class="text-white text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle w-75 h-75">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 8 8 12 12 16"></polyline>
                    <line x1="16" y1="12" x2="8" y2="12"></line>
                </svg>
            </a>
        </button>
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
                        <th>Unique Identifier</th>
                        <th>Trainee Name</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Institute</th>
                        <th>Institute Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trainers as $trainer)
                        <tr>
                            <td>{{ $trainer->id }}</td>
                            <td>{{ $trainer->name }}</td>
                            <td>{{ $trainer->email }}</td>
                            <td>{{ $trainer->mobile }}</td>
                            <td>{{ $trainer->institute->name ?? 'N/A' }}</td>
                            <td>{{ $trainer->institute->type ?? 'N/A' }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination d-flex align-items-end flex-column mb-3">
                    {{ $trainers->links('pagination::bootstrap-5') }}
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
