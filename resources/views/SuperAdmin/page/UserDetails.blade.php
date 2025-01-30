@extends('SuperAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0">User Details</p>
        <a href="{{route('SuperAdmin.page.createUser')}}">
            <button type="button" class="btn btn-primary">
                <i class="ml-2" data-feather="user-plus" style="width: 24px; height: 24px;"></i>
                Create User
            </button>
        </a>
    </div>
    
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
                                <a href="{{ route('SuperAdmin.page.user.edit', $user->id) }}"><i class="align-middle me-2" data-feather="edit"></i></a>
                                <form action="{{ route('SuperAdmin.page.user.delete', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border: none; background: transparent; padding: 0;" onclick="return confirm('Are you sure?')"><i class="align-middle me-2" data-feather="trash-2"></i></button>
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
        
        </div>
    </div>
    
</div>

@endsection