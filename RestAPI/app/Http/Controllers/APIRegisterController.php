<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Prompts\Table;
use PhpOption\None;
use PSpell\Dictionary;
use function Symfony\Component\Translation\t;

/**
 * @OA\Info(
 *     title="Auth API",
 *     version="1.0.0",
 *     description="API для регистрации/редактирования/удаления/просмотра пользователей."
 * )
 */
class APIRegisterController extends Controller
{
    function checkAuthAndBlock($user_id):array
    {
        $user = User::find($user_id);
        if (!$user) {
            return ['access'=>false, 'message'=>'Пользователь не авторизован','user'=>null];
        }
        else if ($user->is_blocked) {
            return ['access'=>false, 'message'=>'Пользователь заблокирован','user'=>null];
        }
        else {
            return ['access'=>true, 'message'=>'','user'=>$user];
        }
    }


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Регистрация нового пользователя",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="UserName пользователя (не Name)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email пользователя",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Имя пользователя",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="Пользователь зарегистрирован"),
     *     @OA\Response(response="422", description="Неправильные UserName или Email")
     * )
     */
    public function register(Request $request){
        try{
            $validated = $request->validate([
                'username' => 'required|unique:users,username|regex:/^[A-Za-z]+$/',
                'email' => 'required|unique:users,email|email',
                'name' => 'nullable|string',
            ]);

            $user = User::create($validated);

            return response()->json([
                'message' => 'Регистрация успешна',
            ], 201);
        }
        catch (\Exception $exception){
            return response()->json([
                'message' => "Проверьте правильность введенных данных",
            ], 422);
        }

    }


    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Просмотр информации пользователя",
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         description="id пользователя",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="Данные получены"),
     *     @OA\Response(response="401", description="Вы не авторизованы или заблокированы")
     * )
     */
    public function getUserData(Request $request)
    {
        $user_id = $request->header('user-id');

        if (!$user_id){
            return response()->json([
                'message'=>'Вы не авторизованы'
            ], 401);
        }
        else {
            $access_arr = $this->checkAuthAndBlock($user_id);
            if (!$access_arr['access']) {
                return response()->json([
                    'message'=>$access_arr['message']
                ], 401);
            }
            $user = $access_arr['user'];
            return response()->json([
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'username'=>$user->username,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/update",
     *     summary="Редактирование информации пользователя",
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         description="id пользователя",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="username",
     *          in="query",
     *          description="UserName пользователя (не Name)",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Имя пользователя",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="201", description="Данные обновлены"),
     *     @OA\Response(response="401", description="Вы не авторизованы или заблокированы"),
     *     @OA\Response(response="422", description="Неправильные новые данные")
     * )
     */
    public function updateUserData(Request $request){
        $user_id = $request->header('user-id');

        if (!$user_id){
            return response()->json([
                'message'=>'Вы не авторизованы'
            ], 401);
        }
        else {
            $access_arr = $this->checkAuthAndBlock($user_id);
            if (!$access_arr['access']) {
                return response()->json([
                    'message'=>$access_arr['message']
                ], 401);
            }

            $user = $access_arr['user'];
            try {

                $validated = $request->validate([
                    'username' => 'required|string|regex:/^[A-Za-z]+$/|unique:users,username',
                    'name' => 'required|string',
                ]);

                $user->update($validated);
                return response()->json([
                    'message' => 'Данные обновлены',
                ],200);
            }
            catch (\Exception $exception){
                return response()->json([
                    'message'=>'Проверьте правильность введенных данных'
                ], 422);
            }
        }
    }


    /**
     * @OA\Post(
     *     path="/api/delete",
     *     summary="Удаление пользователя",
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         description="id пользователя",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="Пользователь удален"),
     *     @OA\Response(response="401", description="Вы не авторизованы или заблокированы")
     * )
     */
    public function deleteUser(Request $request){
        $user_id = $request->header('user-id');

        if (!$user_id){
            return response()->json([
                'message'=>'Вы не авторизованы'
            ], 401);
        }
        else {
            $access_arr = $this->checkAuthAndBlock($user_id);
            if (!$access_arr['access']) {
                return response()->json([
                    'message'=>$access_arr['message']
                ], 401);
            }

            $access_arr['user']->delete();
            return response()->json([
                'message' => 'Пользователь удален',
            ],200);
        }
    }
}
