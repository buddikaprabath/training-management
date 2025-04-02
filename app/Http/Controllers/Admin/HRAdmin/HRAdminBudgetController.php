<?php

namespace App\Http\Controllers\Admin\HRAdmin;

use App\Models\Budget;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HRAdminBudgetController extends Controller
{

    //budget handling
    public function budgetview(Request $request)
    {
        try {
            $query = $request->input('query');

            // Retrieve budgets with optional query filter and pagination
            $budgets = Budget::when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('type', 'LIKE', "%{$query}%")
                    ->orWhere('provide_type', 'LIKE', "%{$query}%")
                    ->orWhere('amount', 'LIKE', "%{$query}%")
                    ->orWhere('created_at', 'LIKE', "%{$query}%");
            })->paginate(10); // Ensure paginate() is used

            // Return the view with budgets and query
            return view('Admin.HRAdmin.budget.Detail', compact('budgets', 'query'));
        } catch (\Exception $e) {
            // Catch any exceptions and return an error message
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    //budget create page
    public function createbudgetview()
    {
        try {
            return view('Admin.HRAdmin.budget.Create');
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading budget create page: ' . $e->getMessage());
        }
    }

    public function budgetstore(Request $request)
    {
        try {
            $request->validate([
                'type' => 'string|required',
                'provide_type' => 'required|string',
                'amount' => 'numeric|max:999999999'
            ]);

            $currentYear = date('Y');

            // Check if this is an initial budget and one already exists for this year and category
            if ($request->type === 'Initial') {
                $existingInitial = Budget::where('type', 'Initial')
                    ->where('provide_type', $request->provide_type)
                    ->whereYear('created_at', $currentYear)
                    ->exists();

                if ($existingInitial) {
                    return back()->with('error', "An initial {$request->provide_type} budget already exists for this year!");
                }
            }

            Budget::create([
                'type' => $request->type,
                'amount' => $request->amount,
                'provide_type' => $request->provide_type,
                'division_id' => 1
            ]);

            return redirect()->route('Admin.HRAdmin.budget.Detail')->with('success', 'Budget created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error storing budget detail: ' . $e->getMessage());
        }
    }
    //end budget handling functions
}
