<?php

namespace App\Http\Controllers\Admin;

use App\Models\Budget;
use App\Models\Division;
use App\Models\Training;
use App\Models\Participant;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class hrreportcontroller extends Controller
{
    //training summery view handling
    public function trainingsummaryView(Request $request)
    {
        try {
            // Define the course types for local and foreign
            $localTypes = ['Local In-house', 'Local Outside', 'Local-Tailor Made', 'CATC'];
            $foreignType = ['Foreign'];

            // Get the date range and division from the request
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $division_id = $request->input('division_id');

            // Initialize an empty collection to store the combined summary
            $combinedSummary = collect();

            // Fetch local trainings - process each training type separately
            foreach ($localTypes as $type) {
                // Base query for this training type
                $query = Training::where('course_type', $type);

                // Apply date filters if provided
                if ($startDate && $endDate) {
                    // Both start and end dates are provided - find trainings that overlap with the range
                    $query->where(function ($q) use ($startDate, $endDate) {
                        $q->where(function ($inner) use ($startDate, $endDate) {
                            // Training starts within the range
                            $inner->whereBetween('trainings.training_period_from', [$startDate, $endDate]);
                        })->orWhere(function ($inner) use ($startDate, $endDate) {
                            // Training ends within the range
                            $inner->whereBetween('trainings.training_period_to', [$startDate, $endDate]);
                        })->orWhere(function ($inner) use ($startDate, $endDate) {
                            // Training spans the entire range
                            $inner->where('trainings.training_period_from', '<=', $startDate)
                                ->where('trainings.training_period_to', '>=', $endDate);
                        });
                    });
                } elseif ($startDate) {
                    // Only start date is provided - match exact date or date is within training period
                    $query->where(function ($q) use ($startDate) {
                        $q->where('trainings.training_period_from', '=', $startDate)
                            ->orWhere(function ($inner) use ($startDate) {
                                $inner->where('trainings.training_period_from', '<=', $startDate)
                                    ->where('trainings.training_period_to', '>=', $startDate);
                            });
                    });
                } elseif ($endDate) {
                    // Only end date is provided - match exact date or date is within training period
                    $query->where(function ($q) use ($endDate) {
                        $q->where('trainings.training_period_to', '=', $endDate)
                            ->orWhere(function ($inner) use ($endDate) {
                                $inner->where('trainings.training_period_from', '<=', $endDate)
                                    ->where('trainings.training_period_to', '>=', $endDate);
                            });
                    });
                }

                // Apply division filter if provided - explicitly specify the table
                if ($division_id) {
                    $query->where('trainings.division_id', $division_id);
                }

                // Get the count of programs and sum of costs/hours directly from the trainings table
                $programSummary = (clone $query)
                    ->selectRaw('course_type, 
                                  COUNT(DISTINCT trainings.id) as no_of_programs, 
                                  SUM(trainings.total_training_hours) as training_hours, 
                                  SUM(trainings.total_program_cost) as total_cost')
                    ->groupBy('course_type')
                    ->first();

                // Get the count of participants separately
                $participantCount = (clone $query)
                    ->leftJoin('participants', 'trainings.id', '=', 'participants.training_id')
                    ->selectRaw('COUNT(participants.id) as no_of_participants')
                    ->first();

                // Combine the data if program summary exists
                if ($programSummary) {
                    $programSummary->no_of_participants = $participantCount ? $participantCount->no_of_participants : 0;
                    $combinedSummary->push($programSummary);
                }
            }

            // Fetch foreign trainings with the same enhanced filtering logic
            $foreignQuery = Training::whereIn('course_type', $foreignType);

            // Apply date filters if provided
            if ($startDate && $endDate) {
                // Both start and end dates are provided - find trainings that overlap with the range
                $foreignQuery->where(function ($q) use ($startDate, $endDate) {
                    $q->where(function ($inner) use ($startDate, $endDate) {
                        // Training starts within the range
                        $inner->whereBetween('trainings.training_period_from', [$startDate, $endDate]);
                    })->orWhere(function ($inner) use ($startDate, $endDate) {
                        // Training ends within the range
                        $inner->whereBetween('trainings.training_period_to', [$startDate, $endDate]);
                    })->orWhere(function ($inner) use ($startDate, $endDate) {
                        // Training spans the entire range
                        $inner->where('trainings.training_period_from', '<=', $startDate)
                            ->where('trainings.training_period_to', '>=', $endDate);
                    });
                });
            } elseif ($startDate) {
                // Only start date is provided - match exact date or date is within training period
                $foreignQuery->where(function ($q) use ($startDate) {
                    $q->where('trainings.training_period_from', '=', $startDate)
                        ->orWhere(function ($inner) use ($startDate) {
                            $inner->where('trainings.training_period_from', '<=', $startDate)
                                ->where('trainings.training_period_to', '>=', $startDate);
                        });
                });
            } elseif ($endDate) {
                // Only end date is provided - match exact date or date is within training period
                $foreignQuery->where(function ($q) use ($endDate) {
                    $q->where('trainings.training_period_to', '=', $endDate)
                        ->orWhere(function ($inner) use ($endDate) {
                            $inner->where('trainings.training_period_from', '<=', $endDate)
                                ->where('trainings.training_period_to', '>=', $endDate);
                        });
                });
            }

            // Apply division filter if provided - explicitly specify the table
            if ($division_id) {
                $foreignQuery->where('trainings.division_id', $division_id);
            }

            // Get the count of programs and sum of costs/hours directly from the trainings table
            $foreignProgramSummary = (clone $foreignQuery)
                ->selectRaw('course_type, 
                              COUNT(DISTINCT trainings.id) as no_of_programs, 
                              SUM(trainings.total_training_hours) as training_hours, 
                              SUM(trainings.total_program_cost) as total_cost')
                ->groupBy('course_type')
                ->first();

            // Get the count of participants separately
            $foreignParticipantCount = (clone $foreignQuery)
                ->leftJoin('participants', 'trainings.id', '=', 'participants.training_id')
                ->selectRaw('COUNT(participants.id) as no_of_participants')
                ->first();

            // Combine the data if foreign program summary exists
            if ($foreignProgramSummary) {
                $foreignProgramSummary->no_of_participants = $foreignParticipantCount ? $foreignParticipantCount->no_of_participants : 0;
                $combinedSummary->push($foreignProgramSummary);
            }

            // Store the filtered data in the session
            session(['training_summary' => $combinedSummary]);

            // Return the view with the combined summary
            return view('Admin.HRAdmin.report.trainingSummary', compact('combinedSummary'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading training summary: ' . $e->getMessage());
        }
    }

    //download training summery pdf
    public function downloadTrainingSummaryPdf(Request $request)
    {
        try {
            // Fetch the data using the same logic and filter parameters
            $combinedSummary = session('training_summary', collect());

            // Load the view and pass the data
            $pdf = Pdf::loadView('Admin.HRAdmin.report.pdf.trainingSummaryPdf', compact('combinedSummary'));

            // Download the PDF file
            return $pdf->download('training_summary_report.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    //report of Individual Employee Training Record
    public function IndividualEmployeeTrainingRecordView(Request $request)
    {
        try {
            // Ensure that name or epf_number is provided (year alone is not enough)
            if (!$request->filled('name') && !$request->filled('epf_number')) {
                return view('Admin.HRAdmin.report.IndividualEmployeeTrainingRecordReport', ['participants' => collect()]);
            }

            // Get values from the request
            $name = $request->name;
            $epf_number = $request->epf_number;
            $year = $request->year;

            // Step 1: First filter participants by name and/or EPF number
            $query = Participant::query();

            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }

            if ($epf_number) {
                $query->where('epf_number', $epf_number);
            }

            // Step 2: Get these participants with their training relationships
            $participants = $query->get();

            // Step 3: If year filter is applied, filter the related trainings
            if ($year) {
                // Create a collection to hold participants with training in the specified year
                $filteredParticipants = collect();

                foreach ($participants as $participant) {
                    // Load the related training with institutes and trainers
                    $participant->load(['training' => function ($query) use ($year) {
                        $query->whereRaw('YEAR(training_period_to) = ?', [$year])
                            ->with('institutes', 'trainers');
                    }]);

                    // Only include participants who have training in the specified year
                    if ($participant->training) {
                        $filteredParticipants->push($participant);
                    }
                }

                // Replace original participants with filtered ones
                $participants = $filteredParticipants;
            } else {
                // If no year filter, load all trainings with institutes and trainers
                foreach ($participants as $participant) {
                    $participant->load('training.institutes', 'training.trainers');
                }
            }
            // Store the filtered data in the session
            session(['filtered_participants' => $participants]);

            // Return view with filtered data
            return view('Admin.HRAdmin.report.IndividualEmployeeTrainingRecordReport', ['participants' => $participants]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading Individual Employee Training Record: ' . $e->getMessage());
        }
    }
    //download the Individual Employee Training Record
    public function downloadIndividualEmployeeTrainingRecordPdf(Request $request)
    {
        try {
            // Retrieve the filtered data from the session
            $participants = session('filtered_participants', collect());

            // Load the view and pass the data
            $pdf = Pdf::loadView('Admin.HRAdmin.report.pdf.IndividualEmployeeTrainingRecordPdf', compact('participants'));

            // Download the PDF file
            return $pdf->download('individual_employee_training_record.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    //ParticularCourseCompletedSummaryView
    public function ParticularCourseCompletedSummaryView(Request $request)
    {
        try {

            $training_codes = DB::table('training_codes')->get();
            // Ensure that at least one filter is applied
            if (!$request->filled('name') && !$request->filled('training_code')) {
                return view('Admin.HRAdmin.report.ParticularCourseCompletedSummery', [
                    'trainings' => collect(),
                    'training_codes' => $training_codes,
                ]);
            }

            // Get values from the request
            $name = $request->name;
            $training_code = $request->training_code;

            // Step 1: First filter trainings by name and/or training code
            $query = Training::query();

            if ($name) {
                $query->where('training_name', 'like', '%' . $name . '%');
            }

            if ($training_code) {
                $query->where('training_code', $training_code);
            }

            // Step 2: Get these trainings with participants who have completed the training
            $trainings = $query->with(['participants' => function ($query) {
                $query->where('completion_status', 'attended');
            }])->get();

            // Step 3: Calculate the total count of attended employees
            $attendedCount = 0;
            foreach ($trainings as $training) {
                $attendedCount += $training->participants->count();
            }

            // Store the filtered data and attended count in the session
            session([
                'filtered_course_completed_participant' => $trainings,
                'attended_employee_count' => $attendedCount,
            ]);

            // Return view with filtered data
            return view('Admin.HRAdmin.report.ParticularCourseCompletedSummery', [
                'trainings' => $trainings,
                'attendedCount' => $attendedCount,
                'training_codes' => $training_codes
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading Particular Course Completed Summary: ' . $e->getMessage());
        }
    }

    //download the Particular Course Completed Summary
    public function downloadParticularCourseCompletedSummaryPdf(Request $request)
    {
        try {
            // Retrieve the filtered data from the session
            $trainings = session('filtered_course_completed_participant', collect());
            $attendedCount = session('attended_employee_count', 0);

            // Load the view and pass the data
            $pdf = Pdf::loadView('Admin.HRAdmin.report.pdf.ParticularCourseCompletedSummaryPdf', compact('trainings', 'attendedCount'));

            // Download the PDF file
            return $pdf->download('particular_course_completed_summary.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    //full summary view (Local/Foreign)
    public function TrainingFullSummaryView(Request $request)
    {
        try {

            // Ensure that at least one filter is applied
            if (!$request->filled('year') && !$request->filled('category')) {
                return view('Admin.HRAdmin.report.TrainingFullSummery', ['trainings' => collect()]);
            }

            // Get values from the request
            $year = $request->year;
            $category = $request->category;

            // Step 1: First filter trainings by year and/or category 
            $query = Training::query();

            if ($year) {
                $query->whereRaw('YEAR(training_period_to) = ?', [$year]);
            }
            if ($category) {
                $query->where('category', $category);
            }

            // Step 2: Get these trainings with institutes and trainers with participants group by course type
            $trainings = $query->with(['institutes', 'trainers'])
                ->get()
                ->groupBy('course_type');

            // Store the filtered data and filter option in the session
            session([
                'filtered_Training_Full_Summary_Local_foreign' => $trainings,
                'Year' => $year,
                'Category' => $category
            ]);

            //return trainings to the view
            return view('Admin.HRAdmin.report.TrainingFullSummery', ['trainings' => $trainings, 'year' => $year, 'category' => $category]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading Training Full Summary: ' . $e->getMessage());
        }
    }

    //download the Training FUll Summary(Local/Foreign)
    public function downloadTrainingFullSummaryLocalForeignPdf(Request $request)
    {
        try {
            // Retrieve the filtered data from the session
            $trainings = session('filtered_Training_Full_Summary_Local_foreign', collect());
            $year = session('Year', 0);
            $category = session('Category');

            // Load the view and pass the data
            $pdf = Pdf::loadView('Admin.HRAdmin.report.pdf.TrainingFullSummaryPdf', compact('trainings', 'year', 'category'));

            // Download the PDF file
            return $pdf->download('Training_Full_summary.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }
    //Training Custodian-Wise Summery
    public function TrainingCustodianWiseSummeryView(Request $request)
    {
        try {
            //check if atleast one filter option given
            if (!$request->filled('custodian') && !$request->filled('year') && !$request->filled('course_type')) {
                return view('Admin.HRAdmin.report.TrainingCustodianWiseSummery', ['trainings' => collect()]);
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
            return view('Admin.HRAdmin.report.TrainingCustodianWiseSummery', ['trainings' => $trainings, 'year' => $year, 'course_type' => $course_type, 'custodian' => $custodian]);
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
            $pdf = PDF::loadView('Admin.HRAdmin.report.pdf.TrainingCustodianWiseSummeryPdf', compact('trainings', 'year', 'course_type', 'custodian'));

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
                return view('Admin.HRAdmin.report.DesignationWiseSummery', ['trainings' => collect()]);
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

            return view('Admin.HRAdmin.report.DesignationWiseSummery', ['trainings' => $trainings, 'designation' => $designation, 'year' => $year, 'course_type' => $course_type]);
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
            $pdf = PDf::loadView('Admin.HRAdmin.report.pdf.DesignationWiseSummeryPdf', compact('trainings', 'designation', 'year', 'course_type'));

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
                return view('Admin.HRAdmin.report.CourseCode-wise_summary', [
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
            return view('Admin.HRAdmin.report.CourseCode-wise_summary', [
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
            $pdf = PDF::loadView('Admin.HRAdmin.report.pdf.Course_Code_Wise_Summery_pdf', [
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
                return view('Admin.HRAdmin.report.ListOfAbsenteesReport', ['trainings' => collect()]);
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
            return view('Admin.HRAdmin.report.ListOfAbsenteesReport', [
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
            $pdf = PDF::loadView('Admin.HRAdmin.report.pdf.ListofAbsenteesPdf', [
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
        try {
            // Initialize variables with default values
            $employee_name = null;
            $division_name = null;

            // Check if at least one filter option is provided
            if (!$request->filled('training_name') && !$request->filled('epf_number') && !$request->filled('division_id')) {
                return view('Admin.HRAdmin.report.TrainingsRequiredtobeRenewed_Recurrent', ['participants' => collect()]);
            }

            // Get filtered data
            $training_name = $request->training_name;
            $epf_number = $request->epf_number;
            $division_id = $request->division_id;

            if ($request->filled('epf_number')) {
                $participant = Participant::where('epf_number', $epf_number)->first();

                if ($participant) {
                    $employee_name = $participant->name;
                    $division = Division::find($participant->division_id);
                    $division_name = $division ? $division->division_name : null;
                }
            }

            // Filter query
            $query = Participant::query();

            if ($epf_number) {
                $query->where('epf_number', $epf_number);
            }
            if ($division_id) {
                $query->where('division_id', $division_id);
                // If you want to get division_name when filtering by division_id
                if (is_null($division_name) && $division_id) {
                    $division = Division::find($division_id);
                    $division_name = $division ? $division->division_name : null;
                }
            }

            // Filter participants that have recurrent trainings
            $query->whereHas('training', function ($query) use ($training_name) {
                $query->where('training_structure', 'Recurrent');
                if (!empty($training_name)) {
                    $query->where('training_name', 'LIKE', '%' . $training_name . '%');
                }
            });

            // Load related trainings
            $participants = $query->with(['training' => function ($query) use ($training_name) {
                $query->where('training_structure', 'Recurrent');
                if (!empty($training_name)) {
                    $query->where('training_name', 'LIKE', '%' . $training_name . '%');
                }
            }])->get();

            // Store in session and return view
            session([
                'TrainingsRequiredtobeRenewedRecurrentData' => $participants,
                'epf_number' => $epf_number,
                'training_name' => $training_name,
                'employee_name' => $employee_name,
                'division_name' => $division_name,
            ]);

            return view('Admin.HRAdmin.report.TrainingsRequiredtobeRenewed_Recurrent', [
                'participants' => $participants,
                'epf_number' => $epf_number,
                'training_name' => $training_name,
                'employee_name' => $employee_name,
                'division_name' => $division_name,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading training required to be renewed/recurrent.');
        }
    }

    //download the Trainings Required to be Renewed Recurrent report pdf
    public function downloadTrainingsRequiredtobeRenewedRecurrentPdf(Request $request)
    {
        try {
            //get data from TrainingsRequiredtobeRenewedRecurrentView method
            $participants = session('TrainingsRequiredtobeRenewedRecurrentData');
            $epf_number = session('epf_number');
            $training_name = session('training_name');
            $employee_name = session('employee_name');
            $division_name = session('division_name');

            //load the Trainings Required to be Renewed Recurrent pdf page with relevent data
            $pdf = PDF::loadView('Admin.HRAdmin.report.pdf.TrainingsRequiredtobeRenewedRecurrentDataPdf', [
                'participants' => $participants,
                'epf_number' => $epf_number,
                'training_name' => $training_name,
                'employee_name' => $employee_name,
                'division_name' => $division_name,
            ]);

            //dowload the Trainings Required to be Renewed Recurrent pdf
            return $pdf->download('Trainings_Required_to_be_Renewed_Recurrent.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'error downloading Trainings Required to be Renewed Recurrent pdf.');
        }
    }
    //bond summery load with filtered data
    public function bondsummaryView(Request $request)
    {
        try {
            // Ensure that at least one filter is applied
            if (!$request->filled('name') && !$request->filled('epf_number') && !$request->filled('training_name') && !$request->filled('division_id')) {
                return view('Admin.HRAdmin.report.BONDSummary', ['bondsummery' => collect()]);
            }

            // Subquery to get the IDs of trainings with matching participants and training_name
            $subQuery = Training::where(function ($query) use ($request) {
                // Filter by training_name
                if ($request->filled('training_name')) {
                    $query->where('training_name', 'like', '%' . $request->training_name . '%');
                }
            })->whereHas('participants', function ($query) use ($request) {
                // Filter by participant fields
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }
                if ($request->filled('epf_number')) {
                    $query->where('epf_number', $request->epf_number);
                }
                if ($request->filled('division_id')) {
                    $query->where('division_id', $request->division_id);
                }
            })->pluck('id'); // Get the IDs of trainings that match the filters

            // Main query to fetch trainings with participants and sureties
            $query = Training::with(['participants' => function ($query) use ($request) {
                // Apply filters on participants
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }
                if ($request->filled('epf_number')) {
                    $query->where('epf_number', $request->epf_number);
                }
                if ($request->filled('division_id')) {
                    $query->where('division_id', $request->division_id);
                }
            }, 'participants.sureties'])
                ->whereIn('id', $subQuery); // Filter trainings based on the subquery

            // Fetch filtered data with pagination
            $bondsummery = $query->paginate(10);

            session([
                'bondsummerydata' => $bondsummery,
            ]);

            // Return view with filtered and grouped data
            return view('Admin.HRAdmin.report.BONDSummary', compact('bondsummery'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading Bond summary: ' . $e->getMessage());
        }
    }

    //download the bond summery pdf
    public function downloadBondSummeryPdf(Request $request)
    {
        try {
            //get bond summery data from bondsummaryView method
            $bondsummery = session('bondsummerydata');

            //load the bond summery pdf view with relevent data
            $pdf = PDF::loadView('Admin.HRAdmin.report.pdf.bondsummerypdf', ['bondsummery' => $bondsummery]);

            //download the bond summery pdf
            return $pdf->download('Bond_Summery.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error downloading Bond summary pdf. ');
        }
    }

    //load budget summery with relevent data
    public function budgetSummeryView(Request $request)
    {
        try {
            // Check if at least one filter option provided
            if (!$request->filled('duration_monthly') && !$request->filled('duration_quarterly') && !$request->filled('year')) {
                return view('Admin.HRAdmin.report.BudgetSummery', [
                    'budgets' => collect(),
                    'local' => null,
                    'foreign' => null,
                    'year' => null,
                    'time_period' => null
                ]);
            }

            // Validate mandatory year filter
            if (!$request->filled('year')) {
                return back()->with('error', 'Year filter is mandatory. Please select a year first.');
            }

            $year = $request->year;

            // Validate that either monthly or quarterly is selected with year
            if (!$request->filled('duration_monthly') && !$request->filled('duration_quarterly')) {
                return back()->with('error', 'Please choose either monthly or quarterly filter along with the year.');
            }

            // Get filtered option values
            $duration_monthly = $request->duration_monthly;
            $duration_quarterly = $request->duration_quarterly;

            // Define local and foreign course types
            $localTypes = ['Local In-house', 'Local Outside', 'Local-Tailor Made', 'CATC'];
            $foreignType = ['Foreign'];

            // Initialize summary arrays
            $localBudgetSummary = [
                'training_count' => 0,
                'participant_count' => 0,
                'total_hours' => 0,
                'total_cost' => 0,
                'budget_amount' => 0
            ];

            $foreignBudgetSummary = [
                'training_count' => 0,
                'participant_count' => 0,
                'total_hours' => 0,
                'total_cost' => 0,
                'budget_amount' => 0
            ];

            // Query for local trainings with mandatory year filter
            $localQuery = Training::whereIn('course_type', $localTypes)
                ->whereYear('training_period_from', $year)
                ->withCount('participants');

            // Query for foreign trainings with mandatory year filter
            $foreignQuery = Training::whereIn('course_type', $foreignType)
                ->whereYear('training_period_from', $year)
                ->withCount('participants');

            // Apply monthly or quarterly filter (one must be present)
            if ($duration_monthly) {
                $month = $duration_monthly;
                $localQuery->whereMonth('training_period_from', $month);
                $foreignQuery->whereMonth('training_period_from', $month);

                $time_period = 'Month: ' . $month;
            } elseif ($duration_quarterly) {
                // Ensure quarter is treated as integer
                $quarter = (int)$duration_quarterly;
                $startMonth = ($quarter * 3) - 2;
                $endMonth = $quarter * 3;

                $localQuery->whereBetween(
                    DB::raw('MONTH(training_period_from)'),
                    [$startMonth, $endMonth]
                );

                $foreignQuery->whereBetween(
                    DB::raw('MONTH(training_period_from)'),
                    [$startMonth, $endMonth]
                );

                $time_period = 'Quarter: ' . $quarter;
            }

            // Get budget data filtered by year
            $localBudget = Budget::where('provide_type', 'Local')
                ->whereYear('created_at', $year)
                ->sum('amount');

            $foreignBudget = Budget::where('provide_type', 'Foreign')
                ->whereYear('created_at', $year)
                ->sum('amount');

            $localBudgetSummary['budget_amount'] = $localBudget;
            $foreignBudgetSummary['budget_amount'] = $foreignBudget;

            // Get local trainings data
            $localTrainings = $localQuery->get();
            $localBudgetSummary['training_count'] = $localTrainings->count();
            $localBudgetSummary['participant_count'] = $localTrainings->sum('participants_count');
            $localBudgetSummary['total_hours'] = $localTrainings->sum('total_training_hours');
            $localBudgetSummary['total_cost'] = $localTrainings->sum('total_program_cost');

            // Get foreign trainings data
            $foreignTrainings = $foreignQuery->get();
            $foreignBudgetSummary['training_count'] = $foreignTrainings->count();
            $foreignBudgetSummary['participant_count'] = $foreignTrainings->sum('participants_count');
            $foreignBudgetSummary['total_hours'] = $foreignTrainings->sum('total_training_hours');
            $foreignBudgetSummary['total_cost'] = $foreignTrainings->sum('total_program_cost');

            //dd($localBudgetSummary);
            //dd($foreignBudgetSummary);
            //pass summery through the session to download budget summery pdf
            session([
                'budgetSummerylocalData' => $localBudgetSummary,
                'budgetSummeryforeignData' => $foreignBudgetSummary,
                'year' => $year,
                'time_period' => $time_period ?? null
            ]);
            return view('Admin.HRAdmin.report.BudgetSummery', [
                'local' => $localBudgetSummary,
                'foreign' => $foreignBudgetSummary,
                'year' => $year,
                'time_period' => $time_period ?? null
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading Budget Summary: ' . $e->getMessage());
        }
    }
    //dowload budget summery reprt pdf
    public function downloadBudgetSummeryPdf(Request $request)
    {
        try {
            //get filtered budget summery data from budgetSummeryView method
            $localBudgetSummary = session('budgetSummerylocalData');
            $foreignBudgetSummary = session('budgetSummeryforeignData');
            $year = session('year');
            $time_period = session('time_period');

            //load the budget summery pdf with relevent data
            $pdf = PDF::loadView('Admin.HRAdmin.report.pdf.budgetSummeryPdf', [
                'local' => $localBudgetSummary,
                'foreign' => $foreignBudgetSummary,
                'year' => $year,
                'time_period' => $time_period ?? null
            ]);

            //dowload budget summery report pdf
            return $pdf->download('Budget_Summery_report.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'erro downloading budget summery pdf.');
        }
    }
}
