<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load('orders');

        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
