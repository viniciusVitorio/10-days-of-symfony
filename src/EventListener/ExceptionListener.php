<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse();

        $message = $exception->getMessage();
        $statusCode = $exception instanceof HttpExceptionInterface 
            ? $exception->getStatusCode() 
            : 500;

        if ($statusCode === 500 && $_ENV['APP_ENV'] === 'prod') {
            $message = 'Um erro interno ocorreu no servidor.';
        }

        $response->setData([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
        ]);
        
        $response->setStatusCode($statusCode);

        $event->setResponse($response);
    }
}