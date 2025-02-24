@extends('Admin.HRAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0">Cost BreakDown Details : {{ $costs ? $costs->training_id : 'N/A' }}</p>
        
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>

        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('Admin.HRAdmin.training.Detail') }}" class="text-white text-decoration-none">
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

    @if($costs) 
        <div class="card-group m-5">
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <div class="col">
                  <div class="card bg-body-secondary">
                    <div class="card-body">
                      <h5 class="card-title text-primary">Airfare</h5>
                      <span>{{ $costs->airfare }}</span>
                    </div>
                  </div>
                </div>
                <div class="col">
                    <div class="card bg-body-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Subsistence</h5>
                            <span>{{ $costs->subsistence }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Incidental</h5>
                            <span>{{ $costs->incidental }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Registration</h5>
                            <span>{{ $costs->registration }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Visa</h5>
                            <span>{{ $costs->visa }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Insurance</h5>
                            <span>{{ $costs->insurance }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Warm Clothes</h5>
                            <span>{{ $costs->warm_clothes }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Total Amount</h5>
                            <span>{{ $costs->total_amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="align-items-end">
                <a href="{{route('Admin.HRAdmin.training.costbreak',$costs->id)}}" style="text-decoration: none">
                    <i data-feather="edit" class="text-primary card-link" style="width:36px;height:36px;"></i>
                </a>
                <form action="{{ route('Admin.HRAdmin.training.cost-breakdown.delete', $costs->id) }}" method="POST" 
                        style="display: inline-block; vertical-align: middle; margin-left: 5px;"
                        onsubmit="return confirm('Are you sure you want to delete this item?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                        <i data-feather="trash-2" class="text-primary" style="width: 36px; height: 36px;"></i>
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-warning m-3">
            No cost breakdown data available.
        </div>
    @endif
    
</div>

@endsection
