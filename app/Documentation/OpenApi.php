<?php

namespace App\Documentation;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "E-commerce API",
    version: "1.0.0",
    description: "API REST para gestión de usuarios, productos, órdenes y pagos mediante Stripe. Incluye autenticación JWT."
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

// SISTEMA
#[OA\Get(
    path: "/api/health",
    summary: "Verificar el estado de la API",
    tags: ["Sistema"],
    responses: [
        new OA\Response(response: 200, description: "API funcionando correctamente")
    ]
)]

// AUTENTICACIÓN
#[OA\Post(
    path: "/api/register",
    summary: "Registrar un usuario",
    tags: ["Autenticación"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Usuario Prueba"),
                new OA\Property(property: "email", type: "string", format: "email", example: "prueba@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "Password123"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "Password123")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "Usuario registrado correctamente"),
        new OA\Response(response: 422, description: "Datos de validación incorrectos")
    ]
)]
#[OA\Post(
    path: "/api/login",
    summary: "Iniciar sesión y obtener token JWT",
    tags: ["Autenticación"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "prueba@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "Password123")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Inicio de sesión correcto. Devuelve el token JWT"),
        new OA\Response(response: 401, description: "Credenciales incorrectas"),
        new OA\Response(response: 422, description: "Datos de validación incorrectos")
    ]
)]
#[OA\Get(
    path: "/api/me",
    summary: "Obtener el usuario autenticado",
    tags: ["Autenticación"],
    security: [["bearerAuth" => []]],
    responses: [
        new OA\Response(response: 200, description: "Información del usuario autenticado"),
        new OA\Response(response: 401, description: "No autenticado o token inválido")
    ]
)]
#[OA\Post(
    path: "/api/logout",
    summary: "Cerrar sesión e invalidar el token",
    tags: ["Autenticación"],
    security: [["bearerAuth" => []]],
    responses: [
        new OA\Response(response: 200, description: "Sesión cerrada correctamente"),
        new OA\Response(response: 401, description: "No autenticado o token inválido")
    ]
)]

// PRODUCTOS
#[OA\Get(
    path: "/api/products",
    summary: "Listar productos activos",
    tags: ["Productos"],
    responses: [
        new OA\Response(response: 200, description: "Listado de productos")
    ]
)]
#[OA\Get(
    path: "/api/products/{product}",
    summary: "Consultar un producto",
    tags: ["Productos"],
    parameters: [
        new OA\Parameter(
            name: "product",
            in: "path",
            required: true,
            description: "ID del producto",
            schema: new OA\Schema(type: "integer", example: 1)
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Producto encontrado"),
        new OA\Response(response: 404, description: "Producto no encontrado")
    ]
)]
#[OA\Post(
    path: "/api/products",
    summary: "Crear un producto",
    tags: ["Productos"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "price"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Teclado Mecánico"),
                new OA\Property(property: "description", type: "string", nullable: true, example: "Teclado RGB"),
                new OA\Property(property: "price", type: "number", format: "float", example: 39.99),
                new OA\Property(property: "stock", type: "integer", example: 20),
                new OA\Property(property: "image", type: "string", nullable: true, example: "teclado.jpg"),
                new OA\Property(property: "is_active", type: "boolean", example: true)
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "Producto creado correctamente"),
        new OA\Response(response: 401, description: "No autenticado"),
        new OA\Response(response: 422, description: "Datos de validación incorrectos")
    ]
)]
#[OA\Put(
    path: "/api/products/{product}",
    summary: "Actualizar un producto",
    tags: ["Productos"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(
            name: "product",
            in: "path",
            required: true,
            description: "ID del producto",
            schema: new OA\Schema(type: "integer", example: 1)
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string", example: "Teclado Mecánico actualizado"),
                new OA\Property(property: "description", type: "string", nullable: true),
                new OA\Property(property: "price", type: "number", format: "float", example: 44.99),
                new OA\Property(property: "stock", type: "integer", example: 15),
                new OA\Property(property: "image", type: "string", nullable: true),
                new OA\Property(property: "is_active", type: "boolean", example: true)
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Producto actualizado correctamente"),
        new OA\Response(response: 401, description: "No autenticado"),
        new OA\Response(response: 404, description: "Producto no encontrado"),
        new OA\Response(response: 422, description: "Datos de validación incorrectos")
    ]
)]
#[OA\Delete(
    path: "/api/products/{product}",
    summary: "Eliminar un producto",
    tags: ["Productos"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(
            name: "product",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer", example: 1)
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Producto eliminado correctamente"),
        new OA\Response(response: 401, description: "No autenticado"),
        new OA\Response(response: 404, description: "Producto no encontrado")
    ]
)]

// ÓRDENES
#[OA\Get(
    path: "/api/orders",
    summary: "Listar las órdenes del usuario autenticado",
    tags: ["Órdenes"],
    security: [["bearerAuth" => []]],
    responses: [
        new OA\Response(response: 200, description: "Listado de órdenes del usuario"),
        new OA\Response(response: 401, description: "No autenticado")
    ]
)]
#[OA\Post(
    path: "/api/orders",
    summary: "Crear una orden",
    tags: ["Órdenes"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["items"],
            properties: [
                new OA\Property(
                    property: "items",
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        required: ["product_id", "quantity"],
                        properties: [
                            new OA\Property(property: "product_id", type: "integer", example: 1),
                            new OA\Property(property: "quantity", type: "integer", example: 2)
                        ]
                    )
                )
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "Orden creada correctamente"),
        new OA\Response(response: 401, description: "No autenticado"),
        new OA\Response(response: 422, description: "Producto inválido, inactivo o stock insuficiente")
    ]
)]
#[OA\Get(
    path: "/api/orders/{id}",
    summary: "Consultar una orden del usuario autenticado",
    tags: ["Órdenes"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer", example: 1)
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Información de la orden"),
        new OA\Response(response: 401, description: "No autenticado"),
        new OA\Response(response: 404, description: "Orden no encontrada")
    ]
)]

// PAGOS
#[OA\Post(
    path: "/api/payments/create-intent",
    summary: "Crear un intento de pago con Stripe",
    tags: ["Pagos"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["order_id"],
            properties: [
                new OA\Property(property: "order_id", type: "integer", example: 1)
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "Intento de pago creado. Devuelve payment_intent_id y client_secret"),
        new OA\Response(response: 401, description: "No autenticado"),
        new OA\Response(response: 404, description: "Orden no encontrada"),
        new OA\Response(response: 422, description: "La orden no está pendiente de pago o los datos son inválidos")
    ]
)]
#[OA\Post(
    path: "/api/stripe/webhook",
    summary: "Recibir notificaciones de Stripe",
    description: "Endpoint público utilizado por Stripe para notificar eventos de pago. Valida la firma mediante el encabezado Stripe-Signature.",
    tags: ["Pagos"],
    parameters: [
        new OA\Parameter(
            name: "Stripe-Signature",
            in: "header",
            required: true,
            description: "Firma enviada por Stripe para validar el webhook",
            schema: new OA\Schema(type: "string")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            description: "Evento enviado por Stripe"
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Webhook procesado correctamente"),
        new OA\Response(response: 400, description: "Firma del webhook inválida")
    ]
)]
class OpenApi
{
}
