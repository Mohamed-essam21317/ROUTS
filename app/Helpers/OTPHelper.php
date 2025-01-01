<?php
namespace App\Helpers;

class OTPHelper
{
    public static function generateOTP()
    {
        return rand(1000, 9999);
    }
}

