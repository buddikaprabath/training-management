@extends('SuperAdmin.index')

@section('content')

<div class="card">
    <div class="m-3 d-flex justify-content-between align-items-center">
        <p class="p-1 m-0 fw-bold">Training Details</p>

        <!-- Search Form -->
        <form class="d-flex" method="GET" action="#">
            <input class="form-control me-2" type="search" name="query" placeholder="Enter training name" value="{{ request('query') }}">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <a href="{{ url()->current() }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
        </a>
        <a href="{{ route('SuperAdmin.training.create') }}" class="text-decoration-none">
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
                        <th class="text-center align-top">Participant</th>
                        <th class="text-center align-top">Cost Breakdown</th>
                        <th class="text-center align-top">Add Document</th>
                        <th class="text-center align-top">Action</th>
                    </tr>
                </thead>
                <tbody>
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
                                <a href="#" class="open-status-modal" data-training-id="{{ $item->id }}">
                                    <i data-feather="check-circle" class="check-icon" id="check-icon-{{ $item->id }}"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('SuperAdmin.participant.Detail',$item->id) }}">
                                    <i data-feather="eye"></i>
                                </a>

                                <a href="{{route('SuperAdmin.participant.create',$item->id)}}"><i data-feather="user-plus"></i></a>
                            </td>
                            <td class="text-center">
                                <!-- For Cost Breakdown, Access Cost Breakdown for this specific training item -->
                                <a href="#" class="open-cost-modal" data-training-id="{{ $item->id }}">
                                    <i data-feather="dollar-sign"></i>
                                </a>
                                <a href="{{ route('SuperAdmin.training.edit', $item->id) }}">
                                    <i data-feather="edit"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="#">
                                    <i data-feather="file-text"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('SuperAdmin.training.edit', $item->id) }}" style="display: inline-block; vertical-align: middle;">
                                    <i data-feather="edit"></i>
                                </a>
                                
                                <form action="{{ route('SuperAdmin.training.Training.delete', $item->id) }}" method="POST" style="display: inline-block; vertical-align: middle; margin-left: 5px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
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
                                    value="0.00" min="0">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Subsistence Including Travel Day</label>
                            <div class="col-md-6">
                                <input name="subsistence" type="number" class="form-control cost-input"
                                    value="0.00" min="0">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Incidental Including Travel Day</label>
                            <div class="col-md-6">
                                <input name="incidental" type="number" class="form-control cost-input"
                                    value="0.00" min="0">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Registration Fee</label>
                            <div class="col-md-6">
                                <input name="registration" type="number" class="form-control cost-input"
                                    value="0.00" min="0">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Visa Fee</label>
                            <div class="col-md-6">
                                <input name="visa" type="number" class="form-control cost-input"
                                    value="0.00" min="0">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Travel Insurance</label>
                            <div class="col-md-6">
                                <input name="insurance" type="number" class="form-control cost-input"
                                    value="0.00" min="0">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-6 col-form-label">Warm Clothes</label>
                            <div class="col-md-6">
                                <input name="warm_clothes" type="number" class="form-control cost-input"
                                    value="0.00" min="0">
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
                <form id="trainingStatusForm">
                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="feedback_form">Feedback Form</label>
                        <input class="form-check-input status-switch" type="checkbox" id="feedback_form" name="feedback_form">
                    </div>

                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="e_report">E-Report</label>
                        <input class="form-check-input status-switch" type="checkbox" id="e_report" name="e_report">
                    </div>

                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="warm_clothe_allowance">Warm Clothes Allowance</label>
                        <input class="form-check-input status-switch" type="checkbox" id="warm_clothe_allowance" name="warm_clothe_allowance">
                    </div>

                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="presentation">Presentation</label>
                        <input class="form-check-input status-switch" type="checkbox" id="presentation" name="presentation">
                    </div>

                    <div class="form-check form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="training_status">Training Completed</label>
                        <input class="form-check-input" type="checkbox" id="training_status" name="training_status" disabled>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveStatus">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const costModalElement = document.getElementById("costModal");
        const trainingModalElement = document.getElementById("trainingStatusModal");
        const costModal = new bootstrap.Modal(costModalElement);
        const trainingModal = new bootstrap.Modal(trainingModalElement);
        const costInputs = document.querySelectorAll(".cost-input");
        const totalAmountField = document.getElementById("totalAmount");
        const saveButton = document.getElementById("saveStatus");
        const trainingCompletedSwitch = document.getElementById("training_status");
        const switches = document.querySelectorAll(".status-switch");

        function initializeModals() {
            document.querySelectorAll(".open-cost-modal").forEach(button => {
                button.addEventListener("click", function () {
                    const trainingId = this.dataset.trainingId;
                    const costForm = document.getElementById("costForm");
                    if (costForm) {
                        costForm.action = `/SuperAdmin/training/cost-breakdown/store/${trainingId}`;
                        costModal.show();
                    }
                });
            });

            document.getElementById("saveCost").addEventListener("click", function () {
                console.log("Cost details saved!");
                costModal.hide();
            });

            document.querySelectorAll(".open-status-modal").forEach(button => {
                button.addEventListener("click", function () {
                    const trainingId = this.dataset.trainingId;
                    trainingModalElement.setAttribute("data-training-id", trainingId);
                    trainingModal.show();
                });
            });

            saveButton.addEventListener("click", () => trainingModal.hide());
        }

        function calculateTotal() {
            const total = [...costInputs].reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
            totalAmountField.value = total.toFixed(2);
        }

        function attachCostCalculation() {
            costInputs.forEach(input => input.addEventListener("input", calculateTotal));
            calculateTotal(); // Initial calculation
        }

        function checkTrainingCompleted() {
            const allOn = [...switches].every(sw => sw.checked);
            trainingCompletedSwitch.disabled = !allOn;
            saveButton.textContent = allOn && trainingCompletedSwitch.checked ? "Completed" : "Save";
        }

        function handleTrainingStatus() {
            switches.forEach(sw => sw.addEventListener("change", checkTrainingCompleted));

            trainingCompletedSwitch.addEventListener("change", function () {
                const trainingId = trainingModalElement.getAttribute("data-training-id");
                const icon = document.getElementById(`check-icon-${trainingId}`);
                icon.style.color = this.checked ? "green" : "";
            });
        }

        function initFeatherIcons() {
            feather.replace();
        }

        // Initialize all functionalities
        function init() {
            initializeModals();
            attachCostCalculation();
            handleTrainingStatus();
            initFeatherIcons();
        }

        init();
    });
</script>


@endsection
