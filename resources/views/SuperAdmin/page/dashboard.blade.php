@extends('SuperAdmin.index')
@section('content')
<h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>

<div class="row">
    <div class="col-xl-6 col-xxl-5 d-flex">
        <div class="w-100">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Trainings</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="book-open"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3">{{$totalTraining}}</h1>
                            <div class="mb-0">
                                <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> {{$trainingPercentage}}% </span>
                                <span class="text-muted">This year {{$currentYear}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Participants</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="users"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3">{{$totalParticipants}}</h1>
                            <div class="mb-0">
                                <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> {{$Participantspercentage}}% </span>
                                <span class="text-muted">This year {{$currentYear}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Foreign/Local Budget Utilization</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3">Rs.{{$totalCost}}</h1>
                            <div class="mb-0">
                                <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> {{$budgetUtilization}}% </span>
                                <span class="text-muted">This year {{$currentYear}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Attendence</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3">{{$totalAttendentParticipants}}</h1>
                            <div class="mb-0">
                                <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> {{$attendentParticipantsPercentage}}% </span>
                                <span class="text-muted">This year {{$currentYear}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xxl-3 d-flex order-1 order-xxl-1">
        <div class="card flex-fill">
            <div class="card-header">

                <h5 class="card-title mb-0">Calendar</h5>
            </div>
            <div class="card-body d-flex">
                <div class="align-self-center w-100">
                    <div class="chart">
                        <div id="datetimepicker-dashboard"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    
    <div class="col-12 col-md-6 col-xxl-3 d-flex order-2 order-xxl-3">
        <div class="card flex-fill w-100">
            <div class="card-header">

                <h5 class="card-title mb-0">Local Budget</h5>
            </div>
            <div class="card-body d-flex">
                <div class="align-self-center w-100">
                    <div class="py-3">
                        <div class="chart chart-xs">
                            <canvas id="chartjs-local-budget-pie"></canvas>
                        </div>
                    </div>

                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Initial</td>
                                <td class="text-end">{{$localBudget}}</td>
                            </tr>
                            <tr>
                                <td>Transfer</td>
                                <td class="text-end">{{$localTransferBudget}}</td>
                            </tr>
                            <tr>
                                <td>Total Local Budget</td>
                                <td class="text-end">{{ (float)$localBudget + (float)$localTransferBudget }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xxl-3 d-flex order-2 order-xxl-3">
        <div class="card flex-fill w-100">
            <div class="card-header">

                <h5 class="card-title mb-0">Foreign Budget</h5>
            </div>
            <div class="card-body d-flex">
                <div class="align-self-center w-100">
                    <div class="py-3">
                        <div class="chart chart-xs">
                            <canvas id="chartjs-foreign-budget-pie"></canvas>
                        </div>
                    </div>

                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Initial</td>
                                <td class="text-end">{{$foreignBudget}}</td>
                            </tr>
                            <tr>
                                <td>Transfer</td>
                                <td class="text-end">{{$foreignTransferBudget}}</td>
                            </tr>
                            <tr>
                                <td>Total Foreign Budget</td>
                                <td class="text-end">{{ (float)$foreignBudget + (float)$foreignTransferBudget }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 col-xxl-9 d-flex">
        <div class="card flex-fill">
            <div class="card-header">

                <h5 class="card-title mb-0">Latest Trainings</h5>
            </div>
            <table class="table table-hover my-0">
                <thead>
                    <tr>
                        <th>Training Name</th>
                        <th class="d-none d-xl-table-cell">Start Date</th>
                        <th class="d-none d-xl-table-cell">End Date</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Participants</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestTrainings as $training)
                        <tr>
                            <td>{{$training->training_name}}</td>
                            <td class="d-none d-xl-table-cell">{{$training->training_period_from->format('Y-m-d')}}</td>
                            <td class="d-none d-xl-table-cell">{{date($training->training_period_to->format('Y-m-d'))}}</td>
                            @if ($training->training_status == '1')
                                <td>Completed</td>
                            @else
                                <td>Not Completed</td>    
                            @endif
                            
                            <td class="text-center">{{ $training->participants_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-12 col-lg-4 col-xxl-3 d-flex">
        <div class="card flex-fill w-100">
            <div class="card-header">

                <h5 class="card-title mb-0">Monthly Trainings</h5>
            </div>
            <div class="card-body d-flex w-100">
                <div class="align-self-center chart chart-lg">
                    <canvas id="chartjs-trainings-bar"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Start Date - Green Highlight */
    .flatpickr-day.start-date {
        background: #28a745;
        color: white;
        border-color: #28a745;
        font-weight: bold;
    }
    
    /* End Date - Red Highlight */
    .flatpickr-day.end-date {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
        font-weight: bold;
    }
    
    /* Dates that are both start and end */
    .flatpickr-day.start-date.end-date {
        background: linear-gradient(135deg, #28a745 50%, #dc3545 50%);
    }
    
    /* Hover States */
    .flatpickr-day.start-date:hover,
    .flatpickr-day.end-date:hover {
        background: #333;
        color: white;
    }
</style>
<!-- local budget pie chart -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Pass PHP data to JavaScript safely
        const budgetData = {
            localBudget: @json($localBudget),
            localTransferBudget: @json($localTransferBudget),
            localTrainingCost: @json($localTrainingCost)
        };

        // Initialize pie chart
        new Chart(document.getElementById("chartjs-local-budget-pie"), {
            type: "pie",
            data: {
                labels: ["Initial", "Transfer", "Utilization"],
                datasets: [{
                    data: [
                        budgetData.localBudget,
                        budgetData.localTransferBudget,
                        budgetData.localTrainingCost
                    ],
                    backgroundColor: [
                        window.theme.primary,
                        window.theme.warning,
                        window.theme.danger
                    ],
                    borderWidth: 5
                }]
            },
            options: {
                responsive: !window.MSInputMethodContext,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                cutoutPercentage: 75
            }
        });
    });
</script>
<!-- foreign budget pie chart -->
<script>
    
    document.addEventListener("DOMContentLoaded", function() {
        const budgetData = @json([
            'foreignBudget' => $foreignBudget,
            'foreignTransferBudget' => $foreignTransferBudget,
            'foreignTrainingCost' => $foreignTrainingCost
        ]);
        // Pie chart
        new Chart(document.getElementById("chartjs-foreign-budget-pie"), {
            type: "pie",
            data: {
                labels: ["Initial", "Transfer", "Utilization"],
                datasets: [{
                    data: [budgetData.foreignBudget, budgetData.foreignTransferBudget, budgetData.foreignTrainingCost],
                    backgroundColor: [
                        window.theme.primary,
                        window.theme.warning,
                        window.theme.danger
                    ],
                    borderWidth: 5
                }]
            },
            options: {
                responsive: !window.MSInputMethodContext,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                cutoutPercentage: 75
            }
        });
    });
</script>
<!-- monthly trainings bar chart -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Convert PHP array to JavaScript
        const monthlyCounts = @json($monthlyTrainingCounts);

        // Bar chart
        new Chart(document.getElementById("chartjs-trainings-bar"), {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "This year",
                    backgroundColor: window.theme.primary,
                    borderColor: window.theme.primary,
                    hoverBackgroundColor: window.theme.primary,
                    hoverBorderColor: window.theme.primary,
                    data: monthlyCounts,
                    barPercentage: .75,
                    categoryPercentage: .5
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: false
                        },
                        stacked: false,
                        ticks: {
                            beginAtZero: true, // Start from 0
                            stepSize: 5 // Adjust based on your data range
                        }
                    }],
                    xAxes: [{
                        stacked: false,
                        gridLines: {
                            color: "transparent"
                        }
                    }]
                }
            }
        });
    });
</script>
<!-- calender -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const startDates = @json($startDates);
        const endDates = @json($endDates);

        // Debugging - check what dates were passed
        console.log("Start Dates from server:", startDates);
        console.log("End Dates from server:", endDates);

        document.getElementById("datetimepicker-dashboard").flatpickr({
            inline: true,
            prevArrow: "<span title=\"Previous month\">&laquo;</span>",
            nextArrow: "<span title=\"Next month\">&raquo;</span>",
            defaultDate: new Date(),
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                // Get date in YYYY-MM-DD format (consistent with PHP)
                const dateStr = dayElem.dateObj.getFullYear() + '-' + 
                              String(dayElem.dateObj.getMonth() + 1).padStart(2, '0') + '-' + 
                              String(dayElem.dateObj.getDate()).padStart(2, '0');

                // Debug current date being checked
                console.log("Processing date:", dateStr);
                
                if (startDates.includes(dateStr)) {
                    console.log("Marking as start date:", dateStr);
                    dayElem.classList.add('start-date');
                }
                
                if (endDates.includes(dateStr)) {
                    console.log("Marking as end date:", dateStr);
                    dayElem.classList.add('end-date');
                }
            }
        });
    });
</script>
@endsection