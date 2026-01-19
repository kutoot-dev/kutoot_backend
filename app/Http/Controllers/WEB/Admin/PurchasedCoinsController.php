<?php
namespace App\Http\Controllers\WEB\Admin;
use App\Http\Controllers\Controller;
use App\Models\PurchasedCoins;
use App\Models\UserCoupons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PurchasedCoinsController extends Controller
{

    public function index()
    {
        $orders = PurchasedCoins::with('user')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

}
