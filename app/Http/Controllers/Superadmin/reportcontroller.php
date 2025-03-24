<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class reportcontroller extends Controller
{
    //Training Custodian-Wise Summery
    public function TrainingCustodianWiseSummeryView(Request $request)
    {
        try {
            //check if atleast one filter option given
            if (!$request->filled('custodian') && !$request->filled('year') && !$request->filled('course_type')) {
                return view('SuperAdmin.report.TrainingCustodianWiseSummery', ['trainings' => collect()]);
            }

            //get the value from the request
            $custodian = $request->custodian;
            $year = $request->year;
            $course_type = $request->course_type;

            // Step 1: First filter trainings by custodian/year/course_type
            $query = Training::query();

            if ($custodian) {
                $query->where('training_custodian', 'like', '%' . $custodian . '%');
            }
            if ($year) {
                $query->whereRaw('YEAR(training_period_to) = ?', [$year]);
            }
            if ($course_type) {
                $query->where('course_type', $course_type);
            }

            //get the participant/institute/trainer according to the training
            $trainings = $query->with(['institutes', 'trainers']) // Eager load institutes and trainers
                ->withCount('participants') // Add participant count
                ->get();

            //store trainings details in session for pdf download method
            session([
                'filtered_training_custodian_wise_summery' => $trainings,
                'year' => $year,
                'course_type' => $course_type,
                'custodian' => $custodian
            ]);

            // Return the view with the filtered data
            return view('SuperAdmin.report.TrainingCustodianWiseSummery', ['trainings' => $trainings, 'year' => $year, 'course_type' => $course_type, 'custodian' => $custodian]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error loading training custodian wise summary');
        }
    }

    //download training custodian wise summery pdf
    public function downloadTrainingCustodianWiseSummeryPdf(Request $request)
    {
        try {
            $trainings = session('filtered_training_custodian_wise_summery', collect());
            $year = session('Year', 0);
            $course_type = session('course_type');
            $custodian = session('custodian');

            //load the view and pass the data
            $pdf = PDF::loadView('SuperAdmin.report.pdf.TrainingCustodianWiseSummeryPdf', compact('trainings', 'year', 'course_type', 'custodian'));

            //download th pdf
            return $pdf->download('Training_custodian_wise_summery.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error downloading training custodian wise summery pdf.');
        }
    }

    //Designation-Wise Summery
    public function DesignationWiseSummeryView(Request $request)
    {
        try {
            //check if atleast one filter option provided
            if (!$request->filled('designation') && !$request->filled('year') && !$request->filled('course_type')) {
                return view('SuperAdmin.report.DesignationWiseSummery', ['trainings' => collect()]);
            }

            //get the value from the request
            $designation = $request->designation;
            $year = $request->year;
            $course_type = $request->course_type;

            //filter by designation/year/course_type
            $query = Training::query();

            if ($year) {
                $query->whereRaw('YEAR(training_period_to) = ?', [$year]);
            }

            if ($course_type) {
                $query->where('course_type', $course_type);
            }
            //get participants according to their designation
            $trainings = $query->with(['participants' => function ($query) use ($designation) {
                $query->where('designation', 'like', '%' . $designation . '%');
            }])->get();

            session([
                'designation_wise_summery_data' => $trainings,
                'designation' => $designation,
                'year' => $year,
                'course_type' => $course_type
            ]);

            return view('SuperAdmin.report.DesignationWiseSummery', ['trainings' => $trainings, 'designation' => $designation, 'year' => $year, 'course_type' => $course_type]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error loading Designation wise summery');
        }
    }

    //download designation wise summery pf
    public function downloadDesignationWiseSummeryPdf(Request $request)
    {
        try {
            //get designation wise summery from DesignationWiseSummeryView method
            $trainings = session('designation_wise_summery_data');
            $designation = session('designation');
            $year = session('year');
            $course_type = session('course_type');

            //load the designation summery view and pass the data 
            $pdf = PDf::loadView('SuperAdmin.report.pdf.DesignationWiseSummeryPdf', compact('trainings', 'designation', 'year', 'course_type'));

            //download the designation wise summery pdf
            return $pdf->download('Designation_Wise_Summery.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error downloading designation wise summery pdf.');
        }
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
