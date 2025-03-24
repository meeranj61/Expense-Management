@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Budget Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#budgetModal">
            <i class="bi bi-plus-circle"></i> Add Budget
        </button>
    </div>

    <!-- Budget List -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Budget Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgets as $budget)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $budget->category->name }}</td>
                    <td>{{ number_format($budget->amount, 2) }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-budget" data-id="{{ $budget->id }}" data-category="{{ $budget->category_id }}" data-amount="{{ $budget->amount }}" data-bs-toggle="modal" data-bs-target="#budgetModal" data-edit="edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-budget" data-id="{{ $budget->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Budget Modal -->
<div class="modal fade" id="budgetModal" tabindex="-1" aria-labelledby="budgetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="budgetModalLabel">Add Budget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="budgetForm" method="POST" action="{{ route('budgets.store') }}">
                @csrf
                <input type="hidden" id="budget_id" name="budget_id">
                <input type="hidden" id="edit" name="edit" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Category:</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Budget Amount:</label>
                        <input type="number" name="amount" id="amount" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Budget</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Handling Modal and Delete -->
<script>
    $(document).ready(function() {
        $('.edit-budget').on('click', function() {
            $('#budget_id').val($(this).data('id'));
            $('#category_id').val($(this).data('category'));
            $('#amount').val($(this).data('amount'));
            $('#budgetModalLabel').text('Edit Budget');
            $("#edit").val("edit"); // Ensure edit flag is set

        });

        $('.delete-budget').on('click', function() {
            let budgetId = $(this).data('id');
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
                    $.ajax({
                        url: `/budgets/${budgetId}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            location.reload();
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
