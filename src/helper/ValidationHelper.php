<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Psr7\Response as SlimResponse;

class ValidationHelper
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
       
        try {
           $data = $request->getParsedBody(); 
           
            $rules = [
                'name' => v::notEmpty()->alpha(),
                'email' => v::notEmpty()->email(),
            ];
           
            foreach ($rules as $field => $rule) {
                $rule->setName($field)->assert( $data[$field]?? '');
            }
           

            return $handler->handle($request);
        } catch (NestedValidationException $exception) {
            $errors = $exception->getMessages();
            $responseBody = json_encode(['errors' => $errors]);

            $response = new SlimResponse();
            $response->getBody()->write($responseBody);
            return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
        }
    }
}
