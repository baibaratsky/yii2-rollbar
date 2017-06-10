<?php

namespace baibaratsky\yii\rollbar;

use Rollbar\Rollbar;
use Rollbar\Payload\Level;
use Rollbar\Utilities;
use Yii;
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
            $extra = $this->getPayloadData($exception);

            if ($extra === null) {
                $extra = [Utilities::IS_UNCAUGHT_KEY => true];
            } else {
                $extra = array_merge($extra, [Utilities::IS_UNCAUGHT_KEY => true]);
            }

            Rollbar::log(Level::error(), $exception, $extra);
        }

        parent::logException($exception);
    }

    public function handleError($code, $message, $file, $line)
    {
        Rollbar::errorHandler($code, $message, $file, $line);

        parent::handleError($code, $message, $file, $line);
    }

    public function handleFatalError()
    {
        Rollbar::fatalHandler();

        parent::handleFatalError();
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
}