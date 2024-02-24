<?php

namespace App\Http\Controllers\Api;

use App\Helpers\HashHelper;
use App\Helpers\UserAuthHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UsersSessions;
use Exception;

class UserController extends Controller
{
    public function auth()
    {
        // принимаем входные параметры
         $request = request()->all();

        if (empty($request['data'])) {
            return response('Ошибка авторизации в приложении 1', 401);
        }

        // декриптим входные данные в массив. Если что-то сломалось возвращаем ошибку
        try {
            $decryptRequest = HashHelper::decryptHash($request['data']);
        } catch (Exception $e) {
            return response('Ошибка авторизации в приложении 2', 401);
        }

        // var_dump($decryptRequest);

        // проверяем подпись
        if (UserAuthHelper::signatureVerification($decryptRequest)) {
            // Ищем юзера по id и обновляем, если такой есть. Если такого пользлвателя нет то создаем его.
            $userId = (int)$decryptRequest['id'];
            $token = addslashes($decryptRequest['access_token']);
            $user = User::find(['id', $userId])->toArray();

            if (!empty($user)) {
                User::updateUser($userId, $decryptRequest);
                UsersSessions::replace($userId, $token);
            } else {
                User::addUser($decryptRequest);
                UsersSessions::add($userId, $token);
                $user = User::find(['id', $userId])->toArray();
            }

            return response()->json([
                'access_tocken' => $token,
                'user_info' => $user,
                'error' => '',
                'error_key' => ''
            ], 200);
        }

        return response('Ошибка авторизации в приложении 3', 401);
    }

    /**
     * Имитация входящих данных с фронта.
     * Метод вернет закодированную строку. Её передаём в GET параметр. Это позволит более безопасно передавать данные методом GET.
     */
    public function simulatingPreparationRequestFromFrontSide()
    {
        $request = request()->all();

        return HashHelper::prepareHash($request);
    }

    public function getSig()
    {
        $request = request()->all();
        ksort($request);
        $string = '';

        foreach ($request as $key => $value) {
            $string .= $key . '=' . $value;
        }

        $string .= env("SEKRET_KEY");
        return mb_strtolower(md5($string), 'UTF-8');
    }

}
