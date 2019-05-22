<?php
/**
 * Created by PhpStorm.
 * User: rafael.hirooka
 * Date: 21/05/2019
 * Time: 11:27
 */

namespace App\Traits;


trait Response {

    public static function json($code, $data) {
        http_response_code($code);
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($data);
    }
}