<?php

use App\Custom\ApiResponse;

if (! function_exists('apiResponse')) {
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    function apiResponse($content = '', $status = 200, array $headers = [])
    {
        if (!app()->has(ApiResponse::class)) {
            app()->singleton(ApiResponse::class);
        }
        $factory = app()->get(ApiResponse::class);

        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->make($content, $status, $headers);
    }
}
