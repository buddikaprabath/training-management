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
        try {
            // Get current year for filtering data
            $currentYear = Carbon::now()->year;

            // Training Statistics
            // ===================

            // Count trainings in current year
            $currentYearTrainings = Training::whereYear('training_period_from', $currentYear)->count();

            // Count trainings in all other years (excluding current year)
            $otherYearsTrainings = Training::whereYear('training_period_from', '!=', $currentYear)->count();

            // Calculate total trainings (current year + other years)
            $totalTrainings = $currentYearTrainings + $otherYearsTrainings;

            // Calculate percentage of current year trainings (avoid division by zero)
            $trainingPercentage = $totalTrainings > 0
                ? ($currentYearTrainings / $totalTrainings) * 100
                : 0;

            // Budget and Cost Statistics
            // =========================

            // Get total cost of trainings in the current year
            $totalCost = Training::whereYear('training_period_from', $currentYear)
                ->sum('total_program_cost');

            // Get budget allocation for current year (default to 0 if not found)
            $budgetAllocation = Budget::whereyear('created_at', $currentYear)
                ->first()
                ?->amount ?? 0;

            // Calculate budget utilization percentage (avoid division by zero)
            $budgetUtilization = $budgetAllocation > 0
                ? ($totalCost / $budgetAllocation) * 100
                : 0;

            // Participant Statistics
            // =====================

            // Get total number of participants in current year trainings
            $totalParticipants = Participant::whereHas('training', function ($query) use ($currentYear) {
                $query->whereYear('training_period_from', $currentYear);
            })->count();

            // Get participants count from other years (excluding current year)
            $otherYearsParticipants = Participant::whereHas('training', function ($query) use ($currentYear) {
                $query->whereYear('training_period_from', '!=', $currentYear);
            })->count();

            // Calculate percentage of current year participants (avoid division by zero)
            $Participantspercentage = $otherYearsParticipants > 0
                ? ($totalParticipants / $otherYearsParticipants) * 100
                : 0;

            // Foreign/Local Budget Breakdown
            // =============================

            // Get foreign initial budget for current year
            $foreignBudget = Budget::whereyear('created_at', $currentYear)
                ->where('provide_type', 'Foreign')
                ->where('type', 'Initial')
                ->first()
                ?->amount ?? 0;

            // Get foreign transfer budget for current year
            $foreignTransferBudget = Budget::whereyear('created_at', $currentYear)
                ->where('provide_type', 'Foreign')
                ->where('type', 'Transfer')
                ->first()
                ?->amount ?? 0;

            // Get total cost of foreign trainings in current year
            $foreignTrainingCost = Training::whereYear('training_period_from', $currentYear)
                ->where('course_type', 'Foreign')
                ->sum('total_program_cost');

            // Get local initial budget for current year
            $localBudget = Budget::whereyear('created_at', $currentYear)
                ->where('provide_type', 'Local')
                ->where('type', 'Initial')
                ->first()
                ?->amount ?? 0;

            // Get local transfer budget for current year
            $localTransferBudget = Budget::whereyear('created_at', $currentYear)
                ->where('provide_type', 'Local')
                ->where('type', 'Transfer')
                ->first()
                ?->amount ?? 0;

            // Get total cost of local trainings in current year
            $localTrainingCost = Training::whereYear('training_period_from', $currentYear)
                ->where('course_type', '!=', 'Foreign')
                ->sum('total_program_cost');

            // Recent Trainings and Monthly Data
            // ================================

            // Get latest 8 trainings with their participant counts
            $latestTrainings = Training::withCount('participants')
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            // Query trainings count by month for current year
            $monthlyTrainings = Training::selectRaw('MONTH(training_period_from) as month, COUNT(*) as count')
                ->whereYear('training_period_from', $currentYear)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray();

            // Initialize array with 0 counts for all months (1-12)
            $monthlyCounts = array_fill(1, 12, 0);

            // Merge with actual data from query
            foreach ($monthlyTrainings as $month => $count) {
                $monthlyCounts[$month] = $count;
            }

            // Get training periods for current year (for calendar view)
            $trainings = Training::select('training_period_from', 'training_period_to')
                ->whereYear('training_period_from', now()->year)
                ->get();

            // Format start dates for calendar
            $startDates = $trainings->pluck('training_period_from')->map(function ($date) {
                return $date->format('Y-m-d');
            })->toArray();

            // Format end dates for calendar
            $endDates = $trainings->pluck('training_period_to')->map(function ($date) {
                return $date->format('Y-m-d');
            })->toArray();

            // Participant Attendance Statistics
            // ================================

            // Get count of participants who actually attended trainings
            $totalAttendentParticipants = Participant::whereHas('training', function ($query) use ($currentYear) {
                $query->whereYear('training_period_from', $currentYear);
            })->where('completion_status', 'Attended')->count();

            // Calculate attendance percentage (avoid division by zero)
            $attendentParticipantsPercentage = $totalParticipants > 0
                ? ($totalAttendentParticipants / $totalParticipants) * 100
                : 0;

            // Return view with all collected data
            // ==================================
            return view('SuperAdmin.page.dashboard', [
                // Training statistics
                'totalTraining' => $totalTrainings,
                'currentYearTraining' => $currentYearTrainings,
                'trainingPercentage' => round($trainingPercentage, 2), // rounded to 2 decimal places

                // Year reference
                'currentYear' => $currentYear,

                // Budget and cost
                'totalCost' => $totalCost,
                'budgetUtilization' => round($budgetUtilization, 2),

                // Participant statistics
                'totalParticipants' => $totalParticipants,
                'Participantspercentage' => round($Participantspercentage, 2),

                // Budget breakdown
                'foreignBudget' => $foreignBudget,
                'localBudget' => $localBudget,
                'foreignTransferBudget' => $foreignTransferBudget,
                'localTransferBudget' => $localTransferBudget,
                'foreignTrainingCost' => $foreignTrainingCost,
                'localTrainingCost' => $localTrainingCost,

                // Recent trainings and monthly data
                'latestTrainings' => $latestTrainings,
                'monthlyTrainingCounts' => array_values($monthlyCounts),

                // Calendar data
                'startDates' => $startDates,
                'endDates' => $endDates,

                // Attendance statistics
                'totalAttendentParticipants' => $totalAttendentParticipants,
                'attendentParticipantsPercentage' => round($attendentParticipantsPercentage, 2),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while fetching the dashboard data.');
        }
    }
}
