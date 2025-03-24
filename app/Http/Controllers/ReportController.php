<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month); // Default: current month
        $year = $request->input('year', Carbon::now()->year); // Default: current year

        // Get total expense for the selected month
        $totalExpense = Expense::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        // Get expenses grouped by category
        $monthlyExpenses = Expense::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return view('reports.index', compact('monthlyExpenses', 'totalExpense', 'month', 'year'));
    }
}
