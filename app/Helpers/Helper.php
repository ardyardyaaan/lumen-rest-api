<?php

use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

if (!function_exists('responses')) {
    function responses($code = 200, $status = true, $message = 'Success', $data, $page, $per_page, $total)
    {
        $results = array();

        $results['status'] = $status;

        if (!empty($data) && $message == null) {
            $results['message'] = "Success!";
        } else if (empty($data) && $message == null) {
            $results['message'] = "Data not found!";
        } else {
            $results['message'] = $message;
        }

        if ($page != null && $per_page != null && $total != null) {
            $results['page'] = (int)$page;
            $results['limit'] = (int)$per_page;
            $results['total_page'] = ceil($total / $per_page);
        }

        if ($total != null) {
            $results['total_data'] = (int)$total;
        }

        $results['data'] = $data;

        return response()->json($results)->setStatusCode($code);
    }
}

if (!function_exists('errorQuery')) {
    function errorQuery($message)
    {
        $statusCode = 500;
        $resultPrint = [];
        $resultPrint['status'] = false;
        $resultPrint['message'] = 'form-error';
        $resultPrint['data'] = $message->errorInfo[2];

        return response()->json($resultPrint)->setStatusCode($statusCode);
    }
}

if (!function_exists('generateOtp')) {
    function generateOtp($userId)
    {
        try {
            $data = Otp::where('user_id', $userId)->first();
            if ($data) {
                $dateTimeNow = Carbon::now()->format('Y-m-d H:i:s');
                // $createdDateTime = Carbon::parse($data['created_at']);
                $expiredDateTime = Carbon::parse($data['created_at'])->addMinutes()->format('Y-m-d H:i:s');
                if ($dateTimeNow > $expiredDateTime) {
                    $data->deleted_at = Carbon::now()->format('Y-m-d H:i:s');
                    $data->deleted_by = 'System';
                    $data->save();
                } else {
                    $data = Otp::create([
                        'user_id'   => $userId,
                        'otp'       => rand(100000,999999),
                        'created_by'=> $userId,
                        'updated_by'=> $userId
                    ]);
                }
            } else {
                $data = Otp::create([
                    'user_id'   => $userId,
                    'otp'       => rand(100000,999999),
                    'created_by'=> $userId,
                    'updated_by'=> $userId
                ]);
            }

            return  $data;
        } catch (QueryException $e) {
            return errorQuery($e);
        }
    }
}