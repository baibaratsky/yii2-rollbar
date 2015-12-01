<?php

namespace baibaratsky\yii\rollbar;

use Rollbar;
use Yii;

trait ErrorHandlerTrait
{
    public $rollbarComponentName = 'rollbar';

    public function handleException($exception)
    {
        $ignoreException = false;
        foreach (Yii::$app->get($this->rollbarComponentName)->ignoreExceptions as $ignoreRecord) {
            if ($exception instanceof $ignoreRecord[0]) {
                $ignoreException = true;
                foreach (array_slice($ignoreRecord, 1) as $property => $range) {
                    if (!in_array($exception->$property, $range)) {
                        $ignoreException = false;
                        break;
                    }
                }
                if ($ignoreException) {
                    break;
                }
            }
        }

        if (!$ignoreException) {
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