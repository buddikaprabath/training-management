<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Participant;
use App\Models\Training;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current year
        $currentYear = Carbon::now()->year;

        // Count trainings in current year
        $currentYearTrainings = Training::whereYear('training_period_from', $currentYear)->count();

        // Count trainings in all other years
        $otherYearsTrainings = Training::whereYear('training_period_from', '!=', $currentYear)->count();

        // Calculate total trainings
        $totalTrainings = $currentYearTrainings + $otherYearsTrainings;

        // Calculate percentage (avoid division by zero)
        $trainingPercentage = $totalTrainings > 0
            ? ($currentYearTrainings / $totalTrainings) * 100
            : 0;

        // get total cost of trainings in the current year   
        $totalCost = Training::whereYear('training_period_from', $currentYear)
            ->sum('total_program_cost');

        // get budget of current year
        $budgetAllocation = Budget::whereyear('created_at', $currentYear)
            ->first()
            ?->amount ?? 0;

        // calculate the budget utilization of the current year
        $budgetUtilization = $budgetAllocation > 0
            ? ($totalCost / $budgetAllocation) * 100
            : 0;


        // get the total number of participants in the current year
        $totalParticipants = Participant::whereHas('training', function ($query) use ($currentYear) {
            $query->whereYear('training_period_from', $currentYear);
        })->count();

        // get the percentage of participants in the current year according to the other years
        $otherYearsParticipants = Participant::whereHas('training', function ($query) use ($currentYear) {
            $query->whereYear('training_period_from', '!=', $currentYear);
        })->count();
        $Participantspercentage = $otherYearsParticipants > 0
            ? ($totalParticipants / $otherYearsParticipants) * 100
            : 0;


        // get the foreign initial budget of the current year
        $foreignBudget = Budget::whereyear('created_at', $currentYear)
            ->where('provide_type', 'Foreign')
            ->where('type', 'Initial')
            ->first()
            ?->amount ?? 0;


        // get the foreign Transfer budget of the current year
        $foreignTransferBudget = Budget::whereyear('created_at', $currentYear)
            ->where('provide_type', 'Foreign')
            ->where('type', 'Transfer')
            ->first()
            ?->amount ?? 0;

        //get the foreign training total cost of the current year
        $foreignTrainingCost = Training::whereYear('training_period_from', $currentYear)
            ->where('course_type', 'Foreign')
            ->sum('total_program_cost');

        // get the local initial  budget of the current year
        $localBudget = Budget::whereyear('created_at', $currentYear)
            ->where('provide_type', 'Local')
            ->where('type', 'Initial')
            ->first()
            ?->amount ?? 0;
        // get the local Transfer budget of the current year
        $localTransferBudget = Budget::whereyear('created_at', $currentYear)
            ->where('provide_type', 'Local')
            ->where('type', 'Transfer')
            ->first()
            ?->amount ?? 0;

        // get the local training total cost of the current year
        $localTrainingCost = Training::whereYear('training_period_from', $currentYear)
            ->where('course_type', '!=', 'Foreign')
            ->sum('total_program_cost');



        //get latest 8 trainings with thier participant count
        $latestTrainings = Training::withCount('participants')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Query trainings count by month using training_period_from
        $monthlyTrainings = Training::selectRaw('MONTH(training_period_from) as month, COUNT(*) as count')
            ->whereYear('training_period_from', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Initialize array with 0 counts for all months
        $monthlyCounts = array_fill(1, 12, 0);

        // Merge with actual data
        foreach ($monthlyTrainings as $month => $count) {
            $monthlyCounts[$month] = $count;
        }

        $trainings = Training::select('training_period_from', 'training_period_to')
            ->whereYear('training_period_from', now()->year)
            ->get();

        $startDates = $trainings->pluck('training_period_from')->map(function ($date) {
            return $date->format('Y-m-d');
        })->toArray();

        $endDates = $trainings->pluck('training_period_to')->map(function ($date) {
            return $date->format('Y-m-d');
        })->toArray();

        // get the total attendent participants in the current year
        $totalAttendentParticipants = Participant::whereHas('training', function ($query) use ($currentYear) {
            $query->whereYear('training_period_from', $currentYear);
        })->where('completion_status', 'Attended')->count();

        // get the percentage of the total attendent participants in the current year
        $attendentParticipantsPercentage = $totalParticipants > 0
            ? ($totalAttendentParticipants / $totalParticipants) * 100
            : 0;

        // return the view with the data
        return view('SuperAdmin.page.dashboard', [
            'totalTraining' => $totalTrainings,
            'currentYearTraining' => $currentYearTrainings,
            'trainingPercentage' => round($trainingPercentage, 2), // rounded to 2 decimal places
            'currentYear' => $currentYear,
            'totalCost' => $totalCost,
            'budgetUtilization' => round($budgetUtilization, 2), // rounded to 2 decimal places
            'totalParticipants' => $totalParticipants,
            'Participantspercentage' => round($Participantspercentage, 2), // rounded to 2 decimal places
            'foreignBudget' => $foreignBudget,
            'localBudget' => $localBudget,
            'foreignTransferBudget' => $foreignTransferBudget,
            'localTransferBudget' => $localTransferBudget,
            'foreignTrainingCost' => $foreignTrainingCost,
            'localTrainingCost' => $localTrainingCost,
            'latestTrainings' => $latestTrainings,
            'monthlyTrainingCounts' => array_values($monthlyCounts),
            'startDates' => $startDates,
            'endDates' => $endDates,
            'totalAttendentParticipants' => $totalAttendentParticipants,
            'attendentParticipantsPercentage' => round($attendentParticipantsPercentage, 2), // rounded to 2 decimal places
        ]);
    }
}
