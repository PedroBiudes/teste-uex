<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Repositories\Interfaces\UsuarioInterface;
use App\Helpers\Helpers;
use App\Models\Usuario;
use App\Models\Login;
use DateTime;
use App\Functions\Bcrypt;

use App\Functions\Log;

class UsuarioServico
{
    protected $interface;
    protected $helpers;

    public function __construct(UsuarioInterface $usuarioInterface, Helpers $helpers)
    {
        $this->interface = $usuarioInterface;
        $this->helpers = $helpers;

    }

    /**
     * Método responsavel por fazer o login efetivamente, realizando todas as tratativas
     */ public function LoginUser(Request $request)
    {
        try {
            $date = new DateTime();

            $email = $request->Email;
            $password = $request->Password ?? null;

            $user = $this->interface->GetUserByEmailLogin($email);
            if(empty($user)) {
                return response()->json("O email informado não existe no nosso banco de cadastros.", Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $this->validateLogin($request);

            if(!Bcrypt::check($password, $user->Password)) {
                return response()->json("Usuário ou senha nao correspondem.", Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            $newToken = $this->generateToken(64);

            $buscaTokenUser = $this->interface->FindLastLoginForUser($user->Id);
            if($buscaTokenUser == null){
                $token = new Login ([
                    "token" => $newToken,
                    "usuario_id" => $user->Id,
                    "expira_em" => $date->modify('+ 1 month')->format("Y-m-d H:i:s"),
                    "data_login" => $date->format("Y-m-d H:i:s")
                ]);
                
                $this->interface->SaveLogin($token);

                $result = [
                    "Token" => $token->token,
                    "ExpiraEm" => $token->expira_em,
                    "Usuario" => $user
                ];
            }
            else{
                $buscaTokenUser->expira_em = $date->modify('+ 1 month')->format("Y-m-d H:i:s");
                
                $this->interface->SaveLogin($buscaTokenUser);

                $result = [
                    "Token" => $buscaTokenUser->Token,
                    "ExpiraEm" => $buscaTokenUser->ExpiraEm,
                    "Usuario" => $user
                ];
            }
            return response()->json($result, Response::HTTP_OK);
        } catch(\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    protected function validateLogin(Request $request){
        $errors = [];
        if (!$request->has('Email')) {
            array_push($errors, 'Informe o email');
        }

        if (!$request->has('Password')) {
            array_push($errors, 'Informe a senha');
        }

        if (!empty($errors))
            return false;
        
        return true;
    }

    protected function generateToken(int $length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";        
        $max = strlen($codeAlphabet);   

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }
        return $token;
    }
}