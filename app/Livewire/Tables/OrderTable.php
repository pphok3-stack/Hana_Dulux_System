<?php

namespace App\Livewire\Tables;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderTable extends Component
{
    use WithPagination;

    public $perPage = 5;

    public $search = '';

    public $sortField = 'invoice_no';

    public $sortAsc = false;

    public $filterCustomer = '';
    public $filterStartDate = '';
    public $filterEndDate = '';

    public function updatingFilterCustomer() { $this->resetPage(); }
    public function updatingFilterStartDate() { $this->resetPage(); }
    public function updatingFilterEndDate() { $this->resetPage(); }

    public function sortBy($field): void
    {
        if($this->sortField === $field)
        {
            $this->sortAsc = ! $this->sortAsc;

        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->filterCustomer = '';
        $this->filterStartDate = '';
        $this->filterEndDate = '';
        $this->search = '';
        $this->resetPage();
    }

    public function getDownloadUrl()
    {
        $params = [];
        if ($this->filterCustomer) $params['customer'] = $this->filterCustomer;
        if ($this->filterStartDate) $params['start_date'] = $this->filterStartDate;
        if ($this->filterEndDate) $params['end_date'] = $this->filterEndDate;

        return route('orders.receipt-history', $params);
    }

    public function render()
    {
        $query = Order::where("user_id", auth()->id())
            ->with(['customer', 'details'])
            ->search($this->search);

        if ($this->filterCustomer) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . $this->filterCustomer . '%');
            });
        }

        if ($this->filterStartDate) {
            $query->whereDate('order_date', '>=', $this->filterStartDate);
        }

        if ($this->filterEndDate) {
            $query->whereDate('order_date', '<=', $this->filterEndDate);
        }

        return view('livewire.tables.order-table', [
            'orders' => $query
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage)
        ]);
    }
}
