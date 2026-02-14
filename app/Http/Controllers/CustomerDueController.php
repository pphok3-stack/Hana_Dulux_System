<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerDueController extends Controller
{
    public function index()
    {
        $customers = Customer::where('user_id', auth()->id())
            ->withSum(['orders' => fn($q) => $q->where('due', '>', 0)], 'due')
            ->get()
            ->filter(fn($c) => $c->orders_sum_due > 0 || $c->points > 0);

        return view('customers.due', [
            'customers' => $customers
        ]);
    }

    public function edit($uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();
        $customer->loadMissing('orders');

        return view('customers.due-edit', [
            'customer' => $customer
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'points' => 'required|integer|min:0',
        ]);

        $customer->update([
            'points' => $request->points,
        ]);

        return redirect()
            ->route('customers.due')
            ->with('success', 'Customer points updated!');
    }

    public function payAllDue(Request $request, $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'pay' => 'required|numeric|min:0',
        ]);

        $remaining = $request->pay;

        // Distribute payment across orders with due (oldest first)
        $orders = $customer->orders()
            ->where('due', '>', 0)
            ->orderBy('order_date', 'asc')
            ->get();

        foreach ($orders as $order) {
            if ($remaining <= 0) break;

            $payForThis = min($remaining, $order->due);
            $order->update([
                'pay' => $order->pay + $payForThis,
                'due' => $order->due - $payForThis,
            ]);
            $remaining -= $payForThis;
        }

        return redirect()
            ->route('customers.due.edit', $customer->uuid)
            ->with('success', 'Payment applied to due orders!');
    }
}
