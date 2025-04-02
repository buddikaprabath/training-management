@extends('SuperAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0">User Details</p>

        <!-- Search Form -->
        <form class="d-flex" method="GET" action="{{ route('SuperAdmin.Users.user.search') }}">
            <input class="form-control me-2" type="search" name="query" placeholder="name, username, or email" value="{{ request('query') }}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <a href="{{route('SuperAdmin.Users.Create')}}">
            <button type="button" class="btn btn-primary">
                <i class="ml-2" data-feather="user-plus" style="width: 24px; height: 24px;"></i>
                Create User
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
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Division</th>
                        <th>Section</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($users->isNotEmpty())
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->division->division_name }}</td>
                                <td>{{ $user->section ? $user->section->section_name : 'None'}}</td>
                                <td>
                                    <a href="{{ route('SuperAdmin.Users.edit', $user->id) }}">
                                        <i class="align-middle me-2" data-feather="edit" ></i>
                                    </a>
                                    <form action="{{ route('SuperAdmin.Users.user.delete', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="border: none; background: transparent; padding: 0;" onclick="return confirm('Are you sure you want to delete user {{ $user->name }}?')">
                                            <i class="align-middle me-2 text-danger" data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination d-flex align-items-end flex-column mb-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
