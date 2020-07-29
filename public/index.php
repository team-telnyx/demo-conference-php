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
$CONFERENCE_FILE_NAME = '../conference_id.txt';

Telnyx\Telnyx::setApiKey($TELNYX_API_KEY);
Telnyx\Telnyx::setPublicKey($TELNYX_PUBLIC_KEY);
// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

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

function readConferenceFile (String $CONFERENCE_FILE_NAME) {
    if (!file_exists($CONFERENCE_FILE_NAME)) {
        return FALSE;
    }
    else {
        $conferenceFile = fopen($CONFERENCE_FILE_NAME, 'r') or die("Unable to open file!");
        $fileConferenceId = fread($conferenceFile, filesize($CONFERENCE_FILE_NAME));
        return $fileConferenceId;
    }
}

function createConferenceFile (String $conferenceId, String $CONFERENCE_FILE_NAME) {
    $conferenceFile = fopen($CONFERENCE_FILE_NAME, 'w') or die ('Unable to open conference file');
    fwrite($conferenceFile, $conferenceId);
    fclose($conferenceFile);
    return $conferenceId;
};

function deleteConferenceFile (String $CONFERENCE_FILE_NAME){
    if (!file_exists($CONFERENCE_FILE_NAME)) {
        return;
    }
    if (!unlink($CONFERENCE_FILE_NAME)) {
        die ('Can not delete conference file');
    }
    return;
};

function addCallToConference (String $callControlId, String $conferenceId) {
    $conference = new Telnyx\Conference($conferenceId);
    $joinConferenceParameters = array(
        'call_control_id' => $callControlId
    );
    $conference->join($joinConferenceParameters);
};

function createConference (String $callControlId, String $CONFERENCE_FILE_NAME) {
    $conferenceName = uniqid('conf-');
    $conferenceParameters = array(
        'call_control_id' => $callControlId,
        'name' => $conferenceName,
        'beep_enabled' => 'always'
    );
    $newConference = Telnyx\Conference::create($conferenceParameters);
    $conferenceId = $newConference->id;
    createConferenceFile($conferenceId, $CONFERENCE_FILE_NAME);
    return $conferenceId;
}

function handleAnswer (String $callControlId, String $CONFERENCE_FILE_NAME) {
    $speakParams = array(
        'payload' => 'joining conference',
        'voice' => 'female',
        'language' => 'en-GB'
    );
    $call = new Telnyx\Call($callControlId);
    $call->speak($speakParams);
    $existingConferenceId = readConferenceFile($CONFERENCE_FILE_NAME);
    if (!$existingConferenceId) {
        createConference($callControlId, $CONFERENCE_FILE_NAME);
    }
    else {
        addCallToConference($callControlId, $existingConferenceId);
    }
    return;
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
    global $CONFERENCE_FILE_NAME;
    $telnyxEvent = $request->getAttribute('telnyxEvent');
    $data = $telnyxEvent->data;
    if ($data['record_type'] != 'event') {
        return $response->withStatus(200);
    }
    $callControlId = $data->payload['call_control_id'];
    $event = $data['event_type'];
    switch ($event) {
        case 'call.initiated':
            $call = new Telnyx\Call($callControlId);
            $call->answer();
            break;
        case 'call.answered':
            handleAnswer($callControlId, $CONFERENCE_FILE_NAME);
            break;
        case 'conference.ended':
            deleteConferenceFile($CONFERENCE_FILE_NAME);
        default:
            # other events less importante right now
            break;
    }
    return $response->withStatus(200);
})->add($telnyxWebhookVerify);

$app->run();