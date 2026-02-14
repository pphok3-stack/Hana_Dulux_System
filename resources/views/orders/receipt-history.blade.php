<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ config('app.name') }} - Receipt History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        .receipt-container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .receipt-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .filter-info {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .summary-row {
            background: #f8f9fa;
            font-weight: 600;
        }
        .table th {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .table td {
            font-size: 13px;
        }
        .btn-section {
            text-align: center;
            margin-top: 20px;
        }
        .btn-section .btn {
            margin: 0 5px;
            padding: 10px 30px;
        }
        @media print {
            body { background: #fff; }
            .receipt-container { box-shadow: none; margin: 0; padding: 15px; }
            .btn-section { display: none; }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header d-flex justify-content-between align-items-center">
            <div>
                <h2>{{ Str::title(auth()->user()->store_name) }}</h2>
                <small class="text-muted">Receipt History</small>
            </div>
            <div class="text-end">
                <small class="text-muted">Printed: {{ now()->format('d-m-Y H:i') }}</small>
            </div>
        </div>

        <div class="filter-info">
            <strong>Filters:</strong>
            @if($customerFilter)
                Customer: <strong>{{ $customerFilter }}</strong> |
            @endif
            @if($startDate && $endDate)
                Date: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</strong>
                to <strong>{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</strong> |
            @elseif($startDate)
                From: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</strong> |
            @elseif($endDate)
                To: <strong>{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</strong> |
            @endif
            Total: <strong>{{ $orders->count() }} orders</strong>
        </div>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th class="text-center">Invoice</th>
                    <th class="text-center">Customer</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Payment</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Paid</th>
                    <th class="text-center">Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $order->invoice_no }}</td>
                    <td class="text-center">{{ $order->customer->name ?? '-' }}</td>
                    <td class="text-center">{{ $order->order_date->format('d-m-Y') }}</td>
                    <td class="text-center">{{ $order->payment_type }}</td>
                    <td class="text-center">${{ number_format($order->total, 2) }}</td>
                    <td class="text-center">${{ number_format($order->pay, 2) }}</td>
                    <td class="text-center">${{ number_format($order->due, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No orders found.</td>
                </tr>
                @endforelse
            </tbody>
            @if($orders->count() > 0)
            <tfoot>
                <tr class="summary-row">
                    <td colspan="5" class="text-end">Total:</td>
                    <td class="text-center">${{ number_format($orders->sum('total'), 2) }}</td>
                    <td class="text-center">${{ number_format($orders->sum('pay'), 2) }}</td>
                    <td class="text-center">${{ number_format($orders->sum('due'), 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>

        <div class="btn-section">
            <button onclick="window.print()" class="btn btn-primary">
                Print
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>
</body>

</html>
