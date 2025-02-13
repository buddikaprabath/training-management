@extends('SuperAdmin.index')
@section('content')


<div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($training) ? 'Training Details' : 'Training Details' }}</p>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('SuperAdmin.training.Detail') }}" class="text-white text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle w-75 h-75">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 8 8 12 12 16"></polyline>
                    <line x1="16" y1="12" x2="8" y2="12"></line>
                </svg>
            </a>
        </button>
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

    <div class="row">
        <!-- Left Section -->
        <div class="col-md-6" >
            <div class="card p-3" style="background-color: #A8BDDB;">
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Unique Identifier : {{$training->id}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Code : {{$training->training_code}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Mode Of Delivery : {{$training->mode_of_delivery}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Period From : {{$training->training_period_from}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Period To : {{$training->training_period_to}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Total Training Hours : {{$training->total_training_hours}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Total Program Cost : {{$training->total_program_cost}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Batch Size : {{$training->batch_size}}</span>
            </div>
        </div>

        <!-- Right Section -->
        <div class="col-md-6">
            <div class="card p-3" style="background-color: #A8BDDB;">
                @foreach ($institutes as $institute)
                    <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Institute Name: {{ $institute->name }}</span>
                @endforeach
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Course Type : {{$training->course_type}}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Country : {{ $training->country }}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Structure : {{ $training->training_structure }}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Expiration Date : {{ $training->exp_date }}</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Category : {{ $training->category }}</span>
                @if($training)
                    @foreach ($training->remarks as $remark)
                        <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Other Comments: {{ $remark->remark }}</span>
                    @endforeach
                @endif
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Custodian : {{ $training->training_custodian }}</span>
            </div>
        </div>
    </div>




    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($training) ? 'Participant Details' : 'Participant Details' }}</p>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center align-top">Unique Identifier</th>
                        <th class="text-center align-top">Training Code</th>
                        <th class="text-center align-top">Participant Name</th>
                        <th class="text-center align-top">Training Division</th>
                        <th class="text-center align-top">Course Type</th>
                        <th class="text-center align-top">Designation</th>
                        <th class="text-center align-top">Status</th>
                        <th class="text-center align-top">Add Document</th>
                        <th class="text-center align-top">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                        <tr>
                            <td class="text-center">{{ $participant->id }}</td>
                            <td class="text-center">{{ $participant->epf_number }}</td>
                            <td class="text-center">{{ $participant->name }}</td>
                            <td class="text-center">{{ $participant->designation }}</td>
                            <td class="text-center">{{ $participant->location }}</td>
                            <td class="text-center">{{ $participant->salary_scale }}</td>
                            <td class="text-center">{{ $participant->status ?? 'Pending' }}</td>
                            <td class="text-center">
                                <a href="#">
                                    <i data-feather="file-text"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{route('SuperAdmin.participant.edit',$participant->id)}}">
                                    <i data-feather="edit"></i>
                                </a>
                                <a href="#">
                                    <i data-feather="trash-2"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
            </table>
        </div>
    </div>

    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($training) ? 'Document Details' : 'Document Details' }}</p>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center align-top">Name</th>
                        <th class="text-center align-top">Status</th>
                        <th class="text-center align-top">Date Submitted</th>
                        <th class="text-center align-top">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($training as $document)
                        <tr>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</div>


@endsection
