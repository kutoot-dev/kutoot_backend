<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinCampaigns;
use App\Models\Baseplans;
use App\Models\PurchasedCoins;
use App\Models\UserCoins;
use App\Models\MasterPrize;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\BaseplanCampaignLinked;

class BasePlanController extends Controller
{
    public $types;
    function __construct()
    {
        $this->types = [
            0 => 'All',
            1 => 'Running',
            2 => 'Upcoming',
            3 => 'Completed'
        ];
    }
   
    public function index(Request $request)
    {
        $data = Baseplans::all();
        return view('admin.baseplan.index', compact('data'));
    }
    

    public function indexAPI(Request $request)
    {
        
        $data = Baseplans::all();
        return response()->json(['data' => $data]);
    }

    public function changeStatus($id){
        $category = Baseplans::find($id);
        if($category->status==1){
            $category->status=0;
            $category->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $category->status=1;
            $category->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $campaigns = CoinCampaigns::all();
        return view('admin.baseplan.create',compact('campaigns'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // print_r($data['camp_id']);die();

        $validation = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ticket_price' => 'required|numeric|min:0',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'coins_per_campaign' => 'required|integer|min:1',
            'coupons_per_campaign' => 'required|integer|min:0',
            'duration' => 'required|string',
        ];

        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = 'baseplan-'.time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/website-images/coin_campaigns'), $imageName);
            $data['img'] = 'uploads/website-images/coin_campaigns/' . $imageName;
        } else {
            $data['img'] = null;
        }

        $create = Baseplans::create($data);

        if (!empty($data['camp_id_list']) && is_array($data['camp_id_list'])) {
            foreach ($data['camp_id_list'] as $campaignId) {
                BaseplanCampaignLinked::create([
                    'baseplan_id' => $create->id,
                    'campaign_id' => $campaignId,
                ]);
            }
        }

        if ($create) {
            $notification= trans('admin_validation.Created Successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];
            return redirect()->route('admin.all-baseplans');
        } else {
            $notification= 'Failed to create Coin Campaign.';
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function details($id)
    {
        $data = Baseplans::find($id);
        return response()->json(['data' => $data]);
    }

    
    public function show($id)
    {
        $data = Baseplans::find($id);
        return view('admin.baseplan.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Baseplans::find($id);
        $campaigns = CoinCampaigns::all();
     $selectedCampaigns = $data->campaigns->pluck('id')->toArray();
        return view('admin.baseplan.edit',compact('data','campaigns','selectedCampaigns'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        // dd($data);
        $validation = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ticket_price' => 'required|numeric|min:0',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            // 'total_tickets' => 'required|integer|min:1',
            'coins_per_campaign' => 'required|integer|min:1',
            'coupons_per_campaign' => 'required|integer|min:0',
            'duration' => 'required|string',
            // 'max_coins_per_transaction' => 'required|integer|min:1|max:100',
            // 'start_date' => 'required|date',
            // 'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
        $validator = Validator::make($data, $validation);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $hasImage = $request->hasFile('img');
        if ($hasImage) {
            $image = $request->file('img');
            $imageName = 'baseplan-'.time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/website-images/coin_campaigns'), $imageName);
            $data['img'] = 'uploads/website-images/coin_campaigns/' . $imageName;
        } 
      $record = Baseplans::findOrFail($id);

        if ($record) {
            // Delete old image only if new image is uploaded
            if ($hasImage && $record->img && file_exists(public_path($record->img))) {
                unlink(public_path($record->img));
            }

            // If no new image, keep the old image path
            if (!$hasImage) {
                $data['img'] = $record->img;
            }

            $record->update($data);

             BaseplanCampaignLinked::where('baseplan_id',$record->id)->delete();

            if (!empty($data['camp_id_list']) && is_array($data['camp_id_list'])) {
                foreach ($data['camp_id_list'] as $campaignId) {
                    BaseplanCampaignLinked::create([
                        'baseplan_id' => $record->id,
                        'campaign_id' => $campaignId,
                    ]);
                }
            }

            $notification= trans('admin_validation.Updated Successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];
            return redirect()->route('admin.all-baseplans');
        } else {
            $notification= 'Failed to update Coin Campaign.';
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Baseplans::find($id);
        if ($data) {
            try {
                if ($data->img && file_exists(public_path($data->img))) {
                    unlink(public_path($data->img));
                }
            } catch (\Exception $e) {
                Log::error('Error deleting Coin campaign image at path '. $data->img .': ' . $e->getMessage());
            }
            $data->delete();
            $notification = 'Deleted Successfully';
            $notification = ['messege' => $notification, 'alert-type' => 'success'];
        } else {
            $notification = ['messege' => 'No record found', 'alert-type' => 'error'];
        }
        return redirect()->route('admin.all-baseplans')->with($notification);
    }

   

}
