@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="row my-4">
    <!-- Left Side: Title -->
    <div class="col-md-6 d-flex align-items-center">
        <h2 class="mb-0">Expense Management</h2>
    </div>

    <!-- Right Side: Buttons (Filter & Add Expense) -->
    <div class="col-md-6 d-flex justify-content-end gap-2">
        <button id="toggleFilter" class="btn btn-info">
            Show Filter
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="bi bi-plus-circle"></i> Add Expense
        </button>
    </div>
</div>


    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success fade show fadeout" id="successMessage">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger fade show fadeout" id="errorMessage">
            {{ session('error') }}
        </div>
    @endif

    <!-- Expense Filters -->
    <div class="card mb-3" id="filterSection" style="display: none;">
    <div class="card-header">Filter Expenses</div>
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category_id" id="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="amount" class="form-label">Amount (Max)</label>
                        <input type="number" name="amount" id="amount" class="form-control" value="{{ request('amount') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Expense List -->
    <div class="card">
        <div class="card-header">Expense List</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="expenseTable">
                    @foreach($expenses as $expense)
                        <tr>
                            <td>{{ $expense->date }}</td>
                            <td>{{ $expense->category->name }}</td>
                            <td>{{ $expense->amount }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $expense->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delete-form-{{ $expense->id }}" action="{{ route('expenses.destroy', $expense->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category_id" id="category" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" name="amount" id="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Fade out success message after 3 seconds
    setTimeout(function() {
        let successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.classList.add("fade");
            setTimeout(() => successMessage.remove(), 500);
        }
    }, 3000);

    setTimeout(function() {
        let errorMessage = document.getElementById('errorMessage');
        if (errorMessage) {
            errorMessage.classList.add("fade");
            setTimeout(() => errorMessage.remove(), 500);
        }
    }, 3000);

    // SweetAlert Delete Confirmation
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function() {
                let expenseId = this.getAttribute("data-id");
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("delete-form-" + expenseId).submit();
                    }
                });
            });
        });
    });

    $(document).ready(function() {
        // Check if any filter is applied, then show the filter section
        if ("{{ request('date') }}" || "{{ request('category_id') }}" || "{{ request('amount') }}") {
            $("#filterSection").show();
        }

        // Toggle filter section on button click
        $("#toggleFilter").click(function() {
            $("#filterSection").toggle();
        });
    });
</script>
@endsection
