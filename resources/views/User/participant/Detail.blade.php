@extends('User.index')
@section('content')


<div class="card mt-2 ml-5 mr-5 mb-5 bg-gray-50 p-3 rounded-md shadow-lg">
    <div class="card-header d-flex justify-content-between">
        <p class="fw-bold">{{ isset($training) ? 'Training Details' : 'Training Details' }}</p>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <button id="backButton" style="border: none; background: transparent; padding: 0;" type="button">
            <a href="{{ route('User.training.Detail') }}" class="text-white text-decoration-none">
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
                        <th class="text-center align-top">EPF Number</th>
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
                                <a href="#" class="upload-document-btn" data-participant-id="{{ $participant->id }}" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                    <i data-feather="file-text"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{route('User.participant.edit',$participant->id)}}">
                                    <i data-feather="edit"></i>
                                </a>
                                <form action="{{ route('User.participant.delete', $participant->id) }}" method="POST" 
                                    style="display: inline-block; vertical-align: middle; margin-left: 5px;"
                                    onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                                        <i data-feather="trash-2" class="text-primary"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
            </table>
            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination d-flex align-items-end flex-column mb-3">
                    {{ $participants->links('pagination::bootstrap-4') }}
                </ul>
            </nav>
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
                        <th class="text-center align-top">File Path</th>
                        <th class="text-center align-top">Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $document)
                        <tr>
                            <td class="text-center">{{ $document->name }}</td>
                            <td class="text-center">{{ $document->file_path}}</td>
                            <td class="text-center">{{ $document->date_of_submitting }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for File Upload -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="documentUploadForm" action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="participant_id" id="participant_id">
                    <input type="hidden" name="training_id" id="training_id">

                    <div class="mb-3">
                        <label for="document_name" class="form-label">Document Name</label>
                        <input type="text" class="form-control" name="name" id="document_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="submit_date" class="form-label">Date of Submitting</label>
                        <input type="date" class="form-control" name="date_of_submitting" id="date_of_submitting" required>
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Choose File</label>
                        <input type="file" class="form-control" name="document_file" id="document" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var uploadModal = document.getElementById("uploadDocumentModal");
        uploadModal.addEventListener("show.bs.modal", function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var participantId = button.getAttribute("data-participant-id");

            // Set the action URL dynamically for the correct participant
            var formAction = "{{ route('User.participant.documents.store', ':id') }}";
            formAction = formAction.replace(':id', participantId);
            document.getElementById("documentUploadForm").action = formAction;

            // Ensure the correct training ID is set in the hidden input field
            var trainingId = "{{ $training->id }}"; // Ensure this is available in your Blade template
            document.getElementById("training_id").value = trainingId;  
        });
    });

</script>
@endsection
