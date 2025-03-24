<!DOCTYPE html>
<html>
<head>
    <title>Trainings Required to be Renewed/Recurrent</title>
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

        /* Divider Styles */
        .Course-details {
            margin-bottom: 10px; /* Reduce space below the details */
        }

        .detail-row {
            display: flex;
            margin-bottom: 5px; /* Reduce space between rows */
        }

        .detail-row .label {
            font-weight: bold;
            width: 120px; /* Reduce label width */
        }

        .detail-row .value {
            flex: 1;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px; /* Reduce space below the table */
            font-size: 10px; /* Reduce table font size */
        }

        th, td {
            border: 1px solid #000;
            padding: 4px; /* Reduce cell padding */
            text-align: center; /* Center align all text */
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
        <h3>Trainings Required to be Renewed/Recurrent</h3>
    </div>

    <!-- Display employee details using the first participant -->
    <div class="Course-details">
        <div class="detail-row">
            <span class="label">Course Name:</span>
            <span class="value">{{$training_name ?? 'N/A'}}</span>
        </div>
        <div class="detail-row">
            <span class="label">Employee Name:</span>
            <span class="value">{{$employee_name ?? 'N/A'}}</span>
        </div>
        <div class="detail-row">
            <span class="label">Employee EPF :</span>
            <span class="value">{{$epf_number ?? 'N/A'}}</span>
        </div>
        <div class="detail-row">
            <span class="label">Division Name :</span>
            <span class="value">{{$division_name ?? 'N/A'}}</span>
        </div>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Employee EPF</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Division</th>
                <th>Title of Training Required to Renew/Recurrent</th>
                <th>Validity Period</th>
                <th>Expiration Date</th>
            </tr>
        </thead>
        <tbody>
            @if ($participants->isempty())
                    <tr>
                        <td colspan="8" class="text-center">No Record Found</td>
                    </tr>
                @else
                    @foreach ($participants as $participant)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td class="text-center">{{$participant->epf_number}}</td>
                            <td class="text-center">{{$participant->name}}</td>
                            <td class="text-center">{{$participant->designation}}</td>
                            <td class="text-center">{{$participant->division->division_name}}</td>
                            <td class="text-center">{{$participant->training->training_name}}</td>
                            <td class="text-center">{{ date('Y-m-d', strtotime($participant->training->training_period_from)) }}-{{ date('Y-m-d', strtotime($participant->training->training_period_to)) }}</td>
                            <td class="text-center">{{ date('Y-m-d',strtotime($participant->training->exp_date))}}</td>
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