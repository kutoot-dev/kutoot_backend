<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinCampaigns;
use App\Models\PurchasedCoins;
use App\Models\UserCoins;
use App\Models\MasterPrize;
use App\Models\Winners;
use Validator;

class CoinCampaignController extends Controller
{
    public $types;
    function __construct()
    {
        $this->types = [
            0 => 'All',
            1 => 'Running',
            2 => 'Upcoming',
            3 => 'Completed',
            4 => 'Highest',
            5 => 'Best',
            6 => 'Latest',
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 0);
        $data = [
            'running' => collect([]),
            'upcoming' => collect([]),
            'completed' => collect([])
        ];
        switch ($type) {
            case 1:
                $data['running'] = CoinCampaigns::running()->get();
                break;
            case 2:
                $data['upcoming'] = CoinCampaigns::upcoming()->get();
                break;
            case 3:
                $data['completed'] = CoinCampaigns::completed()->get();
                break;
            default:
                $data['running'] = CoinCampaigns::running()->get();
                $data['upcoming'] = CoinCampaigns::upcoming()->get();
                $data['completed'] = CoinCampaigns::completed()->get();
                break;
        }
        $types = $this->types;
        return view('admin.coin-campaign.index', compact('data', 'type', 'types'));
    }


 public function purchasedindex(Request $request)
{
    $data = PurchasedCoins::where('payment_status', 'success')
    ->orderBy('id', 'desc')
    ->get();

    // Append coin purchase and expiry data from CoinLedger
    $data->transform(function ($purchase) {
        $coinLedgerEntry = \App\Models\CoinLedger::where('reference_id', 'OID' . $purchase->id)
            ->where('entry_type', \App\Models\CoinLedger::TYPE_PAID_CREDIT)
            ->first();
        $purchase->coins_purchased = $purchase->camp_coins_per_campaign * $purchase->quantity;
        $purchase->coin_expires_at = $coinLedgerEntry ? $coinLedgerEntry->expiry_date : null;
        return $purchase;
    });

    return view('admin.coin-campaign.purchaseindex', compact('data'));
}


    public function winnerslist(Request $request)
    {

        $data = Winners::with('campaign','campaign.campaign','userdetails')->orderBy('id', 'desc')->paginate(10);
        return response()->json($data);
    }


    public function homepage(Request $request)
    {
        $data=array();
        $data['winners'] = Winners::with('campaign')->orderBy('id', 'desc')->get();
        $data['banner'] = CoinCampaigns::first();
        $data['sub_banners'] = CoinCampaigns::orderBy('id', 'desc')->get()->take(3);

        return response()->json(['data'=>$data]);
    }


    public function prizeindex(Request $request)
    {

        $data = MasterPrize::all();
        return view('admin.coin-campaign.prizeindex', compact('data'));
    }


    public function showPurchaseDetails($id)
    {
        $purchase = PurchasedCoins::with('coupons', 'user')->findOrFail($id);

        return view('admin.coin-campaign.purchasdetails', compact('purchase'));
    }


    public function statementsindex()
    {
        $userCoins = UserCoins::with(['user', 'purchasedCampaign'])
            ->whereHas('user') // ✅ skip records whose user is deleted
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.coin-campaign.usercoinsindex', compact('userCoins'));
    }
    public function indexAPI(Request $request)
    {
         $type = $request->get('type', 0);
    $data = [
        'running' => collect([]),
        'upcoming' => collect([]),
        'completed' => collect([])
    ];

     switch ($type) {
    case 1:
        $data = CoinCampaigns::running();
        break;
    case 2:
        $data = CoinCampaigns::upcoming();
        break;
    case 3:
        $data = CoinCampaigns::completed();
        break;
    case 4: // Highest amount
        $data = CoinCampaigns::query()->orderByDesc('ticket_price');
        break;

    case 5: // Best deal (most coupons)
        $data = CoinCampaigns::query()->orderByDesc('total_tickets');
        break;

    case 6: // Latest created
        $data = CoinCampaigns::query()->orderByDesc('created_at');
        break;
    default:
        $data = CoinCampaigns::running();
        break;
}

        $data = $data->select('*', \DB::raw('total_tickets - sold_tickets as available'))->with(['baseplans'])->where('status', 1)->get();

        $data = $data->map(function ($campaign) {
            $manifest = $campaign->marketingManifest();
            return array_merge($campaign->toArray(), [
                'total_target' => 100,
                'marketing_message' => $manifest['message'],
                'progress' => $manifest['progress'],
                'display_percentage' => $manifest['display_percentage'],
            ]);
        });
        $types = [];
        foreach ($this->types as $key => $value) {
            // if ($key == 0) {
            //     continue; // Skip 'All' type for API response
            // }
            $types[] = ['id' => $key, 'name' => $value];
        }
        return response()->json(['data' => $data, 'type' => $type, 'types' => $types]);
    }

