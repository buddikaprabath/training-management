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
        <h3>Training Full Summary(Local/Foreign)</h3>
    </div>
        <div class="employee-details">
            <div class="detail-row">
                <span class="label">Category:</span>
                <span class="value">{{$category}}</span>
            </div>
            <div class="detail-row">
                <span class="label">Year:</span>
                <span class="value">{{ $year }}</span>
            </div>
            
        </div>
    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Program Name</th>
                <th>Institute</th>
                <th>Trainer</th>
                <th>No. Of Days</th>
                <th>Batch Size</th>
                <th>Total Cost</th>
                <th>Training Hours</th>
                <th>Month</th>
            </tr>
        </thead>
        <tbody>
            @if($trainings->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">No records found.</td>
                </tr>
            @else
                @foreach ($trainings as $courseType => $trainingGroup)
                    <tr>
                        <td colspan="9" class="text-left"><strong>{{ $courseType }} Training Records</strong></td>
                    </tr>
                    @foreach ($trainingGroup as $training)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $training->training_name }}</td>
                            <td class="text-center">
                                @foreach ($training->institutes as $institute)
                                    {{ $institute->name }}<br>
                                @endforeach
                            </td>
                            <td class="text-center">
                                @foreach ($training->trainers as $trainer)
                                    {{ $trainer->name }}<br>
                                @endforeach
                            </td>
                            <td class="text-center">{{ $training->duration }}</td>
                            <td class="text-center">{{ $training->batch_size }}</td>
                            <td class="text-center">{{ $training->total_program_cost }}</td>
                            <td class="text-center">{{ $training->total_training_hours }}</td>
                            <td class="text-center">{{ $training->training_period_to->format('M') }}</td>
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