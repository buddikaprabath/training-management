<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\Training;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        try {
            $training_codes = DB::table('training_codes')->get();
            //check if atleast one filter option provided
            if (!$request->filled('course_code') && !$request->filled('duration') && !$request->filled('course_type')) {
                return view('SuperAdmin.report.CourseCode-wise_summary', [
                    'trainings' => collect(),
                    'training_codes' => $training_codes,
                ]);
            }
            //get filtered data to the variable
            $course_code = $request->course_code;
            $duration = $request->duration;
            $course_type = $request->course_type;

            //filter training by Course code/duration/course type
            $query = Training::query();

            if ($course_code) {
                $query->where('training_code', $course_code);
            }
            if ($duration) {
                $query->whereMonth('training_period_to', '=', $duration);
            }
            if ($course_type) {
                $query->where('course_type', $course_type);
            }
            //get the participant count according to the training
            $trainings = $query->withCount('participants')
                ->get();

            session([
                'corse_code_wise_summery_data' => $trainings,
                'course_code' => $course_code,
                'course_type' => $course_type,
                'training_codes' => $training_codes
            ]);
            //laod the view with filtered data
            return view('SuperAdmin.report.CourseCode-wise_summary', [
                'trainings' => $trainings,
                'course_code' => $course_code,
                'course_type' => $course_type,
                'training_codes' => $training_codes,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error loading course code wise summery page.');
        }
    }

    //download the course code wise summery pdf
    public function downloadCourseCoudeWiseSummeryPdf(Request $request)
    {
        try {
            //get data from courseCodeWiseSummaryView method
            $trainings = session('corse_code_wise_summery_data');
            $course_code = session('course_code');
            $course_type = session('course_type');

            //load the course code wise summery pdf view with relevent data
            $pdf = PDF::loadView('SuperAdmin.report.pdf.Course_Code_Wise_Summery_pdf', [
                'trainings' => $trainings,
                'course_code' => $course_code,
                'course_type' => $course_type,
            ]);

            //download the course code wise summery pdf
            return $pdf->download('Course_code_wise_Summery.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error downloading course code wise summery pdf.');
        }
    }

    //List of absentees
    public function ListOfAbsenteesReportView(Request $request)
    {
        try {
            //check if atleast one filter option provide
            if (!$request->filled('training_name') && !$request->filled('course_type')) {
                return view('SuperAdmin.report.ListOfAbsenteesReport', ['trainings' => collect()]);
            }
            //get value from request
            $training_name = $request->training_name;
            $course_type = $request->course_type;

            //filter training by training name and/or course type
            $query = Training::query();

            if ($training_name) {
                $query->where('training_name', 'like', '%' . $training_name . '%');
            }
            if ($course_type) {
                $query->where('course_type', $course_type);
            }
            //get participants who are absent
            $trainings = $query->with(['participants' => function ($query) {
                $query->where('completion_status', 'unattended');
            }])->get();

            session([
                'list_of_absentees_report_data' => $trainings,
                'training_name' => $training_name,
                'course_type' => $course_type
            ]);
            return view('SuperAdmin.report.ListOfAbsenteesReport', [
                'trainings' => $trainings,
                'training_name' => $training_name,
                'course_type' => $course_type
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error loading list of absentees report view.');
        }
    }

    //download the list of absentees summery pdf
    public function downloadlistOfAbsenteesSummeryPdf(Request $request)
    {
        try {
            //get velue filtered data from ListOfAbsenteesReportView method
            $trainings = session('list_of_absentees_report_data');
            $training_name = session('training_name');
            $course_type = session('course_type');

            //load the list of absentees pdf view with relevent data
            $pdf = PDF::loadView('SuperAdmin.report.pdf.ListofAbsenteesPdf', [
                'trainings' => $trainings,
                'training_name' => $training_name,
                'course_type' => $course_type
            ]);

            //download the list of absentees report pdf
            return $pdf->download('List_of_absentees_report.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error downloading list of absentees summery pdf.');
        }
    }

    //Trainings Required to be Renewed Recurrent
    public function TrainingsRequiredtobeRenewedRecurrentView(Request $request)
    {
        return view('SuperAdmin.report.TrainingsRequiredtobeRenewed_Recurrent');
    }
}
