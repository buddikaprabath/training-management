@extends('SuperAdmin.index')
@section('content')

<div class="card">
    <!-- Header Section -->
    <div class="m-3 d-flex justify-content-between align-items-center">
        <h2 class="h4 fw-bold mb-0">Cost Breakdown Details: {{ $costs ? $costs->training_id : 'N/A' }}</h2>
        
        <div class="d-flex align-items-center">
            <!-- Refresh Button -->
            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary me-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
            </a>
            
            <!-- Back Button -->
            <a href="{{ route('SuperAdmin.training.Detail') }}" class="btn btn-sm btn-outline-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success mx-3">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger mx-3">
            {{ session('error') }}
        </div>
    @endif

    <!-- Cost Breakdown Cards -->
    @if($costs) 
        <div class="container-fluid p-3">
            <div class="row g-3">
                <!-- Card Template -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Airfare</h6>
                            <p class="card-text fs-5 fw-bold">{{ number_format($costs->airfare, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Subsistence</h6>
                            <p class="card-text fs-5 fw-bold">{{ number_format($costs->subsistence, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Incidental</h6>
                            <p class="card-text fs-5 fw-bold">{{ number_format($costs->incidental, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Registration</h6>
                            <p class="card-text fs-5 fw-bold">{{ number_format($costs->registration, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Visa</h6>
                            <p class="card-text fs-5 fw-bold">{{ number_format($costs->visa, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Insurance</h6>
                            <p class="card-text fs-5 fw-bold">{{ number_format($costs->insurance, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Warm Clothes</h6>
                            <p class="card-text fs-5 fw-bold">{{ number_format($costs->warm_clothes, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-primary">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-primary">Total Amount</h6>
                            <p class="card-text fs-5 fw-bold text-primary">{{ number_format($costs->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="{{route('SuperAdmin.training.costbreak',$costs->id)}}" class="btn btn-outline-primary">
                    <i data-feather="edit" class="me-1"></i> Edit
                </a>
                
                <form action="{{ route('SuperAdmin.training.cost-breakdown.delete', $costs->id) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this item?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i data-feather="trash-2" class="me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-warning mx-3">
            No cost breakdown data available.
        </div>
    @endif
</div>

<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

@endsection