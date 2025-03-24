<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('category')->get();
        $categories = Category::all();
        return view('budgets.index', compact('budgets', 'categories'));
    }

    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'amount' => 'required|numeric|min:1'
    ]);

    // Check if it's an edit request
    if ($request->has('edit') && $request->edit == 'edit' && $request->has('budget_id')) {
        $budget = Budget::findOrFail($request->budget_id);
        $budget->update($request->only('category_id', 'amount'));
        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully!');
    }

    // Create new budget entry if not an edit request
    Budget::create($request->all());

    return redirect()->route('budgets.index')->with('success', 'Budget added successfully!');
}


    public function edit($id)
    {
        $budget = Budget::findOrFail($id);
        return response()->json($budget); // Return JSON for AJAX
    }

    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();
        return response()->json(['success' => 'Budget deleted successfully!']);
    }
}
