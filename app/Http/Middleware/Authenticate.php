<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Models\Token;
use App\Functions\Log;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(!$request->header('Authorization')) 
            return response()->json(['error' => 'Unauthorized'], 401);

        if($request->header('Authorization') == env('TOKEN')) 
            return $next(env('TOKEN'));

        $token = $request->header('Authorization');
        $validateToken = Token::where('Token', '=', $token)->with('usuario')->with('usuario.administrativo')->with('usuario.aluno')->first();
        
        if($validateToken == null)
            return response()->json(['error' => 'Unauthorized'], 401);
        
        /*
         * Comentado por conta de problema de performance na gravação do Log em storage

        $uri = $request->path();
        $uri = str_replace("api/v1/", "", $uri);

        if($validateToken->usuario->administrativo != null){
            $requisicao = json_decode(file_get_contents('php://input'), true);
            Log::Log($requisicao, 'Trace', 'Auditoria/UsuariosAdministrativos/'.$uri.'/'.'user:'.$validateToken->usuario->administrativo->Nome);
        }
        else if($validateToken->usuario->aluno != null){
            $requisicao = json_decode(file_get_contents('php://input'), true);
            Log::Log($requisicao, 'Trace', 'Auditoria/UsuariosAlunos/'.$uri.'/'.'user:'.$validateToken->usuario->aluno->Nome);
        }
        */

        return $next($validateToken);
    }
}
