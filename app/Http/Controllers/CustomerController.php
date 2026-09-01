<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $bill_id = $request->query('bill_id');
        $customers = Customer::orderBy('customer_id', 'desc')->get();
        
        return view('customers.index', compact('customers', 'bill_id'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'gst' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:100',
            'panno' => 'nullable|string|max:50',
            'adharno' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $customer = Customer::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'gstnumber' => $request->gst,
                'state' => $request->state,
                'panno' => $request->panno,
                'adharno' => $request->adharno
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer added successfully!',
                    'customer_id' => $customer->customer_id,
                    'customer' => [
                        'customer_id' => $customer->customer_id,
                        'name' => $customer->name,
                        'address' => $customer->address,
                        'phone' => $customer->phone,
                        'gstnumber' => $customer->gstnumber,
                        'state' => $customer->state,
                        'panno' => $customer->panno,
                        'adharno' => $customer->adharno
                    ]
                ]);
            }

            if ($request->has('bill_id')) {
                return redirect()->route('customers.index', ['bill_id' => $request->bill_id])
                    ->with('success', 'Customer added successfully!');
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer added successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Error saving customer: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * Display the specified customer.
     */
    public function show($id, Request $request)
    {
        $customer = Customer::find($id);
        
        if (!$customer) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Customer not found'], 404);
            }
            abort(404);
        }
        
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($customer);
        }
        
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        $bill_id = $request->query('bill_id');
        return view('customers.edit', compact('customer', 'bill_id'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'gst' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:100',
            'panno' => 'nullable|string|max:50',
            'adharno' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer = Customer::findOrFail($id);
        $customer->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'gstnumber' => $request->gst,
            'state' => $request->state,
            'panno' => $request->panno,
            'adharno' => $request->adharno
        ]);

        if ($request->has('bill_id')) {
            return redirect()->route('customers.index', ['bill_id' => $request->bill_id])
                ->with('success', 'Customer updated successfully!');
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        
        // Check if customer has bills
        if ($customer->bills()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete customer with existing bills!');
        }
        
        $customer->delete();
        
        if ($request->has('bill_id')) {
            return redirect()->route('customers.index', ['bill_id' => $request->bill_id])
                ->with('success', 'Customer deleted successfully!');
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Search customers (for AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $customers = Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        return response()->json($customers);
    }
}