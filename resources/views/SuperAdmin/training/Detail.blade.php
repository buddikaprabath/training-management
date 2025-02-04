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
                        <th class="text-center align-top">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">AASL</td>
                        <td class="text-center">Online</td>
                        <td class="text-center">40</td>
                        <td class="text-center">6,500,000</td>
                        <td class="text-center">IT</td>
                        <td class="text-center">25</td>
                        <td class="text-center">Development</td>
                        <td class="text-center">
                            <a href="#" class="open-modal" data-training-id="1">
                                <i data-feather="check-circle"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="#"><i data-feather="eye"></i></a>
                            <a href="#"><i data-feather="user-plus"></i></a>
                        </td>
                        <td class="text-center">
                            <a href="#"><i data-feather="dollar-sign"></i></a>
                        </td>
                        <td class="text-center">
                            <a href="#"><i data-feather="edit"></i></a>
                            <a href="#"><i data-feather="trash-2"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end mx-3">
                <!-- Dynamic pagination links go here -->
            </ul>
        </nav>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="trainingModal" tabindex="-1" aria-labelledby="trainingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trainingModalLabel">Complete Training Tasks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="trainingForm">
                    <input type="hidden" id="trainingId" name="training_id">

                    @foreach (['Training Status', 'Feedback Form', 'E-Report', 'Warm Clothes Allowance', 'Presentation'] as $index => $task)
                        <div class="form-check">
                            <label class="form-check-label" for="task{{ $index }}">{{ $task }}</label>
                            <input class="form-check-input task-checkbox" type="checkbox" id="task{{ $index }}">
                        </div>
                    @endforeach

                    <div class="form-check mt-3">
                        <label class="form-check-label fw-bold" for="trainingCompleted">Training Completed</label>
                        <input class="form-check-input" type="checkbox" id="trainingCompleted" disabled>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveTraining">Save</button>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        let trainingModalElement = document.getElementById("trainingModal");
        let trainingModal = new bootstrap.Modal(trainingModalElement);

        // Open modal and set training ID
        document.querySelectorAll(".open-modal").forEach(button => {
            button.addEventListener("click", function () {
                let trainingId = this.getAttribute("data-training-id");
                document.getElementById("trainingId").value = trainingId;
                trainingModal.show(); // Open the modal
            });
        });

        // Enable 'Training Completed' checkbox only when all tasks are checked
        document.querySelectorAll(".task-checkbox").forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                let allChecked = [...document.querySelectorAll(".task-checkbox")].every(cb => cb.checked);
                document.getElementById("trainingCompleted").disabled = !allChecked;
            });
        });

        // Save UI changes without sending data
        document.getElementById("saveTraining").addEventListener("click", function () {
            let trainingId = document.getElementById("trainingId").value;
            let completed = document.getElementById("trainingCompleted").checked;

            if (completed) {
                // Change the "check-circle" icon to green
                let checkIcon = document.querySelector(`.open-modal[data-training-id="${trainingId}"] i`);
                if (checkIcon) {
                    checkIcon.style.color = "green";  // Change color to green
                }
            }

            // Close the modal properly
            trainingModal.hide();

            // Fix backdrop issue
            setTimeout(() => {
                document.querySelector('.modal-backdrop')?.remove();
                document.body.classList.remove('modal-open');
            }, 500);
        });

        feather.replace(); // Initialize Feather icons
    });
</script>




@endsection
