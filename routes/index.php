<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function (){
    return response()->json('Unauthorized.', 401);
});

$router->group(['prefix' => env('API_VERSION', 'api')], function ($router){
        // Usuario
        $router->group(['prefix' => 'usuario'], function ($router) {
            $router->post('login', 'UsuarioController@LoginUser');
        });
});
