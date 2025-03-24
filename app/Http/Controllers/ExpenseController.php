<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Budget;

class ExpenseController extends Controller
{
    // Show the expense list with optional filtering
    public function index(Request $request)
    {
        $query = Expense::with('category');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('date', $request->date);
        }

        $expenses = $query->latest()->get();
        $categories = Category::all();
        
        return view('expenses.index', compact('expenses', 'categories'));
    }

    // Store a new expense
    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'amount' => 'required|numeric|min:1',
        'description' => 'nullable|string',
        'date' => 'required|date',
    ]);

    $categoryId = $request->category_id;
    $amount = $request->amount;

    // Fetch the budget from the budget table for the selected category
    $budget = Budget::where('category_id', $categoryId)->value('amount'); // Ensure 'amount' exists in the 'budgets' table

    if (!$budget) {
        return redirect()->route('expenses.index')->with('error', 'Budget not set for this category. Please check the budget module.');
    }

    // Calculate the total existing expenses for this category
    $totalExpense = Expense::where('category_id', $categoryId)->sum('amount');

    // Check if adding the new expense exceeds the budget
    if (($totalExpense + $amount) > $budget) {
        return redirect()->route('expenses.index')->with('error', 'Your limit is reached. Please check the budget module.');
    }

    // Store the expense
    Expense::create([
        'title' => Category::find($categoryId)->name, // Fetch category name
        'category_id' => $categoryId,
        'amount' => $amount,
        'description' => $request->description,
        'date' => $request->date,
    ]);

    return redirect()->route('expenses.index')->with('success', 'Expense added successfully!');
}


    // Delete an expense
    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}
