<?php
namespace App\Documentation;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "E-commerce API",
    version: "1.0.0",
    description: "API REST para usuarios, productos, órdenes y pagos."
)]
#[OA\Server(
    url: "http://127.0.0.1:8001",
    description: "Servidor local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Get(
    path: "/api/health",
    summary: "Verificar el estado de la API",
    tags: ["Sistema"],
    responses: [
        new OA\Response(
            response: 200,
            description: "API funcionando correctamente"
        )
    ]
)]
class OpenApi
{
}
