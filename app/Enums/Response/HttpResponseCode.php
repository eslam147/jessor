<?php
namespace App\Enums\Response;

enum HttpResponseCode: int
{
    const SUCCESS = 200;

    const CREATED = 201;

    const NO_CONTENT = 204;

    const BAD_REQUEST = 400;

    const UNAUTHORIZED = 401;

    const FORBIDDEN = 403;

    const NOT_FOUND = 404;

    const METHOD_NOT_ALLOWED = 405;

    const INTERNAL_SERVER_ERROR = 500;

    const SERVICE_UNAVAILABLE = 503;

    const UNPROCESSABLE_ENTITY = 422;

    const TOO_MANY_REQUESTS = 429;
    const CONFLICT = 409;
}