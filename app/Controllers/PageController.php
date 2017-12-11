<?php namespace Simple\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PageController
{
    public static function index (Request $request, Response $response)
    {
        $response->getBody()->write(file_get_contents(realpath(__DIR__ ) . '/../../dist/index.html'));

        return $response;
    }
}