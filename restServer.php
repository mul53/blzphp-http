<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Bluzelle\Client;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($uri != '/') {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['message' => 'Not Found']);
}

if ($requestMethod != 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['message' => 'Method Not Allowed']);
}

$client = new Client(
    getenv('ADDRESS'),
    getenv('MNEMONIC'),
    getenv('ENDPOINT'),
    'bluzelle',
    getenv('UUID')
);

$params = (array) json_decode(file_get_contents('php://input'), TRUE);;

$methodName = $params['method'];
$args = $params['args'];

if (!isset($methodName) || !isset($args)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode('ArgumentError: Please provide the method name and args');
}

try {
    header("HTTP/1.1 200 OK");
    $result = $client->{$methodName}(...$args);
    echo json_encode($result);
} catch (Exception $e) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode($e->getMessage());
} catch (TypeError $e) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode('ArgumentError: Please provide the right argument type');
}

