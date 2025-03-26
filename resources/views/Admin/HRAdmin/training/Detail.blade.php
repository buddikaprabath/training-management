@extends('Admin.HRAdmin.index')

@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0 fw-bold">Training Details</p>

        <!-- Search Form -->
        <form class="d-flex" method="GET" action="#">
            <input class="form-control me-2" type="search" name="query" placeholder="Search here....." value="{{ request('query') }}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <a href="{{ route('Admin.HRAdmin.training.create') }}" class="text-decoration-none">
            <button type="button" class="btn btn-primary d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                Create Training
            </button>
        </a>
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

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center align-top">Training Name</th>
                        <th class="text-center align-top">Mode of Delivery</th>
                        <th class="text-center align-top">Total Training Hours</th>
                        <th class="text-center align-top">Total Program Cost</th>
                        <th class="text-center align-top">Division</th>
                        <th class="text-center align-top">Batch Size</th>
                        <th class="text-center align-top">Category</th>
                        <th class="text-center align-top">Training Status</th>
                        <th class="text-center align-top">Subject</th>
                        <th class="text-center align-top">Participant</th>
                        <th class="text-center align-top">Cost Breakdown</th>
                        <th class="text-center align-top">Add Document</th>
                        <th class="text-center align-top">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($item))
                        <div class="alert alert-warning">
                            No training records found for your division and section.
                        </div>
                    @else
                        @foreach($training as $item) <!-- Loop through each training item -->
                            <tr>
                                <td class="text-center">{{ $item->training_name }}</td>
                                <td class="text-center">{{ $item->mode_of_delivery }}</td>
                                <td class="text-center">{{ $item->total_training_hours }}</td>
                                <td class="text-center">{{ $item->total_program_cost }}</td>
                                <td class="text-center">{{ $item->division_name }}</td>
                                <td class="text-center">{{ $item->batch_size }}</td>
                                <td class="text-center">{{ $item->category }}</td>
                                <td class="text-center">
                                    <a href="#" 
                                    class="open-status-modal" 
                                    data-training-id="{{ $item->id }}"
                                    @if($item->training_status) 
                                        style="pointer-events: none; color: green;" 
                                    @endif
                                    >
                                        @if($item->training_status)  <!-- Check if training_status is true -->
                                            <span class="completed-status">Completed</span> <!-- Display "Completed" -->
                                        @else
                                            <i data-feather="check-circle" class="check-icon" id="check-icon-{{ $item->id }}"></i>
                                        @endif
                                    </a>
                                </td>

                                <!-- Display Subject Modal -->
                                @if ($item->division_id == 2)
                                    <td class="text-center">
                                        <a href="#" class="open-subject-modal" data-bs-toggle="modal" data-bs-target="#subjectModal" data-training-id="{{ $item->id }}">
                                            <i data-feather="book-open"></i>
                                        </a>
                                @else
                                    <td class="text-center"></td>
                                @endif
                                
                                <td class="text-center">
                                    <a href="{{ route('Admin.HRAdmin.participant.Detail',$item->id) }}">
                                        <i data-feather="eye"></i>
                                    </a>

                                    <a href="{{route('Admin.HRAdmin.participant.create',$item->id)}}"><i data-feather="user-plus"></i></a>
                                </td>
                                <td class="text-center">
                                    <!-- For Cost Breakdown, Access Cost Breakdown for this specific training item -->
                                    <a href="#" class="open-cost-modal" data-training-id="{{ $item->id }}">
                                        <i data-feather="dollar-sign"></i>
                                    </a>
                                    <a href="{{route('Admin.HRAdmin.training.costDetail',$item->id)}}">
                                        <i data-feather="eye"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal" data-training-id="{{ $item->id }}">
                                        <i data-feather="file-text"></i>
                                    </a>
                                </td>                            
                                <td class="text-center">
                                    <a href="{{ route('Admin.HRAdmin.training.edit', $item->id) }}" style="display: inline-block; vertical-align: middle;">
                                        <i data-feather="edit" class="text-primary"></i>
                                    </a>
                                    
                                    <form action="{{ route('Admin.HRAdmin.training.Training.delete', $item->id) }}" method="POST" 
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
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="pagination">
            {{ $training->links() }}
        </div>
    </div>
</div>



