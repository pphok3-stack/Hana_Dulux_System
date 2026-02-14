<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Requests\Invoice\StoreInvoiceRequest;

class InvoiceController extends Controller
{
    public function create(StoreInvoiceRequest $request, Customer $customer)
    {
        $customer = Customer::where('id', $request->get('customer_id'))
            ->first();

        $carts = Cart::content();
        $outstandingBalance = $customer->total_due;

        return view('invoices.create', [
            'customer' => $customer,
            'carts' => $carts,
            'outstandingBalance' => $outstandingBalance,
        ]);
    }
}
