<?php

require __DIR__ . "/vendor/autoload.php";

use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Router;
use Amp\Http\Server\Server;
use Amp\Http\Status;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Socket;
use Monolog\Logger;
use Psr\Log\NullLogger;
use Bluzelle\Client;

Amp\Loop::run(function () {
    $servers = [
        Socket\listen("0.0.0.0:1337"),
        Socket\listen("[::]:1337"),
    ];

    $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
    $logHandler->setFormatter(new ConsoleFormatter);
    $logger = new Logger('server');
    $logger->pushHandler($logHandler);

    $router = new Router;

    $router->addRoute('POST', '/', new CallableRequestHandler(function (Request $request) {
        $client = new Client(
            'bluzelle1upsfjftremwgxz3gfy0wf3xgvwpymqx754ssu9',
            'around buzz diagram captain obtain detail salon mango muffin brother morning jeans display attend knife carry green dwarf vendor hungry fan route pumpkin car',
            'http://testnet.public.bluzelle.com:1317',
            'bluzelle',
            '20fc19d4-7c9d-4b5c-9578-8cedd756e0ea'
        );

        $body = yield $request->getBody()->buffer();
        $params = json_decode($body, true);

        $methodName = $params['method'];
        $args = $params['args'];

        if (!isset($methodName) || !isset($args)) {
            return new Response(Status::BAD_REQUEST, ['content-type' => 'text/plain'], 'ArgumentError: Please provide method name and args');
        }

        try {
            $result = $client->{$methodName}(...$args);
            return new Response(Status::OK, ['content-type' => 'text/plain'], json_encode($result));
        } catch (Exception $e) {
            return new Response(Status::BAD_REQUEST, ['content-type' => 'text/plain'], $e->getMessage());
        }

    }));

    $server = new Server($servers, $router, $logger);
    
    yield $server->start();

    // Stop the server when SIGINT is received (this is technically optional, but it is best to call Server::stop()).
    Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
        Amp\Loop::cancel($watcherId);
        yield $server->stop();
    });
});
