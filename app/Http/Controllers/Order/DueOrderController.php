<?php

namespace App\Http\Controllers\Order;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Mail\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DueOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('due', '>', 0)
            ->latest()
            ->with('customer')
            ->get();

        return view('due.index', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details']);

        return view('due.show', [
           'order' => $order
        ]);
    }

    public function edit(Order $order)
    {
        $order->loadMissing(['customer', 'details']);

        $customers = Customer::select(['id', 'name'])->get();

        return view('due.edit', [
            'order' => $order,
            'customers' => $customers
        ]);
    }

    public function update(Order $order, Request $request)
    {
        $validatedData = $request->validate([
            'pay' => 'required|numeric|min:0'
        ]);

        $total = $order->total;
        $currentPay = $order->pay;
        $newPay = $currentPay + $validatedData['pay'];
        $newDue = max(0, $total - $newPay);

        $order->update([
            'due' => $newDue,
            'pay' => $newPay
        ]);

        return redirect()
            ->route('due.index')
            ->with('success', 'Due amount has been updated!');
    }
    
}
