<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\RecordNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->stopIgnoring(HttpException::class);
        $exceptions->stopIgnoring(HttpResponseException::class);
        $exceptions->stopIgnoring(ModelNotFoundException::class);
        $exceptions->stopIgnoring(MultipleRecordsFoundException::class);
        $exceptions->stopIgnoring(RecordNotFoundException::class);
        $exceptions->stopIgnoring(RecordsNotFoundException::class);
        $exceptions->stopIgnoring(RequestExceptionInterface::class);
    })->create();
