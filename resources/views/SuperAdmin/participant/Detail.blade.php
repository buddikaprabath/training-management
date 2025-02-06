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
                <p><strong>Unique Identifier:</strong> UI-001</p>
                <p><strong>Training Code:</strong> TC-001</p>
                <p><strong>Mode Of Delivery:</strong> In Person</p>
                <p><strong>Training Period From:</strong> 2025-01-01</p>
                <p><strong>Training Period To:</strong> 2026-01-01</p>
                <p><strong>Total Training Hours:</strong> 08</p>
                <p><strong>Total Program Cost:</strong> 50000</p>
                <p><strong>Batch Size:</strong> 100</p>
            </div>
        </div>

        <!-- Right Section -->
        <div class="col-md-6">
            <div class="card p-3" style="background-color: #A8BDDB;">
                <p><strong>Awarding Institute:</strong> AASL</p>
                <p><strong>Course Type:</strong> Local Out Side</p>
                <p><strong>Country:</strong> Sri Lanka</p>
                <p><strong>Training Structure:</strong> One Time</p>
                <p><strong>Expiration Date:</strong> -</p>
                <p><strong>Category:</strong> Seminar</p>
                <p><strong>Other Comments:</strong> Good Student</p>
                <p><strong>Training Custodian:</strong> AASL</p>
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
                    @foreach($training as $participant)
                        <tr>
                            <td class="text-center">Ul-001</td>
                            <td class="text-center">TC-001</td>
                            <td class="text-center">John</td>
                            <td class="text-center">HR</td>
                            <td class="text-center">Local Outside</td>
                            <td class="text-center">Engineer</td>
                            <td class="text-center">Completed</td>
                            <td class="text-center">

                                <a href="#">
                                    <i data-feather="dollar-sign"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="#">
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
