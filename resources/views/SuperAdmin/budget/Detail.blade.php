@extends('SuperAdmin.index')
@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0">Budget Details</p>
        <a href="{{route('SuperAdmin.budget.Create')}}" style="text-decoration: none">
         <button type="button" class="btn btn-primary d-flex align-items-center">
            Create New Budget
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
    <div class="m-3 d-flex justify-content-between align-items-center">
        <div class="col-md-6 ">
            <label for="division_id" class="form-label">Division</label>
            <div class="d-flex justify-content-between align-items-center w-50">
            <select name="division_id" id="division" class=" form-select track-change @error('division_id') is-invalid @enderror" required>
                <option selected disabled>Choose...</option>
                <option value="1" {{ old('division_id', isset($training) && $training->division_id == 1 ? 'selected' : '') }}>HR</option>
                <option value="2" {{ old('division_id', isset($training) && $training->division_id == 2 ? 'selected' : '') }}>CATC</option>
                <option value="3" {{ old('division_id', isset($training) && $training->division_id == 3 ? 'selected' : '') }}>IT</option>
                <option value="4" {{ old('division_id', isset($training) && $training->division_id == 4 ? 'selected' : '') }}>FINANCE</option>
                <option value="5" {{ old('division_id', isset($training) && $training->division_id == 5 ? 'selected' : '') }}>SCM</option>
                <option value="6" {{ old('division_id', isset($training) && $training->division_id == 6 ? 'selected' : '') }}>MARKETING</option>
                <option value="7" {{ old('division_id', isset($training) && $training->division_id == 7 ? 'selected' : '') }}>SECURITY</option>
            </select>
            @error('division_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <button type="button" class="ms-3 btn btn-primary d-flex align-items-center">
                Filter
             </button>
            </div>
        </div>
    </div>
    <div class="card p-2">
        <div class="table-responsive">
          <table class="table">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Division</th>
                </tr>
            </thead>
            <tbody>
                <td>2025</td>
                <td>Start Budget Allocation</td>
                <td>35000000</td>
                <td>HR</td>
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
