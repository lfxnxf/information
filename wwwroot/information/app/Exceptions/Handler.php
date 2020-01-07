<?php

namespace App\Exceptions;

use App\Services\IntentionService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Utils\Code;
use App\Utils\Result;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if ($exception instanceof CustomerException
            || $exception instanceof IntentionService
            || $exception instanceof TalkException) {

            Log::info(get_class($exception) . '::' . $exception->getMessage());
            return;
        }
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function render($request, Exception $exception)
    {
        if ($request->is("api/*")) {
            //如果错误是 ValidationException的一个实例，说明是一个验证的错误

            switch (true) {
                case $exception instanceof ValidationException:

                    return Result::getRes(Code::VALIDATE_ERROR_CODE, [], array_values($exception->errors())[0][0]);

                case $exception instanceof QueryException:

                    return Result::getRes(Code::DB_FAILED, [], $exception->getMessage());

                case $exception instanceof NotFoundHttpException:

                    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    return Result::getRes(Code::URL_FAILED, [], $url);

                case $exception instanceof MethodNotAllowedHttpException:

                    $method = $request->method();
                    return Result::getRes(!$exception->getCode() ? Code::METHOD_NOT_ALLOWED : $exception->getCode(), [], $method);

                case $exception instanceof \Exception:
                    return Result::getRes(!$exception->getCode() ? Code::SYSTEM_ERROR_CODE : $exception->getCode(), [], $exception->getMessage());

            }
        }
        return parent::render($request, $exception);
    }

}
