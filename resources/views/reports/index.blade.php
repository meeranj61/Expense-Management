@extends('layouts.app')

@section('title', 'Dashboard - Expense Reports')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">Monthly Expense Report</h2>

    <!-- Dropdown Filter -->
    <form method="GET" action="{{ route('reports.index') }}" class="d-flex justify-content-center mb-4">
        <select name="month" class="form-control w-auto me-2" id="monthFilter">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
            @endforeach
        </select>

        <select name="year" class="form-control w-auto me-2" id="yearFilter">
            @foreach(range(Carbon\Carbon::now()->year - 5, Carbon\Carbon::now()->year) as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Total Expense -->
    <div class="text-center mb-3">
        <h4>Total Expense: ₹{{ number_format($totalExpense, 2) }}</h4>
    </div>

    <!-- Expense Chart -->
    <canvas id="expenseChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('expenseChart').getContext('2d');
        
        const expenseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($monthlyExpenses->pluck('category.name')),
                datasets: [{
                    label: 'Total Expense',
                    data: @json($monthlyExpenses->pluck('total')),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

    });
</script>

@endsection
