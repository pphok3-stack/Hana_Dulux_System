@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Edit Customer Due & Points') }} - {{ $customer->name }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <h4>{{ __('Customer Info') }}</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>{{ __('Name') }}</strong></td>
                                    <td>{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Phone') }}</strong></td>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Total Due') }}</strong></td>
                                    <td>
                                        @if($customer->total_due > 0)
                                            <span class="text-danger fw-bold">${{ number_format($customer->total_due, 2) }}</span>
                                        @else
                                            <span class="text-success">$0.00</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Points') }}</strong></td>
                                    <td><span class="badge bg-success">{{ $customer->points }} pts</span></td>
                                </tr>
                            </table>
                        </div>

                        @if($customer->orders->where('due', '>', 0)->count() > 0)
                        <div class="table-responsive mb-4">
                            <h4>{{ __('Orders with Due') }}</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">{{ __('Invoice') }}</th>
                                        <th class="text-center">{{ __('Date') }}</th>
                                        <th class="text-center">{{ __('Total') }}</th>
                                        <th class="text-center">{{ __('Paid') }}</th>
                                        <th class="text-center">{{ __('Due') }}</th>
                                        <th class="text-center">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders->where('due', '>', 0) as $order)
                                    <tr>
                                        <td class="text-center">{{ $order->invoice_no }}</td>
                                        <td class="text-center">{{ $order->order_date->format('d-m-Y') }}</td>
                                        <td class="text-center">{{ Number::currency($order->total, 'USD') }}</td>
                                        <td class="text-center">{{ Number::currency($order->pay, 'USD') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ Number::currency($order->due, 'USD') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('due.edit', $order) }}" class="btn btn-sm btn-outline-primary">
                                                {{ __('Pay Due') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4">
                            <h4>{{ __('Pay All Due') }}</h4>
                            <form action="{{ route('customers.due.payAll', $customer->uuid) }}" method="POST">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label for="pay" class="form-label">
                                            {{ __('Amount') }}
                                            <small class="text-muted">(Total due: ${{ number_format($customer->total_due, 2) }})</small>
                                        </label>
                                        <input type="number"
                                               id="pay"
                                               name="pay"
                                               class="form-control @error('pay') is-invalid @enderror"
                                               value="{{ old('pay', $customer->total_due) }}"
                                               min="0"
                                               step="0.01"
                                               required
                                        >
                                        @error('pay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Pay $' + document.getElementById('pay').value + ' for all due orders?')">
                                            {{ __('Pay All Due') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                        @endif

                        <form action="{{ route('customers.due.update', $customer->uuid) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h4>{{ __('Edit Points') }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="points" class="form-label">
                                            {{ __('Points Balance') }}
                                        </label>
                                        <input type="number"
                                               id="points"
                                               name="points"
                                               class="form-control @error('points') is-invalid @enderror"
                                               value="{{ old('points', $customer->points) }}"
                                               min="0"
                                               required
                                        >
                                        @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex">
                                <a href="{{ route('customers.due') }}" class="btn btn-secondary me-2">
                                    {{ __('Back') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Points') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
