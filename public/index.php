<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Telnyx;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../.env');
$dotenv->load();

$TELNYX_API_KEY    = $_ENV['TELNYX_API_KEY'];
$TELNYX_PUBLIC_KEY = $_ENV['TELNYX_PUBLIC_KEY'];

Telnyx\Telnyx::setApiKey($TELNYX_API_KEY);
Telnyx\Telnyx::setPublicKey($TELNYX_PUBLIC_KEY);
// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

$conference = '';
$calls = array();

//Callback signature verification
$telnyxWebhookVerify = function (Request $request, RequestHandler $handler) {
    $payload = $request->getBody()->getContents();
    $sigHeader = $request->getHeader('HTTP_TELNYX_SIGNATURE_ED25519')[0];
    $timeStampHeader = $request->getHeader('HTTP_TELNYX_TIMESTAMP')[0];
    $telnyxEvent = \Telnyx\Webhook::constructEvent($payload, $sigHeader, $timeStampHeader);
    $request = $request->withAttribute('telnyxEvent', $telnyxEvent);
    $response = $handler->handle($request);
    return $response;
};

// Add routes
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('<a href="/hello/world">Try /hello/world</a>');
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write('Hello, $name');
    return $response;
});

$app->post('/Callbacks/Messaging', function (Request $request, Response $response) {

    return $response->withStatus(200);
});

$app->post('/Callbacks/Voice/Inbound', function (Request $request, Response $response) {
    global $calls, $conference;
    $telnyxEvent = $request->getAttribute('telnyxEvent');
    $data = $telnyxEvent->data;
    if ($data['record_type'] != 'event') {
        return $response->withStatus(200);
    }
    $callId = $data->payload['call_control_id'];
    $event = $data['event_type'];
    switch ($event) {
        case 'call.initiated':
            $call = new Telnyx\Call($callId);
            $call->answer();
            $calls[$callId] = $call;
            break;
        case 'call.answered':
            // $call = $calls[$callId];
            $speakParams = array(
                'payload' => 'joining conference',
                'voice' => 'female',
                'language' => 'en-GB'
            );
            $call = new Telnyx\Call($callId);
            $call->speak($speakParams);
            break;
        default:
            # code...
            break;
    }
    return $response->withStatus(200);
})->add($telnyxWebhookVerify);

$app->run();