<!-- Modal for Cost Breakdown -->
<div class="modal fade" id="costModal" tabindex="-1" aria-labelledby="costModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="costModalLabel">Cost Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="costForm">
                    @csrf

                    <div class="container">
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Airfare</label>
                            <div class="col-md-6">
                                <input name="airfare" type="number" class="form-control cost-input"
                                    value="0.00" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Subsistence Including Travel Day</label>
                            <div class="col-md-6">
                                <input name="subsistence" type="number" class="form-control cost-input"
                                    value="0.00" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Incidental Including Travel Day</label>
                            <div class="col-md-6">
                                <input name="incidental" type="number" class="form-control cost-input"
                                    value="0.00" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Registration Fee</label>
                            <div class="col-md-6">
                                <input name="registration" type="number" class="form-control cost-input"
                                    value="0.00" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Visa Fee</label>
                            <div class="col-md-6">
                                <input name="visa" type="number" class="form-control cost-input"
                                    value="0.00" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Travel Insurance</label>
                            <div class="col-md-6">
                                <input name="insurance" type="number" class="form-control cost-input"
                                    value="0.00" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Warm Clothes</label>
                            <div class="col-md-6">
                                <input name="warm_clothes" type="number" class="form-control cost-input"
                                    value="0.00" min="0" required>
                            </div>
                        </div>

                        <!-- Total Amount (Read-Only) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label"><strong>Total Amount</strong></label>
                            <div class="col-md-6">
                                <input name="total_amount" type="number" class="form-control"
                                    value="0.00" readonly id="totalAmount">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="saveCost">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Training Status Modal -->
<div class="modal fade" id="trainingStatusModal" tabindex="-1" aria-labelledby="trainingStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Training Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="trainingStatusForm" method="POST" 
                @if (!empty($item)) 
                    action="{{ route('Admin.HRAdmin.training.update-status', $item->id) }}"
                @else
                    action="#" 
                @endif
                >
                    @csrf
                    @method('PUT')
                
                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="feedback_form">Feedback Form</label>
                        <input class="form-check-input status-switch" type="checkbox" id="feedback_form" name="feedback_form" 
                            @if(!empty($item) && $item->feedback_form == 1) checked @endif>
                    </div>
            
                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="e_report">E-Report</label>
                        <input class="form-check-input status-switch" type="checkbox" id="e_report" name="e_report" 
                            @if(!empty($item) && $item->e_report == 1) checked @endif>
                    </div>
            
                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="warm_clothe_allowance">Warm Clothes Allowance</label>
                        <input class="form-check-input status-switch" type="checkbox" id="warm_clothe_allowance" name="warm_clothe_allowance" 
                            @if(!empty($item) && $item->warm_clothe_allowance == 1) checked @endif>
                    </div>
            
                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="presentation">Presentation</label>
                        <input class="form-check-input status-switch" type="checkbox" id="presentation" name="presentation" 
                            @if(!empty($item) && $item->presentation == 1) checked @endif>
                    </div>
            
                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="training_status">Training Completed</label>
                        <input class="form-check-input" type="checkbox" id="training_status" name="training_status" 
                            @if(!empty($item) && $item->training_status == 1) checked @endif>
                    </div>
            
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="saveStatus" 
                            @if(empty($item)) disabled @endif>Save</button>
                    </div>
                </form>
            </div>
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
            <form id="documentUploadForm" 
            @if(!empty($item)) 
            action="{{ route('Admin.HRAdmin.training.documents.store',$item->id) }}" 
            @else 
                action="#" 
            @endif 
            method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="training_id" id="training_id" 
                        value="{{ !empty($item) ? $item->id : '' }}">
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

