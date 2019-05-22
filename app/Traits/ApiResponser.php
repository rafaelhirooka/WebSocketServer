<?php
/**
 * Created by PhpStorm.
 * User: rafael.hirooka
 * Date: 21/05/2019
 * Time: 11:23
 */

namespace App\Traits;
/**
 * Trait ApiResponser
 * @package App\Traits
 */
trait ApiResponser {

    private $codes = [
        100,101,102,103,200,201,202,203,204,205,206,207,208,226,300,301,302,303,304,305,306,307,308,
        400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,421,422,423,424,
        426,428,429,431,451,500,501,502,503,504,505,506,507,508,510,511
    ];

    public function successResponse($data, $code = 200) {
        $code = $this->checkCode($code) ? $code : 200;
        return json_encode(['data' => $data, 'code' => $code]);
    }

    public function errorResponse($message, $code) {
        $code = $this->checkCode($code) ? $code :400;

        return json_encode(['data' => ['message' => $message, 'code' => $code]]);
    }

    private function checkCode($code) {
        return $r = in_array($code, $this->codes) ? true : false;
    }
}