<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Models\User;
use App\Notifications\BudgetExceededNotification;
use Illuminate\Http\RedirectResponse;


// Home Page - Show Dashboard
Route::get('/', [ReportController::class, 'index'])->name('dashboard');

// Category Routes
Route::resource('categories', CategoryController::class)->only([
    'index', 'store', 'destroy'
]);

// Expense Routes
Route::resource('expenses', ExpenseController::class)->only([
    'index', 'store', 'destroy'
]);

Route::resource('budgets', BudgetController::class)->except(['create', 'show', 'update']);
Route::get('budgets/{id}/edit', [BudgetController::class, 'edit'])->name('budgets.edit'); 
Route::delete('budgets/{id}', [BudgetController::class, 'destroy'])->name('budgets.destroy');


// Report Route
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::get('/test-notification', function () {
    $user = User::first(); // Assuming a user exists
    $user->notify(new BudgetExceededNotification('Food', 5000, 4000));
    return 'Notification sent!';
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