    public function changeStatus($id){
        $category = CoinCampaigns::find($id);
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
        return view('admin.coin-campaign.create');
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

        $validation = [
            'title' => 'required|string|max:255',
            'title1'=> 'nullable|string|max:255',
            'title2'=> 'nullable|string|max:255',
            'campaign_id' => 'required|string|unique:coin_campaigns,campaign_id|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'ticket_price' => 'required|numeric|min:0',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'total_tickets' => 'required|integer|min:1',
            'series_prefix' => 'required|string|size:1',
            'number_min' => 'required|integer|min:1',
            'number_max' => 'required|integer|gt:number_min',
            'numbers_per_ticket' => 'required|integer|min:2|max:6',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/x-ms-wmv,video/webm|max:51200',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // Assuming image1 and image2 are optional
            'winner_announcement_date' => 'nullable|date', // Assuming winner announcement date is optional
            'tag1' => 'nullable|string|max:255',
            'tag2' => 'nullable|string|max:255',
            'marketing_start_percent' => 'required|numeric|min:0|max:100',

        ];
        $validator = Validator::make($data, $validation);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
$data['img'] = handleImageUpload($request, 'img', 'coin-campaign');
$data['image1'] = handleImageUpload($request, 'image1', 'image1');
$data['image2'] = handleImageUpload($request, 'image2', 'image2');


        if ($request->hasFile('video')) {
            $image = $request->file('video');
            $imageName = 'coin-video-'.time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/website-images/coin_campaigns'), $imageName);
            $data['video'] = 'uploads/website-images/coin_campaigns/' . $imageName;
        } else {
            $data['video'] = null;
        }

        $data['tags'] = json_encode([]);


$finalHighlights = [];

foreach ($request->highlights as $obj) {
    $merged = [];
    foreach ($obj['key'] as $i => $k) {
        if (!empty($k)) {
            $merged[$k] = $obj['value'][$i] ?? '';
        }
    }
    $finalHighlights[] = $merged;
}

$data['highlights'] = $finalHighlights;

        $create = CoinCampaigns::create($data);
        if ($create) {
            // $notification= trans('admin_validation.Created Successfully');
            // $notification = ['messege' => $notification, 'alert-type' => 'success'];
            // return redirect()->route('admin.all-coin-campaigns');
            return redirect()->route('admin.all-coin-campaigns')
    ->with('messege', trans('admin_validation.Created Successfully'))
    ->with('alert-type', 'success');

        } else {
            $notification= 'Failed to create Coin Campaign.';
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->withInput()->with($notification);
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
        $data = CoinCampaigns::with('baseplans')->find($id);
        $manifest = $data->marketingManifest();
        $data['total_target']=100;
        $data['marketing_message']=$manifest['message'];
        $data['progress']=$manifest['display_percentage'];

        return response()->json(['data' => $data]);
    }


    public function show($id)
    {
        $data = CoinCampaigns::find($id);
        return view('admin.coin-campaign.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = CoinCampaigns::find($id);

        return view('admin.coin-campaign.edit',compact('data'));
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

    $validation = [
        'title' => 'required|string|max:255',
        'title1'=> 'nullable|string|max:255',
        'campaign_id' => 'required|unique:coin_campaigns,campaign_id,' . $id,
        'title2'=> 'nullable|string|max:255',
        'ticket_price' => 'required|numeric|min:0',
        'short_description' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/x-ms-wmv,video/webm|max:51200',
        'total_tickets' => 'required|integer|min:1',
        'series_prefix' => 'required|string|size:1',
        'number_min' => 'required|integer|min:1',
        'number_max' => 'required|integer|gt:number_min',
        'numbers_per_ticket' => 'required|integer|min:2|max:6',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'winner_announcement_date' => 'nullable|date',
        'tag1' => 'nullable|string|max:255',
        'tag2' => 'nullable|string|max:255',
        'marketing_start_percent' => 'required|numeric|min:0|max:100',
         'highlights' => 'nullable|array',

    ];

    $validator = Validator::make($data, $validation);
  $highlightsInput = $request->input('highlights', []);
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        $record = CoinCampaigns::findOrFail($id);

        // VIDEO
        if ($request->hasFile('video')) {
            if ($record->video && file_exists(public_path($record->video))) {
                unlink(public_path($record->video));
            }

            $video = $request->file('video');
            $videoName = 'coin-video-' . time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('uploads/website-images/coin_campaigns'), $videoName);
            $data['video'] = 'uploads/website-images/coin_campaigns/' . $videoName;
        } else {
            $data['video'] = $record->video;
        }

        // IMG
        $data['img'] = $request->hasFile('img')
            ? (function () use ($request, $record) {
                if ($record->img && file_exists(public_path($record->img))) {
                    unlink(public_path($record->img));
                }
                return handleImageUpload($request, 'img', 'coin-campaign');
            })()
            : $record->img;

        // IMAGE1
        $data['image1'] = $request->hasFile('image1')
            ? (function () use ($request, $record) {
                if ($record->image1 && file_exists(public_path($record->image1))) {
                    unlink(public_path($record->image1));
                }
                return handleImageUpload($request, 'image1', 'image1');
            })()
            : $record->image1;

        // IMAGE2
        $data['image2'] = $request->hasFile('image2')
            ? (function () use ($request, $record) {
                if ($record->image2 && file_exists(public_path($record->image2))) {
                    unlink(public_path($record->image2));
                }
                return handleImageUpload($request, 'image2', 'image2');
            })()
            : $record->image2;


            $finalHighlights = [];
    foreach ($highlightsInput as $obj) {
        $merged = [];
        if (isset($obj['key']) && is_array($obj['key'])) {
            foreach ($obj['key'] as $i => $k) {
                $k = trim($k);
                if ($k !== '') {
                    $merged[$k] = $obj['value'][$i] ?? '';
                }
            }
        }
        if (!empty($merged)) {
            $finalHighlights[] = $merged;
        }
    }
    $data['highlights'] = $finalHighlights;
        // Update record

        $record->update($data);

        return redirect()->route('admin.all-coin-campaigns')->with([
            'messege' => trans('admin_validation.Updated Successfully'),
            'alert-type' => 'success'
        ]);

    } catch (\Exception $e) {
        // fallback: update failed
        return redirect()->back()->withInput()->with([
            'messege' => 'Failed to update Coin Campaign.',
            'alert-type' => 'error'
        ]);
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
        $data = CoinCampaigns::find($id);
        if ($data) {
            try {
                if ($data->img && file_exists(public_path($data->img))) {
                    unlink(public_path($data->img));
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting Coin campaign image at path '. $data->img .': ' . $e->getMessage());
            }
            $data->delete();
            $notification = 'Deleted Successfully';
            $notification = ['messege' => $notification, 'alert-type' => 'success'];
        } else {
            $notification = ['messege' => 'No record found', 'alert-type' => 'error'];
        }
        return redirect()->route('admin.all-coin-campaigns')->with($notification);
    }


}
