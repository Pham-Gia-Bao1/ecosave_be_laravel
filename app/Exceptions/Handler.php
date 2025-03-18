<?php
namespace App\Exceptions;

use App\Helpers\ApiErrorHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
{
    // Danh sách các lỗi HTTP và hàm xử lý tương ứng
    $errorHandlers = [
        \Illuminate\Auth\AuthenticationException::class => fn() => ApiErrorHandler::unauthorized("Bạn chưa đăng nhập."),
        \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class => fn() => ApiErrorHandler::forbidden("Bạn không có quyền truy cập tài nguyên này."),
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => fn() => ApiErrorHandler::notFound("Tài nguyên không tồn tại."),
        \Symfony\Component\HttpKernel\Exception\BadRequestHttpException::class => fn() => ApiErrorHandler::badRequest("Yêu cầu không hợp lệ."),
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class => fn() => ApiErrorHandler::badRequest("Phương thức không được hỗ trợ cho request này."),
        \Symfony\Component\HttpKernel\Exception\ConflictHttpException::class => fn() => ApiErrorHandler::badRequest("Dữ liệu có xung đột, vui lòng kiểm tra lại."),
        \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException::class => fn() => ApiErrorHandler::badRequest("Bạn đã gửi quá nhiều yêu cầu, vui lòng thử lại sau."),
        \Illuminate\Session\TokenMismatchException::class => fn() => ApiErrorHandler::unauthorized("Phiên làm việc đã hết hạn, vui lòng tải lại trang."),
        \Tymon\JWTAuth\Exceptions\TokenInvalidException::class => fn() => ApiErrorHandler::unauthorized("Token không hợp lệ."),
        \Tymon\JWTAuth\Exceptions\TokenExpiredException::class => fn() => ApiErrorHandler::unauthorized("Token đã hết hạn."),
        \Symfony\Component\HttpKernel\Exception\HttpException::class => fn() => ApiErrorHandler::serverError("Lỗi máy chủ nội bộ."),
    ];

    // Lặp qua danh sách lỗi để kiểm tra loại exception
    foreach ($errorHandlers as $exceptionClass => $handler) {
        if ($exception instanceof $exceptionClass) {
            return $handler();
        }
    }

    // Nếu đang ở môi trường debug, hiển thị chi tiết lỗi
    if (config('app.debug')) {
        return response()->json([
            'status' => 'error',
            'status_code' => 500,
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ], 500);
    }

    // Xử lý tất cả các lỗi khác chưa được định nghĩa
    return ApiErrorHandler::serverError("Đã xảy ra lỗi không xác định.") ?: parent::render($request, $exception);
}


}
