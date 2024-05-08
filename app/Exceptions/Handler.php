<?php

namespace App\Exceptions;

use App\Http\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * 不应报告的异常类型列表
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class, // 认证相关的异常
        AuthorizationException::class, // 授权相关的异常
        HttpException::class, // HTTP 相关的异常
        HttpResponseException::class, // HTTP 响应相关的异常
        TokenMismatchException::class, // Token 不匹配的异常
        ModelNotFoundException::class,
        MiddleException::class,
        ValidationException::class,
        QueryException::class,
        MethodNotAllowedHttpException::class
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // ModelNotFound
        if ($e instanceof ModelNotFoundException) {
            ApiResponse::missing(404, '请求的资源未找到');
        }
        // 登录异常
        if ($e instanceof AuthenticationException ) {
            ApiResponse::missing(401, '登录状态错误');
        }
        // 表单验证失败
        if ($e instanceof ValidationException) {
            ApiResponse::missing(422, $e->getMessage(), $e->errors());
        }
        // query异常
        if ($e instanceof QueryException) {
            ApiResponse::error(500, $e->getMessage(), $e->getTrace());
        }
        // 自定义异常
        if ($e instanceof MyMissingApiException) {
            ApiResponse::missing($e->getCode(), $e->getMessage(), $e->getDetails());
        }

        // POST等请求不存在接口时
        if ($e instanceof MethodNotAllowedHttpException) {
            ApiResponse::missing(405, $e->getMessage());
        }

        if ($e instanceof ThrottleRequestsException) {
            ApiResponse::missing(429, '操作过于频繁，请稍后重试');
        }
    
        // 未知异常(控制器中Exception应自主捕获)
        \Log::channel('exception')->emergency(get_class($e) . "\n" . $e->getTraceAsString());

        $http_error_code = intval($e->getCode());
        ApiResponse::exceptionReturn($http_error_code ?: 500, $e->getMessage(), $e->getTrace());

        return parent::render($request, $e);
    }

}
