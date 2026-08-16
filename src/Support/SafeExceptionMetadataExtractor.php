<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class SafeExceptionMetadataExtractor
{
    /**
     * @return array{
     *     exception_class: class-string,
     *     message: string,
     *     route_name: ?string,
     *     path: ?string,
     *     method: ?string,
     *     status_code: ?int,
     *     file: ?string,
     *     line: ?int
     * }
     */
    public function extract(Throwable $throwable, Request $request): array
    {
        return [
            'exception_class' => $throwable::class,
            'message' => $this->sanitizeMessage($throwable->getMessage()),
            'route_name' => $request->route()?->getName(),
            'path' => $this->resolvePath($request),
            'method' => strtoupper($request->method()),
            'status_code' => $this->resolveStatusCode($throwable),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
        ];
    }

    protected function sanitizeMessage(string $message): string
    {
        $redacted = preg_replace(
            '/\b(password|token|secret|authorization|api_key|bearer|cookie|session)\s*[:=]\s*\S+/i',
            '$1=[redacted]',
            $message,
        );

        return Str::limit(trim($redacted ?? $message), 1000, '...');
    }

    protected function resolvePath(Request $request): string
    {
        $path = $request->getPathInfo();

        return $path !== '' ? $path : '/';
    }

    protected function resolveStatusCode(Throwable $throwable): ?int
    {
        if ($throwable instanceof HttpExceptionInterface) {
            return $throwable->getStatusCode();
        }

        if ($throwable->getCode() >= 400 && $throwable->getCode() < 600) {
            return (int) $throwable->getCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
