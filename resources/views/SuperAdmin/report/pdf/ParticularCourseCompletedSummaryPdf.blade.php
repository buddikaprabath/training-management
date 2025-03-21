<!DOCTYPE html>
<html>
<head>
    <title>Particular Course Completed Summary</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0 40px; /* Add left and right margin for binding */
        }

        /* Logo Styles */
        .logo-container {
            text-align: center; /* Center the logo */
            margin-bottom: 20px; /* Add space below the logo */
        }

        .logo {
            width: 250px;
            display: inline-block; /* Ensure the logo is centered */
        }

        /* Timestamp Styles */
        .timestamp {
            position: absolute;
            top: 20px;
            right: 40px; /* Align to the top-right corner */
            font-size: 14px;
        }

        /* Header Styles */
        .header {
            text-align: center; /* Center all headings */
            margin-bottom: 20px; /* Add space below the header */
        }

        .header h1, .header h3 {
            margin: 5px 0; /* Reduce margin between headings */
        }
        /* Divider Styles */
        .employee-details {
            margin-bottom: 20px; /* Add space below the employee details */
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px; /* Add space between rows */
        }

        .detail-row .label {
            font-weight: bold;
            width: 150px; /* Fixed width for labels */
        }

        .detail-row .value {
            flex: 1; /* Allow value to take remaining space */
        }
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px; /* Add space below the table */
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Footer Styles */
        footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px; /* Add space above the footer */
        }
    </style>
</head>
<body>
    <!-- Timestamp -->
    <p class="timestamp">Generated on: {{ \Carbon\Carbon::now()->format('Y.m.d') }}</p>

    <!-- Logo -->
    <div class="logo-container">
        <img src="{{ public_path('image/log.png') }}" class="logo" alt="Company Logo">
    </div>

    <!-- Header -->
    <div class="header">
        <h1>Training And Development Department</h1>
        <h3>Individual Employee Training Record</h3>
    </div>
    <!-- Display employee details using the first participant -->
    @if($trainings->isNotEmpty())
        <!-- Display employee details using the first participant -->
        @php
            $firsttraining = $trainings->first();
        @endphp
        <div class="employee-details">
            <div class="detail-row">
                <span class="label">Course Name:</span>
                <span class="value">{{ $firsttraining->training_name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Year:</span>
                <span class="value">{{ date('Y', strtotime($firsttraining->training_period_to)) }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Total attendence:</span>
                <span class="value">{{$attendedCount}}</span>
            </div>
            <div class="detail-row">
                <span class="label">Training Days:</span>
                <span class="value">{{$firsttraining->duration}}</span>
            </div>
            <div class="detail-row">
                <span class="label">Period (From-to):</span>
                <span class="value">{{ $firsttraining->training_period_from }} - {{ $firsttraining->training_period_to }}</span>
            </div>
        </div>
    @else
        <p>No records found.</p>
    @endif
    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Service N0.</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Division</th>
                <th>Cost Per Head</th>
            </tr>
        </thead>
        <tbody>
            @if($trainings->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">No records found.</td>
                </tr>
            @else
                @foreach($trainings as $training)
                    @foreach($training->participants as $participant)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $participant->epf_number }}</td>
                            <td class="text-center">{{ $participant->name }}</td>
                            <td class="text-center">{{ $participant->designation }}</td>
                            <td class="text-center">{{ $participant->division->division_name }}</td>
                            <td class="text-center">{{ $participant->cost_per_head }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <footer>
        © 2009 - {{ \Carbon\Carbon::now()->year }} Airport and Aviation Services (Sri Lanka) (Private) Limited, All rights reserved.
    </footer>
</body>
</html>