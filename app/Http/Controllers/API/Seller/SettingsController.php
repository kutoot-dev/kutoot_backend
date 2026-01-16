<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store\Seller;
use App\Models\Store\SellerBankAccount;
use App\Models\Store\SellerNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();

        if (!Hash::check($request->input('oldPassword'), $seller->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Old password incorrect',
            ], 401);
        }

        $seller->password = Hash::make($request->input('newPassword'));
        $seller->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    public function getBank()
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();
        $bank = SellerBankAccount::query()->where('seller_id', $seller->id)->first();

        $masked = null;
        if ($bank?->account_number) {
            $last4 = substr($bank->account_number, -4);
            $masked = 'XXXXXX' . $last4;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bankName' => $bank?->bank_name,
                'accountNumber' => $masked,
                'ifsc' => $bank?->ifsc,
                'upiId' => $bank?->upi_id,
                'beneficiaryName' => $bank?->beneficiary_name,
            ],
        ]);
    }

    public function updateBank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bankName' => ['required', 'string', 'max:255'],
            'accountNumber' => ['required', 'string', 'max:50'],
            'ifsc' => ['required', 'string', 'max:20', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upiId' => ['nullable', 'string', 'max:255'],
            'beneficiaryName' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();

        SellerBankAccount::query()->updateOrCreate(
            ['seller_id' => $seller->id],
            [
                'bank_name' => $request->input('bankName'),
                'account_number' => $request->input('accountNumber'),
                'ifsc' => strtoupper($request->input('ifsc')),
                'upi_id' => $request->input('upiId'),
                'beneficiary_name' => $request->input('beneficiaryName'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bank details updated successfully',
        ]);
    }

    public function getNotifications()
    {
        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();

        $n = SellerNotificationSetting::query()->firstOrCreate(
            ['seller_id' => $seller->id],
            [
                'enabled' => true,
                'email' => true,
                'sms' => false,
                'whatsapp' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'enabled' => (bool) $n->enabled,
                'channels' => [
                    'email' => (bool) $n->email,
                    'sms' => (bool) $n->sms,
                    'whatsapp' => (bool) $n->whatsapp,
                ],
            ],
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => ['required', 'boolean'],
            'channels.email' => ['required', 'boolean'],
            'channels.sms' => ['required', 'boolean'],
            'channels.whatsapp' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /** @var Seller $seller */
        $seller = Auth::guard('store-api')->user();

        $channels = (array) $request->input('channels', []);

        SellerNotificationSetting::query()->updateOrCreate(
            ['seller_id' => $seller->id],
            [
                'enabled' => (bool) $request->input('enabled'),
                'email' => (bool) ($channels['email'] ?? false),
                'sms' => (bool) ($channels['sms'] ?? false),
                'whatsapp' => (bool) ($channels['whatsapp'] ?? false),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated',
        ]);
    }
}