<!-- Modal for Subject -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectModalLabel">Add Subjects</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="subjectForm" action="{{ route('Admin.HRAdmin.training.subject.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Hidden training ID field -->
                    <input type="hidden" name="training_id" id="subject_training_id">
                    
                    <!-- Default subject field -->
                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" id="subject_name" required>
                    </div>
                    
                    <!-- Dynamic subject fields will be added here -->
                    
                    <!-- Add more button -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-primary addmoreSubject">
                            <i data-feather="plus"></i> Add Another Subject
                        </button>
                        <small class="text-muted ms-2">(Maximum 15 subjects per training)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Subjects</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const costModalElement = document.getElementById("costModal");
        const uploadModalElement = document.getElementById("uploadDocumentModal");
        const costModal = new bootstrap.Modal(costModalElement);
        const trainingStatusModal = new bootstrap.Modal(document.getElementById("trainingStatusModal"));
        const costInputs = document.querySelectorAll(".cost-input");
        const totalAmountField = document.getElementById("totalAmount");

        // New functionality for adding dynamic subject input fields
        let subjectCounter = 1; // Start from 1 for dynamic fields (since default field exists)
        const maxFields = 15; // Maximum number of input fields
        const addButton = document.querySelector('.addmoreSubject'); // Plus icon button
        
        // Open the modal when the "book-open" icon is clicked
        const openModalButtons = document.querySelectorAll('.open-subject-modal');
        openModalButtons.forEach(button => {
            button.addEventListener('click', function() {
                const trainingId = this.getAttribute('data-training-id');
                document.getElementById('subject_training_id').value = trainingId;
                $('#subjectModal').modal('show'); // Using Bootstrap modal show
            });
        });

        // Add new input field when the plus icon is clicked
        if (addButton) {
            addButton.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default anchor behavior

                if (subjectCounter < maxFields) {
                    const newInputGroup = document.createElement('div');
                    newInputGroup.classList.add('mb-3');
                    // When adding a new field:
                    newInputGroup.innerHTML = `
                        <label for="subject_name_${subjectCounter}" class="form-label">Subject Name ${subjectCounter + 1}</label>
                        <input type="text" class="form-control" name="subject_name_${subjectCounter}" id="subject_name_${subjectCounter}" required>
                    `;
                                        
                    // Insert the new field before the container of the plus icon
                    this.parentElement.insertAdjacentElement('beforebegin', newInputGroup);
                    subjectCounter++; // Increment counter after adding a field
                    
                    // Re-initialize Feather icons after adding new elements
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                } else {
                    alert("You can add a maximum of 15 subjects.");
                }
            });
        }


        function initializeModals() {
            document.querySelectorAll(".open-cost-modal").forEach(button => {
                button.addEventListener("click", function () {
                    const trainingId = this.dataset.trainingId;
                    const costForm = document.getElementById("costForm");
                    if (costForm) {
                        costForm.action = `/Admin/HRAdmin/training/cost-breakdown/store/${trainingId}`;
                        costModal.show();
                    }
                });
            });

            document.querySelectorAll(".open-status-modal").forEach(button => {
                button.addEventListener("click", function () {
                    const trainingId = this.dataset.trainingId;
                    document.getElementById("trainingStatusForm").action = `/Admin/HRAdmin/training/update-status/${trainingId}`;
                    trainingStatusModal.show();
                });
            });

            document.getElementById("saveCost").addEventListener("click", function () {
                console.log("Cost details saved!");
                costModal.hide();
            });

            // Handle document upload modal
            uploadModalElement.addEventListener("show.bs.modal", function(event) {
                const button = event.relatedTarget; // Button that triggered the modal
                const trainingId = button.getAttribute("data-training-id");
                document.getElementById("training_id").value = trainingId;
            });
        }

        function calculateTotal() {
            const total = [...costInputs].reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
            totalAmountField.value = total.toFixed(2);
        }

        function attachCostCalculation() {
            costInputs.forEach(input => input.addEventListener("input", calculateTotal));
            calculateTotal(); // Initial calculation
        }

        function initFeatherIcons() {
            feather.replace();
        }

        function handleTrainingStatusForm() {
            document.getElementById("saveStatus").addEventListener("click", function () {
                let form = document.getElementById("trainingStatusForm");

                // Check if all checkboxes are checked
                const allChecked = [...document.querySelectorAll(".status-switch")].every(input => input.checked);

                let trainingStatusField = document.getElementById("training_status");
                if (allChecked) {
                    trainingStatusField.checked = true;
                    trainingStatusField.value = 1;
                } else {
                    trainingStatusField.checked = false;
                    trainingStatusField.value = 0;
                }

                // Enable 'training_status' before submitting
                trainingStatusField.disabled = false;

                // Convert all checkbox values to 1 or 0 before submission
                document.querySelectorAll(".status-switch").forEach(input => {
                    input.value = input.checked ? 1 : 0;
                });

                // Submit the form
                form.submit();
            });
        }

        // Initialize all functionalities
        function init() {
            initializeModals();
            attachCostCalculation();
            initFeatherIcons();
            handleTrainingStatusForm();
        }

        init();
    });
</script>



@endsection
