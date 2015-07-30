<?php

namespace baibaratsky\yii\rollbar;

use Rollbar;
use yii\web\HttpException;

trait ErrorHandlerTrait
{
    public function handleException($exception)
    {
        if (!($exception instanceof HttpException && $exception->statusCode === 404)) {
            Rollbar::report_exception($exception);
        }

        parent::handleException($exception);
    }

    public function handleError($code, $message, $file, $line)
    {
        Rollbar::report_php_error($code, $message, $file, $line);

        parent::handleError($code, $message, $file, $line);
    }

    public function handleFatalError()
    {
        Rollbar::report_fatal_error();

        parent::handleFatalError();
    }
}