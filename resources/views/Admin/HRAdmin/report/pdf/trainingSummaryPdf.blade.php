<!DOCTYPE html>
<html>
<head>
    <title>Training Summary Report</title>
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
        <h3>Training Summary Report</h3>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>Course Type</th>
                <th>No. of Programs</th>
                <th>No. of Participants</th>
                <th>Training Hours</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($combinedSummary as $summary)
                <tr>
                    <td>{{ $summary->course_type }}</td>
                    <td>{{ $summary->no_of_programs }}</td>
                    <td>{{ $summary->no_of_participants }}</td>
                    <td>{{ $summary->training_hours }}</td>
                    <td>{{ $summary->total_cost }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <footer>
        © 2009 - {{ \Carbon\Carbon::now()->year }} Airport and Aviation Services (Sri Lanka) (Private) Limited, All rights reserved.
    </footer>
</body>
</html>