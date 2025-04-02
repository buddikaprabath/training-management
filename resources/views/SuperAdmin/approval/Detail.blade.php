@extends('SuperAdmin.index')
@section('content')
<div class="card">

    <div class="m-3 d-flex justify-content-between align-items-center">
        <h2>Pending Approvals</h2>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
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
                        <th class="text-center align-top">User</th>
                        <th class="text-center align-top">Action</th>
                        <th class="text-center align-top">Model</th>
                        <th class="text-center align-top">Description</th>
                        <th class="text-center align-top">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($approvals as $approval)
                    <tr>
                        <td class="text-center">{{ $approval->user->name }}</td>
                        <td class="text-center">{{ ucfirst($approval->action) }}</td>
                        <td class="text-center">{{ class_basename($approval->model_type) }}</td>
                        <td class="text-center">
                            @if($approval->action == 'edit')
                                @php
                                    $newData = json_decode($approval->new_data, true);
                                    $changes = [];
            
                                    // Checking which fields were edited and displaying them
                                    if (isset($newData['training_code']) && $newData['training_code'] != null) {
                                        $changes[] = "Training code - " . $newData['training_code'];
                                    }
                                    if (isset($newData['category']) && $newData['category'] != null) {
                                        $changes[] = "Category - " . $newData['category'];
                                    }
                                    if (isset($newData['training_name']) && $newData['training_name'] != null) {
                                        $changes[] = "Training name - " . $newData['training_name'];
                                    }
                                    if (isset($newData['training_period_from']) && isset($newData['training_period_to']) &&
                                        $newData['training_period_from'] != null && $newData['training_period_to'] != null) {
                                        $changes[] = "Training period - From " . $newData['training_period_from'] . " to " . $newData['training_period_to'];
                                    }
                                    if (isset($newData['total_training_hours']) && $newData['total_training_hours'] != null) {
                                        $changes[] = "Total training hours - " . $newData['total_training_hours'];
                                    }
                                    if (isset($newData['remark']) && $newData['remark'] != null) {
                                        $changes[] = "Remark - " . $newData['remark'];
                                    }
            
                                    // If changes exist, join them into a readable message
                                    $description = "Training ID " . $approval->model_id . " edited by " . $approval->user->name . ": " . implode(", ", $changes);
                                @endphp
                                <p>{{ $description }}</p>
                            @elseif($approval->action == 'delete')
                                <p>Training ID {{ $approval->model_id }} marked for deletion by {{ $approval->user->name }}.</p>
                            @else
                                <p>No changes.</p>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('SuperAdmin.approval.approve', $approval) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                            <form action="{{ route('SuperAdmin.approval.reject', $approval) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $approvals->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    
</div>
@endsection