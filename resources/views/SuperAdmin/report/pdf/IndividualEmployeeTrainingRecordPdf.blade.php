<!DOCTYPE html>
<html>
<head>
    <title>Individual Employee Training Record</title>
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
    <p class="timestamp">Generated on: {{ \Carbon\Carbon::now()->year }}</p>

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
    @if($participants->isNotEmpty())
        <!-- Display employee details using the first participant -->
        @php
            $firstParticipant = $participants->first();
        @endphp
        <div class="employee-details">
            <div class="detail-row">
                <span class="label">Employee Name:</span>
                <span class="value">{{ $firstParticipant->name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Division:</span>
                <span class="value">{{ $firstParticipant->division->division_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Service Number:</span>
                <span class="value">{{ $firstParticipant->epf_number }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Designation:</span>
                <span class="value">{{ $firstParticipant->designation }}</span>
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
                <th>Course Type</th>
                <th>Uniqe Identifier</th>
                <th>Name Of The Program</th>
                <th>Category</th>
                <th>Institute</th>
                <th>Trainer</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            @if($participants->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">No records found.</td>
                </tr>
            @else
                @foreach($participants as $participant)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $participant->training?->course_type}}</td>
                        <td class="text-center">{{ $participant->training?->id}}</td>
                        <td class="text-center">{{ $participant->training?->training_name}}</td>
                        <td class="text-center">{{ $participant->training?->category}}</td>
                        <td class="text-center">
                            @foreach ($participant->training->institutes as $institute)
                                {{ $institute->name }}
                            @endforeach
                        </td>
                        <td class="text-center">
                            @foreach ($participant->training->trainers as $trainer)
                                {{ $trainer->name }}
                            @endforeach
                        </td>
                        <td class="text-center">
                            @if($participant->training?->training_period_to)
                                {{ date('Y', strtotime($participant->training->training_period_to)) }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
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