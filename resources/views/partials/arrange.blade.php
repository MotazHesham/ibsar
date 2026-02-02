<form id="sorting-form" method="POST" action="{{ route('admin.arrange.update') }}">
    @csrf
    <input type="hidden" name="order_column" value="{{ $orderColumn }}">
    <input type="hidden" name="model" value="{{ $model }}">
    <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body"> 
        @if($model === 'App\\Models\\EconomicStatus')
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">{{ trans('cruds.beneficiary.fields.incomes') }}</h6>
                    <ol class="list-group sortable-list list-item-numbered" id="sorting-income">
                        @foreach ($records->where('type', 'income') as $record)
                            <li class="list-group-item" data-id="{{ $record->id }}">
                                <i class="ri-drag-move-2-fill me-2 text-dark fs-16 handle lh-1"></i>
                                {{ $record->{$name} }}
                            </li>
                        @endforeach
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">{{ trans('cruds.beneficiary.fields.expenses') }}</h6>
                    <ol class="list-group sortable-list list-item-numbered" id="sorting-expense">
                        @foreach ($records->where('type', 'expense') as $record)
                            <li class="list-group-item" data-id="{{ $record->id }}">
                                <i class="ri-drag-move-2-fill me-2 text-dark fs-16 handle lh-1"></i>
                                {{ $record->{$name} }}
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        @else
            <ol class="list-group sortable-list list-item-numbered" id="sorting-with-handle">
                @foreach ($records as $record)
                    <li class="list-group-item" data-id="{{ $record->id }}">
                        <i class="ri-drag-move-2-fill me-2 text-dark fs-16 handle lh-1"></i>
                        {{ $record->{$name} }}
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            {{ trans('global.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
            {{ trans('global.arrange') }}
        </button>
    </div>
</form>

<script>
    var sortingWithHandle = document.getElementById('sorting-with-handle');
    var sortingIncome = document.getElementById('sorting-income');
    var sortingExpense = document.getElementById('sorting-expense');

    if (sortingWithHandle) {
        var sortable = new Sortable(sortingWithHandle, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                updateOrder(sortingWithHandle);
            }
        });
    }

    if (sortingIncome) {
        var sortableIncome = new Sortable(sortingIncome, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                updateOrder(sortingIncome);
            }
        });
    }

    if (sortingExpense) {
        var sortableExpense = new Sortable(sortingExpense, {
            handle: '.handle',
            animation: 150,
            onEnd: function() {
                updateOrder(sortingExpense);
            }
        });
    }

    function updateOrder(listElement) {
        const items = listElement.querySelectorAll('li');
        const totalItems = items.length;
        items.forEach((item, index) => {
            item.setAttribute('data-order', totalItems - index);
        });
    }

    // Handle form submission
    document.getElementById('sorting-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get the new order
        let order = [];
        
        if (sortingWithHandle) {
            order = Array.from(document.querySelectorAll('#sorting-with-handle li')).map(item => ({
                id: item.dataset.id,
                order: item.dataset.order
            }));
        } else {
            const incomeOrder = Array.from(document.querySelectorAll('#sorting-income li')).map(item => ({
                id: item.dataset.id,
                order: item.dataset.order
            }));
            const expenseOrder = Array.from(document.querySelectorAll('#sorting-expense li')).map(item => ({
                id: item.dataset.id,
                order: item.dataset.order
            }));
            order = [...incomeOrder, ...expenseOrder];
        }

        // Send AJAX request
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                order: order,
                order_column: document.querySelector('input[name="order_column"]').value,
                model: document.querySelector('input[name="model"]').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Arranged successfully', 'success');
            } else {
                showToast('Error arranging: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error arranging', 'error');
        });
    });
</script>