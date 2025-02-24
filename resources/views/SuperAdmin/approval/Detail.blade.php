@extends('SuperAdmin.index')
@section('content')
<div class="container">
    <h2>Pending Approvals</h2>
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Model</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($approvals as $approval)
            <tr>
                <td>{{ $approval->user->name }}</td>
                <td>{{ ucfirst($approval->action) }}</td>
                <td>{{ class_basename($approval->model_type) }}</td>
                <td>
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
                <td>
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
</div>
@endsection