<?php

namespace App\Traits;

use Exception;

trait GlobalTrait
{

    public static function datatables($request, $query)
    {
        $orderBy = $request->orderby ?? 'created_at';
        $sortBy = $request->sortby && in_array($request->sortby, ['asc', 'desc']) ? $request->sortby : 'desc';
        $request->perPage ? $perPage = $request->perPage : $perPage = 10;
        $result = $query->orderBy($orderBy, (string)$sortBy)->paginate($perPage)->appends($request->all());
        return $result;
    }

    public static function formatRequestValidation($errors)
    {
        $messages = [];
        foreach ($errors as $key => $value) {
            foreach ($value as $key1 => $value2) {
                array_push($messages, $value2);
            }
        }
        $messages = json_encode($messages);
        return $messages;
    }

    public function phoneNumberValidation($phone)
    {
        $verifiedNumber = NULL;
        if ($phone) {
            $f = substr($phone, 0, 1);
            if ($f == "0") {
                $verifiedNumber = $phone;
                $r = substr($phone, 1, strlen($phone));
                $verifiedNumber = "62$r";
            } else if ($f == "+") {
                $r = substr($phone, 1, strlen($phone) - 1);
                $verifiedNumber = "62$r";
            } else if ($f == "6") {
                $r = substr($phone, 2, strlen($phone) - 2);
            } else {
                $verifiedNumber = $phone;
            }
        }
        return $verifiedNumber;
    }

    public function validatePhone($phoneNumber, $is_zero = true, $is_62 = false)
    {
        if ($is_62 && substr($phoneNumber, 0, 2) === "62") {
            return true;
        }
        if ($is_zero && substr($phoneNumber, 0, 1) === "0") {
            return true;
        }
    }

    public function validatePhoneDigit($phoneNumber)
    {
        if (strlen($phoneNumber) >= 11 && strlen($phoneNumber) <= 13) return true;
        return false;
    }

    public function groupByToArray($items, $single = false)
    {
        $data = [];
        foreach ($items as $key => $value) {
            array_push($data, $value);
        }
        if (count($data) > 0) {
            return $single == true ? $data[0] : $data;
        }
        return [];
    }


    public function getSourceApi()
    {
        $source = isset($_SERVER['HTTP_SOURCE']) ? $_SERVER['HTTP_SOURCE'] : 'mobilev2';
        return $source;
    }

    public static function jsonCheck($string)
    {
        $result = json_decode($string);
        if (json_last_error() === JSON_ERROR_NONE) {
            return TRUE;
        }
        return FALSE;
    }

    public function ApiException($message)
    {
        throw new Exception($message);
    }

    public function getUserAuth()
    {
        return auth('api')->user();
    }
}
