<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class reportcontroller extends Controller
{
    //Training Custodian-Wise Summery
    public function TrainingCustodianWiseSummeryView(Request $request)
    {
        return view('SuperAdmin.report.TrainingCustodianWiseSummery');
    }

    //Designation-Wise Summery
    public function DesignationWiseSummeryView(Request $request)
    {
        return view('SuperAdmin.report.DesignationWiseSummery');
    }

    //Course code-wise summary
    public function courseCodeWiseSummaryView(Request $request)
    {
        return view('SuperAdmin.report.CourseCode-wise_summary');
    }

    //List of absentees
    public function ListOfAbsenteesReportView(Request $request)
    {
        return view('SuperAdmin.report.ListOfAbsenteesReport');
    }

    //Trainings Required to be Renewed Recurrent
    public function TrainingsRequiredtobeRenewedRecurrentView(Request $request)
    {
        return view('SuperAdmin.report.TrainingsRequiredtobeRenewed_Recurrent');
    }
}
