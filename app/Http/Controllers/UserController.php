<?php

namespace App\Http\Controllers;

use App\Exceptions\DenyException;
use App\Exceptions\MissingException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends MyApiController
{

    /**
     * 登录
     *
     * @param Request $request
     * @throws \Exception
     */
    public function login(Request $request)
    {
        begin();

        $user = $this->loginByAccount($request);

        $expired_date = date('Y-m-d H:i:s', time() + TOEKN_AVLIABLE_TIME);
        $expired_at = new \DateTime($expired_date);
        $token = $user->createToken(time(), ['*'], $expired_at);
        $user->token = $token->plainTextToken;

        commit();

        $this->success($user);
    }

    /**
     * 通过账号密码登录
     *
     * @param Request $request
     * @return User
     * @throws MissingException|DenyException
     */
    private function loginByAccount(Request $request): User
    {
        $request->validate([
            'account' => 'required',
            'password' => 'required',
        ]);

        $user = User::query()
            ->where('account', $request->post('account'))
            ->first();
        if (!$user) {
            throw new MissingException('您的账号尚未认证，请先注册', ['type' => '用户名未找到']);
        }

        // check password
        if (!Hash::check($request->password, $user->password)) {
            throw new DenyException('账号或密码错误', ['type' => '密码错误']);
        }

        return $user;
    }

    /**
     * 退出
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        begin();

        /** @var \App\Models\User */
        $user = $request->user();

        // unset tokens
        $user->tokens()->delete();

        commit();

        $this->success();
    }

}
