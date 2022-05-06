<?php

/**
 * Setting Middleware for person accounts.
 */
Flight::route('/person/*', function () {
    try {
        $person = (array)\Firebase\JWT\JWT::decode(Flight::header("Authentication"), Config::JWT_SECRET, ["HS256"]);
        if (Flight::request()->method != "GET" && $person["r"] == "USER_READ_ONLY") {
            throw new Exception("You can't change anything.", 403);
        }
        Flight::set('person', $person);
        return TRUE;
    } catch (\Exception $e) {
        Flight::json(["message" => $e->getMessage()], 401);
        die;
    }
});

/**
 * Setting Middleware for admin accounts.
 */
Flight::route('/admin/*', function () {
    try {
        $person = (array)\Firebase\JWT\JWT::decode(Flight::header("Authentication"), Config::JWT_SECRET, ["HS256"]);
        if ($person["r"] != "ADMIN") {
            throw new Exception("Admin access required.", 403);
        }
        Flight::set('person', $person);
        return TRUE;
    } catch (\Exception $e) {
        Flight::json(["message" => $e->getMessage()], 401);
        die;
    }
});
