<?php

namespace App\Swagger;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Snapic Documentation",
 *         version="1.0.0",
 *         description="Swagger OpenApi description"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             in="header",
 *             name="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT"
 *         )
 *     )
 * )
 */
class SwaggerAnnotations
{
    // Este arquivo pode permanecer vazio, o importante são as anotações acima.
}
