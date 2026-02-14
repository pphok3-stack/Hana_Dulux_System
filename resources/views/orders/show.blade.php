@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Order Details') }}
                        </h3>
                    </div>

                    <div class="card-actions btn-actions">
                        <x-action.close route="{{ route('orders.index') }}" />
                    </div>
                </div>

                <div class="card-body">
                    <div class="row row-cards mb-3">
                        <div class="col">
                            <label for="order_date" class="form-label required">
                                {{ __('Order Date') }}
                            </label>
                            <input type="text" id="order_date" class="form-control"
                                value="{{ $order->order_date->format('d-m-Y') }}" disabled>
                        </div>

                        <div class="col">
                            <label for="invoice_no" class="form-label required">
                                {{ __('Invoice No.') }}
                            </label>
                            <input type="text" id="invoice_no" class="form-control" value="{{ $order->invoice_no }}"
                                disabled>
                        </div>

                        <div class="col">
                            <label for="customer" class="form-label required">
                                {{ __('Customer') }}
                            </label>
                            <input type="text" id="customer" class="form-control" value="{{ $order->customer->name }}"
                                disabled>
                        </div>

                        <div class="col">
                            <label for="payment_type" class="form-label required">
                                {{ __('Payment Type') }}
                            </label>

                            <input type="text" id="payment_type" class="form-control" value="{{ $order->payment_type }}"
                                disabled>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="align-middle text-center">No.</th>
                                    <th scope="col" class="align-middle text-center">Photo</th>
                                    <th scope="col" class="align-middle text-center">Product Name</th>
                                    <th scope="col" class="align-middle text-center">Product Code</th>
                                    <th scope="col" class="align-middle text-center">Quantity</th>
                                    <th scope="col" class="align-middle text-center">Price</th>
                                    <th scope="col" class="align-middle text-center">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->details as $item)
                                    <tr>
                                        <td class="align-middle text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="align-middle text-center">
                                            <div style="max-height: 80px; max-width: 80px;">
                                                <img class="img-fluid"
                                                    src="{{ $item->product->product_image ? asset('storage/' . $item->product->product_image) : asset('assets/img/products/default.webp') }}">
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $item->product->name }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $item->product->code }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ number_format($item->unitcost, 2) }}
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6" class="text-end">Total</td>
                                    <td class="text-center">{{ number_format($order->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">Paid Amount</td>
                                    <td class="text-center">{{ number_format($order->pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">Due</td>
                                    <td class="text-center">{{ number_format($order->due, 2) }}</td>
                                </tr>
                                @php
                                    $outstandingBalance = $order->customer->total_due;
                                @endphp
                                @if($outstandingBalance > 0)
                                <tr>
                                    <td colspan="6" class="text-end text-danger">
                                        <strong>Outstanding Balance (All Orders)</strong>
                                    </td>
                                    <td class="text-center text-danger">
                                        <strong>{{ number_format($outstandingBalance, 2) }}</strong>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="6" class="text-end">Points Balance</td>
                                    <td class="text-center">{{ $order->customer->points }} pts</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end">Status</td>
                                    <td class="text-center">
                                        <x-status dot
                                            color="{{ $order->order_status === \App\Enums\OrderStatus::COMPLETE ? 'green' : ($order->order_status === \App\Enums\OrderStatus::PENDING ? 'orange' : '') }}"
                                            class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                </div>
            </div>

        </div>
    </div>
@endsection
