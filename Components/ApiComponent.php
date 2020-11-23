<?php

class ApiComponent {

    public static function jsonResponse($data , $code) {
        http_response_code($code);
        header("Content-Type: application/json; charset=UTF-8");
        print_r(json_encode($data, true));
    }
}