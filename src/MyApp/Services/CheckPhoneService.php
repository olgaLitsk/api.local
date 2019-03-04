<?php

namespace MyApp\Services;

use Silex\Application;

class CheckPhoneService
{
    public function CurlPhoneValidation($phone, $access_key){
        //  API Access Key -$app['access_key ']
        // Initialize CURL:
        $ch = curl_init('http://apilayer.net/api/validate?access_key='.$access_key.'&number='.$phone.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);
        $validationResult = json_decode($json, true);
        if (!$validationResult['valid']){
            return false;
        }else{
            return $validationResult['international_format'];
        }
    }

}

