<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLoginInformation extends Model
{
    use HasFactory;

    public function setGoogleLoginInfo(){
        $googleInfo = SocialLoginInformation::first();
        if($googleInfo){
            \Config::set('services.google.client_id', $googleInfo->gmail_client_id);
            \Config::set('services.google.client_secret', $googleInfo->gmail_secret_id);
            \Config::set('services.google.redirect', $googleInfo->gmail_redirect_url);
        }
    }

    public function setFacebookLoginInfo(){
        $facebookInfo = SocialLoginInformation::first();
        if($facebookInfo){
            \Config::set('services.facebook.client_id', $facebookInfo->facebook_client_id);
            \Config::set('services.facebook.client_secret', $facebookInfo->facebook_secret_id);
            \Config::set('services.facebook.redirect', $facebookInfo->facebook_redirect_url);
        }
    }


}
