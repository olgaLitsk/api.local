<?php

namespace MyApp\Services;

class CheckPhoneService
{
    public function CurlPhoneValidation($phone){
        // set API Access Key -9903d695c5953b3b26aa028e9f853912
        $access_key = '9903d695c5953b3b26aa028e9f853911';

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

