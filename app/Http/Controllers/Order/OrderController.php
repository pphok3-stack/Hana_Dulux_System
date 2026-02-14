<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use App\Mail\StockAlert;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->count();

        return view('orders.index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        $products = Product::where('user_id', auth()->id())->with(['category', 'unit'])->get();

        $customers = Customer::where('user_id', auth()->id())->get(['id', 'name']);

        $carts = Cart::content();

        return view('orders.create', [
            'products' => $products,
            'customers' => $customers,
            'carts' => $carts,
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        $total = Cart::subtotal();
        $customer = Customer::findOrFail($request->customer_id);

        // Points discount (checkbox: apply all points or none)
        $pointsUsed = $request->apply_points ? $customer->points : 0;
        $pointsDiscount = $pointsUsed; // 1 point = $1
        $finalTotal = max(0, $total - $pointsDiscount);
        $due = $finalTotal - $request->pay;

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
            'pay' => $request->pay,
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_status' => OrderStatus::COMPLETE->value,
            'total_products' => Cart::count(),
            'sub_total' => $total,
            'vat' => 0,
            'total' => $finalTotal,
            'invoice_no' => generate_invoice_number('INV-', 10),
            'due' => $due,
            'user_id' => auth()->id(),
            'uuid' => Str::uuid(),
        ]);

        // Create Order Details
        $contents = Cart::content();
        $oDetails = [];

        foreach ($contents as $content) {
            $oDetails['order_id'] = $order['id'];
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;
            $oDetails['unitcost'] = $content->price;
            $oDetails['total'] = $content->subtotal;
            $oDetails['created_at'] = Carbon::now();

            OrderDetails::insert($oDetails);
        }

        // Deduct stock
        $stockAlertProducts = [];
        foreach ($contents as $content) {
            $product = Product::find($content->id);
            if ($product) {
                $newQty = $product->quantity - $content->qty;
                if ($newQty < $product->quantity_alert) {
                    $stockAlertProducts[] = $product;
                }
                $product->update(['quantity' => $newQty]);
            }
        }

        // Send stock alert email
        if (count($stockAlertProducts) > 0) {
            $adminEmails = User::pluck('email')->toArray();
            Mail::to($adminEmails)->send(new StockAlert($stockAlertProducts));
        }

        // Update customer points
        $pointsEarned = (int) floor($finalTotal / 100);
        $customer->update([
            'points' => $customer->points - $pointsUsed + $pointsEarned,
        ]);

        // Clear Cart after placing the order
        Cart::destroy();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been completed!');
    }

    public function show($uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        $order->loadMissing(['customer', 'details'])->get();

        return view('orders.show', [
            'order' => $order
        ]);
    }

    public function update($uuid, Request $request)
    {
        // Orders are now completed immediately at creation.
        // This method is kept for route compatibility.
        return redirect()->route('orders.index');
    }

    public function destroy($uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        $order->delete();
    }

    public function downloadInvoice($uuid)
    {
        $order = Order::with(['customer', 'details'])->where('uuid', $uuid)->firstOrFail();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }

    public function receiptHistory(Request $request)
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['customer', 'details']);

        if ($request->customer) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer . '%');
            });
        }

        if ($request->start_date) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        $orders = $query->orderBy('order_date', 'desc')->get();

        return view('orders.receipt-history', [
            'orders' => $orders,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'customerFilter' => $request->customer,
        ]);
    }

    public function cancel(Order $order)
    {
        $order->update([
            'order_status' => 2
        ]);
        $orders = Order::where('user_id', auth()->id())->count();

        return redirect()
            ->route('orders.index', [
                'orders' => $orders
            ])
            ->with('success', 'Order has been canceled!');
    }
}
