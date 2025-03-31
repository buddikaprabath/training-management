@extends('SuperAdmin.index')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="m-3 d-flex justify-content-between align-items-center">
                <h1>Notifications</h1>
                <a href="{{ url()->current() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                </a>
            </div>
        </div>
        <div class="card-body bg-body m-2">
                <ul class="list-group">
                        <li class="list-group-item">
                            <div class="row g-0 d-flex justify-content-between align-items-center">
                                <div class="col-2">
                                    <i class="text-primary" data-feather="bell"></i>
                                </div>
                                <div class="col-8">
                                    <div class="text-dark"></div>
                                    <div class="text-muted small mt-1"></div>
                                    <div class="text-muted small mt-1"></div>
                                </div>
                                <div class="col-2">
                                    <form id="statusForm" action="" method="POST">
                                        @csrf
                                        @method('PUT') <!-- Simulate PUT request -->
                                
                                        <!-- Hidden input for the id, though it's already passed in the route -->
                                        <input type="hidden" name="id" value="">
                                        <input type="hidden" name="status" value="read">
                                        <!-- Checkbox for status -->
                                        <input type="checkbox" class="form-check-input me-1 notification-status"
                                            style="width: 20px; height:20px;"
                                            onchange="updateStatus(this)">
                                    </form>
                                </div>
                            </div>
                        </li>
                </ul>

                {{-- Paginate the notifications --}}
                <div class="mt-3">
                    
                </div>
        </div>
    </div>
@endsection