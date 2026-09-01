<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillId;
use App\Models\Customer;
use App\Models\Product;
use App\Models\BillItem;

class DashboardController extends Controller
{
    public function index()
    {
        // Get last bill number
        $lastBill = BillId::orderBy('id', 'desc')->first();
        $lastBillNumber = $lastBill ? $lastBill->bill_id : 'N/A';
        
        // Count totals
        $totalBills = BillId::count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        
        // Total sales
        $totalSales = BillItem::sum('total');
        
        // Recent bills
        $recentBills = Bill::with(['customer', 'items'])
            ->orderBy('dateid', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboard.index', compact(
            'lastBillNumber',
            'totalBills',
            'totalProducts',
            'totalCustomers',
            'totalSales',
            'recentBills'
        ));
    }
}