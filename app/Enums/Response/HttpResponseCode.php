<?php
namespace App\Enums\Response;

enum HttpResponseCode: int
{

    case SUCCESS = 200;

    case CREATED = 201;

    case NO_CONTENT = 204;

    case BAD_REQUEST = 400;

    case UNAUTHORIZED = 401;

    case FORBIDDEN = 403;

    case NOT_FOUND = 404;

    case METHOD_NOT_ALLOWED = 405;

    case INTERNAL_SERVER_ERROR = 500;

    case SERVICE_UNAVAILABLE = 503;

    case UNPROCESSABLE_ENTITY = 422;

    case TOO_MANY_REQUESTS = 429;
    case CONFLICT = 409;
}