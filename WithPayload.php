<?php

namespace baibaratsky\yii\rollbar;

interface WithPayload
{
    /**
     * @return array|null Payload data to be sent to Rollbar
     */
    public function rollbarPayload();
}