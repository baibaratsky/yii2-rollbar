<?php

namespace baibaratsky\yii\rollbar;

use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

trait ErrorHandlerTrait
{
    public $rollbarComponentName = 'rollbar';

    /**
     * @var callable Callback returning a payload data associative array or null
     * Example:
     * function (ErrorHandler $errorHandler) {
     *     return [
     *         'foo' => 'bar',
     *         'xyz' => getSomeData(),
     *     ];
     * }
     */
    public $payloadDataCallback;

    public function logException($exception)
    {
        $this->logExceptionRollbar($exception);

        parent::logException($exception);
    }

    private function getPayloadData($exception)
    {
        $payloadData = $this->payloadCallback();

        if ($exception instanceof WithPayload) {
            $exceptionData = $exception->rollbarPayload();
            if (is_array($exceptionData)) {
                if (is_null($payloadData)) {
                    $payloadData = $exceptionData;
                } else {
                    $payloadData = ArrayHelper::merge($exceptionData, $payloadData);
                }
            } elseif (!is_null($exceptionData)) {
                throw new \Exception(get_class($exception) . '::rollbarPayload() returns an incorrect result');
            }
        }

        return $payloadData;
    }

    private function payloadCallback()
    {
        if (!isset($this->payloadDataCallback)) {
            return null;
        }

        if (!is_callable($this->payloadDataCallback)) {
            throw new \Exception('Incorrect callback provided');
        }

        $payloadData = call_user_func($this->payloadDataCallback, $this);

        if (!is_array($payloadData) && !is_null($payloadData)) {
            throw new \Exception('Callback returns an incorrect result');
        }

        return $payloadData;
    }

    protected function logExceptionRollbar($exception)
    {
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
                    return;
                }
            }
        }
        // Check if an error coming from handleError() should be ignored.
        if ($exception instanceof ErrorException && Rollbar::logger()->shouldIgnoreError($exception->getCode())) {
            return;
        }

        $extra = $this->getPayloadData($exception);
        if ($extra === null) {
            $extra = [];
        }
        $level = $this->isFatal($exception) ? Level::CRITICAL : Level::ERROR;

        Rollbar::log($level, $exception, $extra, true);
    }

    protected function isFatal($exception): bool
    {
        return $exception instanceof \Error
            || ($exception instanceof ErrorException
                && ErrorException::isFatalError(['type' => $exception->getSeverity()]));
    }
}
