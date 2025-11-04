<?php

namespace App\Http\Controllers;

use App\Models\CouponCampaign;
use Illuminate\Http\Request;

class CouponCampaignController extends Controller
{
    public function index()
    {
        $campaigns = CouponCampaign::latest()->get();
        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.campaigns.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:coupon_campaigns',
            'series_prefix' => 'required|string|size:1',
            'number_min' => 'required|integer|min:1',
            'number_max' => 'required|integer|gt:number_min',
            'numbers_per_ticket' => 'required|integer|min:2|max:6',
            'goal_target' => 'nullable|integer|min:0',
        ]);

        CouponCampaign::create($data);
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created!');
    }

    public function show($id)
    {
        $campaign = CouponCampaign::with('tickets')->findOrFail($id);
        return view('admin.campaigns.show', compact('campaign'));
    }

    public function edit($id)
    {
        $campaign = CouponCampaign::findOrFail($id);
        return view('admin.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, $id)
    {
        $campaign = CouponCampaign::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|unique:coupon_campaigns,name,' . $campaign->id,
            'series_prefix' => 'required|string|size:1',
            'number_min' => 'required|integer|min:1',
            'number_max' => 'required|integer|gt:number_min',
            'numbers_per_ticket' => 'required|integer|min:2|max:6',
            'goal_target' => 'nullable|integer|min:0',
        ]);

        $campaign->update($data);
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated!');
    }

    public function destroy($id)
    {
        CouponCampaign::findOrFail($id)->delete();
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign deleted!');
    }
}

