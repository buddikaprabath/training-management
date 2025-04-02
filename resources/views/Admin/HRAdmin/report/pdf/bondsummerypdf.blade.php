<!DOCTYPE html>
<html>
<head>
    <title>Bond Summary</title>
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
        <h3>Bond Summary Report</h3>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center align-top">S/N</th>
                        <th class="text-center align-top">Training Code</th>
                        <th class="text-center align-top">Employee EPF</th>
                        <th class="text-center align-top">Employee Name</th>
                        <th class="text-center align-top">Designation</th>
                        <th class="text-center align-top">Division</th>
                        <th class="text-center align-top">Title Of Bonded Training</th>
                        <th class="text-center align-top">Obligatory Period(from-to)</th>
                        <th class="text-center align-top">Date Of Agreement</th>
                        <th class="text-center align-top">Programme Expired date of bond/agreement Name</th>
                        <th class="text-center align-top">Bond Value</th>
                        <th class="text-center align-top">Surety 01 Name</th>
                        <th class="text-center align-top">Surety 02 Name</th>
                        <th class="text-center align-top">Program Cost</th>
            </tr>
        </thead>
        <tbody>
            @php
            $counter = 1;
        @endphp
        @foreach ($bondsummery as $item)
            @foreach ($item->participants as $participant)
                <tr>
                    <td class="text-center">{{$counter++}}</td>
                    <td class="text-center">{{ $participant->training->id ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->epf_number ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->designation ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->division->division_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->training->training_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->obligatory_period ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->date_of_signing ?? 'N/A' }}</td>
                    <td class="text-center">{{ date('Y-m-d',strtotime($participant->training->exp_date ?? 'N/A')) }}</td>
                    <td class="text-center">{{ $participant->bond_value ?? 'N/A' }}</td>
                    <td class="text-center">{{ $participant->sureties[0]->name ?? 'No Surety' }}</td>
                    <td class="text-center">{{ $participant->sureties[1]->name ?? 'No Surety' }}</td>
                    <td class="text-center">{{ $participant->training->total_program_cost ?? 'N/A' }}</td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <footer>
        © 2009 - {{ \Carbon\Carbon::now()->year }} Airport and Aviation Services (Sri Lanka) (Private) Limited, All rights reserved.
    </footer>
</body>
</html>