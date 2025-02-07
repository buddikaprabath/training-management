@extends('SuperAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0 fw-bold">Institute Details</p>

        <!-- Search Form -->
        <form class="d-flex" method="GET" action="#">
            <input class="form-control me-2" type="search" name="query" placeholder="Enter institute name" value="{{ request('query') }}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <a href="{{ route('SuperAdmin.institute.create') }}" class="text-decoration-none">
            <button type="button" class="btn btn-primary d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                Add Institute
            </button>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold text-success mx-3">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger fw-bold text-danger mx-3">
            {{ session('error') }}
        </div>
    @endif


    <div class="card p-2">
        <div class="table-responsive">
          <table class="table">
            <thead>
                <tr>
                    <th class="text-center align-top">ID</th>
                    <th class="text-center align-top">Institute Name</th>
                    <th class="text-center align-top">Institute Type</th>
                    <th class="text-center align-top">Trainer Details</th>
                    <th class="text-center align-top">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($institutes as $institute)
                    <tr>
                        <td class="text-center">{{$institute->id}}</td>
                        <td class="text-center">{{$institute->name}}</td>
                        <td class="text-center">{{$institute->type}}</td>
                        <td class="text-center">
                            <a href="#"><i data-feather="eye"></i></a>
                        </td>
                        <td class="text-center">
                            <a href="{{route('SuperAdmin.institute.edit',$institute->id)}}"><i data-feather="edit"></i></a>&nbsp;&nbsp;
                            <a href="#"><i data-feather="trash-2"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination d-flex align-items-end flex-column mb-3">
                <!-- This will dynamically generate the pagination links -->

            </ul>
        </nav>

        </div>
    </div>
</div>


@endsection