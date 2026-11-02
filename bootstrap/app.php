<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
        |------------------------------------------------------------
        | El 404 de la API, contestado como lo contestaría una API
        |------------------------------------------------------------
        | Esto no lo pide la guía; entra porque al probar la matriz me
        | topé con el problema y dejarlo así habría sido entregar una
        | API que no cumple su propio contrato.
        |
        | Con APP_DEBUG=true, un abort(404) o un id que no existe
        | responden el código correcto pero con un cuerpo de cientos
        | de líneas: el nombre de la excepción, la ruta absoluta del
        | archivo en mi disco (C:\xampp\htdocs\...) y toda la pila de
        | llamadas, con las versiones de las librerías de por medio.
        |
        | Son dos problemas distintos y los dos importan:
        |
        |   El del contrato. La API prometió devolver JSON con una
        |   clave 'message'. Un cliente que lea respuesta.message en
        |   un 404 recibe una cadena vacía y un montón de datos que no
        |   sabe interpretar, así que no puede ni avisar qué pasó.
        |
        |   El de seguridad. Esa pila de llamadas es un mapa del
        |   servidor: estructura de carpetas, versiones exactas y
        |   nombres de clases internas. Es justo el dato con el que se
        |   empieza a buscar una vulnerabilidad conocida. Por eso la
        |   revisión de cierre de la guía pide APP_DEBUG=false fuera
        |   de la computadora propia, y por eso no me quiero apoyar
        |   solo en ese interruptor: si alguien levanta esto con
        |   debug encendido por error, acá ya no se filtra nada.
        |
        | La condición $request->is('api/*') limita el cambio a la
        | API. En la parte web el trace completo sigue saliendo,
        | porque ahí sí lo quiero mientras programo: lo lee una
        | persona en el navegador, no un programa.
        |
        | El mensaje es uno solo para los dos casos de 404, el id que
        | no existe y el recurso ajeno, y eso es a propósito: es la
        | misma decisión que tomé en el controlador al no usar 403.
        | Distinguirlos en el texto desharía el disimulo que el código
        | de estado logra.
        */
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'No encontramos ese recurso en tu cuenta.',
                ], 404);
            }
        });
    })->create();
