<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/routes/middleware.php';
require_once dirname(__FILE__) . '/routes/accounts.php';
require_once dirname(__FILE__) . '/routes/person.php';
require_once dirname(__FILE__) . '/routes/letter.php';
require_once dirname(__FILE__) . '/services/AccountService.class.php';
require_once dirname(__FILE__) . '/services/PersonService.class.php';
require_once dirname(__FILE__) . '/services/LetterService.class.php';
require_once dirname(__FILE__) . '/services/ReceiverService.class.php';
require_once dirname(__FILE__) . '/services/CommunicationService.class.php';

Flight::set('flight.log_errors', TRUE);

/* Error handling for API
Flight::map('error', function (Exception $ex) {
    // it handles error and changes html format to application/json format.
    Flight::json(["message" => $ex->getMessage()], $ex->getCode() ? $ex->getCode() : 500);
});*/

// This method parse query parameters.
Flight::map('query', function ($name, $default_value = '') {
    $request = Flight::request();
    $query_param = @$request->query->getData()[$name];
    $query_param = $query_param ? $query_param : $default_value;
    return urldecode($query_param);
});

Flight::map('header', function ($name) {
    $headers = getallheaders();
    return @$headers[$name];
});

/* utility function for generating JWT token */
Flight::map('jwt', function ($person) {
    $jwt = \Firebase\JWT\JWT::encode([
        "exp" => (time() + Config::JWT_TOKEN_TIME),
        "id" => $person["id"], "aid" => $person["account_id"],
        "r" => $person["role"]
    ], Config::JWT_SECRET);
    return ["token" => $jwt];
});

Flight::route('GET /swagger', function () {
    $openapi = @\OpenApi\scan(dirname(__FILE__) . '/routes');
    header('Content-Type: application/json');
    echo $openapi->toJson();
});

Flight::route('GET /', function () {
    Flight::redirect('/docs');
});

Flight::register('accountService', 'AccountService');
Flight::register('personService', 'PersonService');
Flight::register('letterService', 'LetterService');
Flight::register('receiverService', 'ReceiverService');
Flight::register('communicationService', 'CommunicationService');

Flight::start();
