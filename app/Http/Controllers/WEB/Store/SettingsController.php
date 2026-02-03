<?php

namespace App\Http\Controllers\WEB\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\SellerBankAccount;
use App\Models\Store\SellerNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @group Settings
 */
class SettingsController extends Controller
{
    public function changePasswordForm()
    {
        return view('store.settings.change_password');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $seller = Auth::guard('store')->user();

        if (!Hash::check($request->input('old_password'), $seller->password)) {
            return redirect()->back()->withErrors(['old_password' => 'Old password incorrect']);
        }

        $seller->password = Hash::make($request->input('new_password'));
        $seller->save();

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function bankForm()
    {
        $seller = Auth::guard('store')->user();
        $bank = SellerBankAccount::query()->where('seller_id', $seller->id)->first();

        return view('store.settings.bank', [
            'bank' => $bank,
        ]);
    }

    public function updateBank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'ifsc' => ['required', 'string', 'max:20', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upi_id' => ['nullable', 'string', 'max:255'],
            'beneficiary_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $seller = Auth::guard('store')->user();

        SellerBankAccount::query()->updateOrCreate(
            ['seller_id' => $seller->id],
            [
                'bank_name' => $request->input('bank_name'),
                'account_number' => $request->input('account_number'),
                'ifsc' => strtoupper($request->input('ifsc')),
                'upi_id' => $request->input('upi_id'),
                'beneficiary_name' => $request->input('beneficiary_name'),
            ]
        );

        return redirect()->back()->with('success', 'Bank details updated successfully');
    }

    public function notificationsForm()
    {
        $seller = Auth::guard('store')->user();
        $n = SellerNotificationSetting::query()->firstOrCreate(
            ['seller_id' => $seller->id],
            [
                'enabled' => true,
                'email' => true,
                'sms' => false,
                'whatsapp' => true,
            ]
        );

        return view('store.settings.notifications', [
            'n' => $n,
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => ['nullable', 'boolean'],
            'email' => ['nullable', 'boolean'],
            'sms' => ['nullable', 'boolean'],
            'whatsapp' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $seller = Auth::guard('store')->user();

        SellerNotificationSetting::query()->updateOrCreate(
            ['seller_id' => $seller->id],
            [
                'enabled' => (bool) $request->boolean('enabled'),
                'email' => (bool) $request->boolean('email'),
                'sms' => (bool) $request->boolean('sms'),
                'whatsapp' => (bool) $request->boolean('whatsapp'),
            ]
        );

        return redirect()->back()->with('success', 'Notification preferences updated');
    }
}


