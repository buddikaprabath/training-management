@extends('Admin.CATCAdmin.index')
@section('content')
 <div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">Edit Cost Break Down</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('Admin.HRAdmin.training.costDetail',$costs->training_id) }}" class="text-white text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle w-75 h-75">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 8 8 12 12 16"></polyline>
                    <line x1="16" y1="12" x2="8" y2="12"></line>
                </svg>
            </a>
        </button>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card-body p-4 rounded-3 shadow-lg" style="background-color: #A8BDDB;">
        <form class="row g-3" action="{{route('Admin.HRAdmin.training.cost-breakdown.update',$costs->id)}}" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="training_id" value="{{$costs->training_id}}" hidden>
            <div class="col-md-6">
                <label for="airfare" class="form-label">Airfare</label>
                <input name="airfare" type="number" class="form-control track-change @error('airfare') is-invalid @enderror" required min="0" value="{{$costs->airfare}}">
                @error('airfare')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="subsistence" class="form-label">subsistence</label>
                <input name="subsistence" type="number" class="form-control track-change @error('subsistence') is-invalid @enderror" required min="0" value="{{$costs->subsistence}}">
                @error('subsistence')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="incidental" class="form-label">incidental</label>
                <input name="incidental" type="number" class="form-control track-change @error('incidental') is-invalid @enderror" required min="0" value="{{$costs->incidental}}">
                @error('incidental')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="registration" class="form-label">registration</label>
                <input name="registration" type="number" class="form-control track-change @error('registration') is-invalid @enderror" required min="0" value="{{$costs->registration}}">
                @error('registration')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="visa" class="form-label">visa</label>
                <input name="visa" type="number" class="form-control track-change @error('visa') is-invalid @enderror" required min="0" value="{{$costs->visa}}">
                @error('visa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="insurance" class="form-label">insurance</label>
                <input name="insurance" type="number" class="form-control track-change @error('insurance') is-invalid @enderror" required min="0" value="{{$costs->insurance}}">
                @error('insurance')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="warm_clothes" class="form-label">warm_clothes</label>
                <input name="warm_clothes" type="number" class="form-control track-change @error('warm_clothes') is-invalid @enderror" required min="0" value="{{$costs->warm_clothes}}">
                @error('warm_clothes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
        
    </div>
 </div>
@endsection
