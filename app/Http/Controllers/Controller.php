<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    function success(string $message = '', int $status = 200)
    {
        return $this->result($message, $status, true);
    }

    /**
     * @param Exception $e
     * @return JsonResponse
     */
    function error(Exception $e)
    {
        $status = 500;
        if ($e->getCode() > 0) {
            $status = $e->getCode();
        }
        return $this->result($e->getMessage(), $status, false);
    }

    /**
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    function errorWith(string $message, int $status = 500)
    {
        if (!$status)
            $status = 500;
        return $this->result($message, $status, false);
    }

    /**
     * @param string $message
     * @param int $status
     * @param bool $is_succeeded
     * @return JsonResponse
     */
    function result(string $message, int $status, bool $is_succeeded)
    {
        return Response::json(array('success' => $is_succeeded, 'message' => $message), $status);
    }
}
