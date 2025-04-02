<!DOCTYPE html>
<html>
<head>
    <title>Budget Summary</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0 20px; /* Reduce left and right margin */
            font-size: 12px; /* Reduce base font size */
        }

        /* Logo Styles */
        .logo-container {
            text-align: center;
            margin-bottom: 10px; /* Reduce space below the logo */
        }

        .logo {
            width: 200px; /* Reduce logo size */
            display: inline-block;
        }

        /* Timestamp Styles */
        .timestamp {
            position: absolute;
            top: 10px;
            right: 20px; /* Align to the top-right corner */
            font-size: 12px; /* Reduce font size */
        }

        /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 10px; /* Reduce space below the header */
        }

        .header h1 {
            font-size: 18px; /* Reduce heading size */
            margin: 5px 0;
        }

        .header h3 {
            font-size: 14px; /* Reduce subheading size */
            margin: 5px 0;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8px; /* Reduce font size further */
            table-layout: fixed; /* Ensures consistent column widths */
            word-wrap: break-word; /* Allows long words to break */
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            line-height: 1.2; /* Reduce line height */
        }


        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Footer Styles */
        footer {
            text-align: center;
            font-size: 10px; /* Reduce footer font size */
            margin-top: 10px; /* Reduce space above the footer */
        }

        /* Ensure table fits within the page */
        @media print {
            table {
                width: 100%;
                font-size: 10px; /* Ensure font size is small for printing */
            }
            th, td {
                padding: 3px; /* Further reduce padding for printing */
            }
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
        <h3>Budget Summary Report</h3>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th></th>
                <th>No. of Training</th>
                <th>No. of Participants</th>
                <th>Total No. of Hours</th>
                <th>Total Cost</th>
                <th>Available Rs.</th>
                <th>Initial Budget Allocations</th>
                <th>Budget Utilization</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Local</strong></td>
                <td>{{ $local['training_count'] ?? 0 }}</td>
                <td>{{ $local['participant_count'] ?? 0 }}</td>
                <td>{{ $local['total_hours'] ?? 0 }}</td>
                <td>{{ number_format($local['total_cost'] ?? 0, 2) }}</td>
                <td>{{ number_format(($local['budget_amount'] ?? 0) - ($local['total_cost'] ?? 0), 2) }}</td>
                <td>{{ number_format($local['budget_amount'] ?? 0, 2) }}</td>
                <td>{{ number_format($local['total_cost'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Foreign</strong></td>
                <td>{{ $foreign['training_count'] ?? 0 }}</td>
                <td>{{ $foreign['participant_count'] ?? 0 }}</td>
                <td>{{ $foreign['total_hours'] ?? 0 }}</td>
                <td>{{ number_format($foreign['total_cost'] ?? 0, 2) }}</td>
                <td>{{ number_format(($foreign['budget_amount'] ?? 0) - ($foreign['total_cost'] ?? 0), 2) }}</td>
                <td>{{ number_format($foreign['budget_amount'] ?? 0, 2) }}</td>
                <td>{{ number_format($foreign['total_cost'] ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <footer>
        © 2009 - {{ \Carbon\Carbon::now()->year }} Airport and Aviation Services (Sri Lanka) (Private) Limited, All rights reserved.
    </footer>
</body>
</html>