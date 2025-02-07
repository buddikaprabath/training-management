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
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Unique Identifier : UI-001</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Code : TC-001</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Mode Of Delivery : In Person</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Period From : 2025-01-01</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Period To : 2026-01-01</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Total Training Hours : 08</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Total Program Cost : 50000</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Batch Size : 100</span>
            </div>
        </div>

        <!-- Right Section -->
        <div class="col-md-6">
            <div class="card p-3" style="background-color: #A8BDDB;">
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Awarding Institute : AASL</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Course Type : Local Out Side</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Country : Sri Lanka</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Structure : One Time</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Expiration Date :</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Category : Seminar</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Other Comments : Good Student</span>
                <span class="bg-light text-dark rounded-pill d-block p-2 mb-2">Training Custodian : AASL</span>
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
                                    <i data-feather="file-text"></i>
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
