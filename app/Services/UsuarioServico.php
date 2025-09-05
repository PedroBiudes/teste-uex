<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Repositories\Interfaces\UsuarioInterface;
use App\Helpers\Helpers;
use App\Models\Usuario;
use App\Models\Cadastros;
use App\Models\Login;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;
use DateTime;
use GuzzleHttp\Client;
use App\Services\NominatinServico;


class UsuarioServico
{
    protected $interface;
    protected $helpers;
    protected $nominatimService;

    public function __construct(UsuarioInterface $usuarioInterface, Helpers $helpers, NominatinServico $nominatimService)
    {
        $this->interface = $usuarioInterface;
        $this->helpers = $helpers;
        $this->nominatimService = $nominatimService;
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
            if (empty($user)) {
                return response()->json("O email informado não existe no nosso banco de cadastros.", Response::HTTP_OK);
            }
            $this->validateLogin($request);

            if ($password !== $user->password) {
                return response()->json("Usuario ou senha nao correspondem.", Response::HTTP_OK);
            }

            $newToken = $this->generateToken(64);

            $buscaTokenUser = $this->interface->FindLastLoginForUser($user->Id);
            if ($buscaTokenUser == null) {
                $token = new Login([
                    "token" => $newToken,
                    "usuario_id" => $user->id,
                    "expira_em" => $date->modify('+ 1 month')->format("Y-m-d H:i:s"),
                    "data_login" => $date->format("Y-m-d H:i:s")
                ]);

                $this->interface->SaveLogin($token);

                $result = [
                    "Token" => $token->token,
                    "ExpiraEm" => $token->expira_em,
                    "Usuario" => $user
                ];
            } else {
                $buscaTokenUser->expira_em = $date->modify('+ 1 month')->format("Y-m-d H:i:s");

                $this->interface->SaveLogin($buscaTokenUser);

                $result = [
                    "Token" => $buscaTokenUser->Token,
                    "ExpiraEm" => $buscaTokenUser->ExpiraEm,
                    "Usuario" => $user
                ];
            }
            return response()->json($result, Response::HTTP_OK);
        } catch (\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function ChangePassword(Request $request)
    {
        try {
            $email = $request->Email;

            $user = $this->interface->GetUserByEmailLogin($email);
            if (empty($user)) {
                return response()->json("O email informado não existe no nosso banco de cadastros.", Response::HTTP_OK);
            }
            
            $newPassword = $this->generateNewPassword(12);
            $user->password = $newPassword;

            $userNewPassword = $this->interface->UpdatePassword($user->id, $newPassword);
            if(!$userNewPassword){
                return response()->json("Não foi possível alterar a senha, tente novamente mais tarde.", Response::HTTP_OK);
            }
            Mail::to($user->email)->send(new GenericMail("Recuperação de senha", ['NewPassword' => $newPassword], 'email.newpassword'));
            
            $result = [
                "Usuario" => $user
            ];
            return response()->json($result, Response::HTTP_OK);
        } catch (\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function CreateUser(Request $request)
    {
        try {
            $email = $request->Email;

            $user = $this->interface->GetUserByEmailLogin($email);
            if (empty($user)) {
                $newUser = new Usuario([
                    "nome" => $request->Nome,
                    "email" => $request->Email,
                    "password" => $request->Password,
                    "telefone" => $request->Telefone,
                    "documento" => $request->Documento
                ]);
                $this->interface->CreateUser($newUser); 
                return response()->json("Usuário cadastrado com sucesso.", Response::HTTP_OK);
            }
            else{
                return response()->json("O email informado já existe no nosso banco de cadastros.", Response::HTTP_OK);
            }
        } catch (\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function DeleteUser($id)
    {
        try {
            $this->interface->ApagarUsuario($id);
            return response()->json("Usuário apagado com sucesso.", Response::HTTP_OK);
        } catch (\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function DeleteContact($id)
    {
        try {
            $this->interface->ApagarContato($id);
            return response()->json("Contato apagado com sucesso.", Response::HTTP_OK);
        } catch (\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function SaveContact(Request $request)
    {
        try {
            $enderecoCompleto = "{$request->logradouro}, {$request->numero}, {$request->cidade}, {$request->uf}";

            $coordenadas = $this->nominatimService->getLatLong($enderecoCompleto);

            $newContact = $this->interface->BuscaContato($request->cpf) != null ? $this->interface->BuscaContato($request->cpf) : new Cadastros();
            foreach($request->all() as $key => $value){
                if($key != "IdUsuario"){
                    $newContact->$key = $value;
                }
            }
            $newContact->latitude = $coordenadas['latitude'] ?? null;
            $newContact->longitude = $coordenadas['longitude'] ?? null;
            $this->interface->SaveContact($newContact); 
            return response()->json("Contato salvo com sucesso.", Response::HTTP_OK);
        } catch (\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function GetContacts(Request $request)
    {
        try {
            $listaContatos = $this->interface->BuscarContatos($request->IdUsuario, $request->searchTerm);
            if (empty($listaContatos)) {
                return response()->json("Nenhum contato encontrado para o usuário informado.", Response::HTTP_OK);
            }
            return response()->json($listaContatos, Response::HTTP_OK);
        } catch (\Exception $ex) {
            $exception = [
                'Message' => $ex->getMessage(),
                'Code' => $ex->getCode(),
                'Trace' => $ex->getTraceAsString(),
                'Exception' => $ex->__toString()
            ];

            return response()->json($exception['Message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function validateLogin(Request $request)
    {
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

    protected function generateToken(int $length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        return $token;
    }
    
    protected function generateNewPassword(int $length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        return $token;
    }
}
