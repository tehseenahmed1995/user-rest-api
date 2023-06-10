<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

$dataValidator = function(Request $request, RequestHandler $handler) {

    $jsonSchema = <<<'JSON'
    {
        "type": "object",
        "properties": {
            "name": {"type": "string"},
            "email": {"type": "string"},
        },
        "required": ["name", "email"]
    }
    JSON;

    $jsonSchemaObject = json_decode($jsonSchema);
    
    $validator = new Validator();
    $data = $request->getParsedBody();

    $dataObject = json_decode(json_encode($data));

    $validator->validate($dataObject, $jsonSchemaObject, Constraint::CHECK_MODE_APPLY_DEFAULTS);

    if($validator->isValid()) {
        var_dump($dataObject);exit;
        $response = $handler->handle($request);
        return $response;
    }
    else {
        $response = new Response();
        $response->getBody()->write(json_encode($validator->getErrors()));
        return $response->withHeader('content-type', 'application/json');
    }
    
};