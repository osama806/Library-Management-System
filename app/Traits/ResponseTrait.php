<?php

namespace App\Traits;

trait ResponseTrait
{

    /**
     * Get dynamic response
     * @param mixed $key
     * @param mixed $val
     * @param mixed $statusCode
     * @return \Illuminate\Http\Response
     */
    public function getResponse($key, $val, $statusCode)
    {
        return response([
            "isSuccess"    => ($statusCode >= 200 && $statusCode < 300) ? true : false,
            $key          =>       $val
        ], $statusCode);
    }
}
