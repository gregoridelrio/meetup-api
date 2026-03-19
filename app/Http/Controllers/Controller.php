<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: "Meetup Football API", version: "1.0.0")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    description: "Enter JWT Bearer token",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Server(url: "http://localhost:8000", description: "Local development server")]
abstract class Controller
{
    //
}
