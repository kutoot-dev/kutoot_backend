<?php

namespace App\Http\Controllers\WEB\Admin;

use Illuminate\Http\Request;
use App\Models\PurchasedCoins;
use App\Models\UserCoupons;
use App\Models\CoinCampaigns;
use App\Models\Winners;
use App\Http\Controllers\Controller;

class WinnerController extends Controller
{
    public function create($camp_id)
    {
        $campaigns = CoinCampaigns::find($camp_id);

        // $coupons = UserCoupons::whereHas('purchasedCampaign', function ($q) use ($camp_id) {
        //     $q->where('camp_id', $camp_id);
        // })->get(['id', 'coupon_code']);

        $coupons = UserCoupons::whereHas('purchasedCampaign', function ($q) use ($camp_id) {
                $q->where('camp_id', $camp_id);
            })->get(['id', 'coupon_code']);

        return view('admin.winnerscreate', compact('campaigns','coupons'));
    }

    public function getCoupons($camp_id)
    {
        $coupons = UserCoupons::whereHas('purchasedCampaign', function ($q) use ($camp_id) {
            $q->where('camp_id', $camp_id);
        })->get(['id', 'coupon_code']);
        
        return response()->json($coupons);
    }


    public function index()
    {
        $winners = Winners::all();

        return view('admin.winner', compact('winners'));
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'camp_id' => 'required',
        //     'coupon_id' => 'required',
        //     'winning_date' => 'required|date',
        // ]);

        // return redirect()->back()->with('success', 'Winner announced successfully!');
        // print_r($request->all());
        // die();

        $coupon = UserCoupons::findOrFail($request->coupon_id);

        Winners::create([
            'camp_id' => $request->camp_id,
            'purchased_camp_id' => $coupon->purchased_camp_id,
            'coupon_id' => $coupon->id,
            'coupon_number' => $coupon->coupon_code,
            'user_id' => $coupon->purchasedCampaign->user_id,
            'announcing_date' => $request->announcing_date,
            'is_claimed' => 0,
            'prize_details' => $request->prize_details,
            'prize_id' => null,
            'status' => 1,
        ]);


        return redirect()->route('admin.winners.index')->with('success', 'Winner announced successfully!');
    }

    public function delete($id)
    {
        $testimonial = Winners::find($id);
        $testimonial->delete();

       

        $notification = trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.winners.index')->with($notification);
    }
}
