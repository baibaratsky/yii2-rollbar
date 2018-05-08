<?php

namespace baibaratsky\yii\rollbar\log;

use Rollbar\Rollbar;
use Rollbar\Payload\Level;
use yii\log\Logger;

class Target extends \yii\log\Target
{
    protected $requestId;

    public function init()
    {
        $this->requestId = uniqid(gethostname(), true);
        parent::init();
    }

    public function export()
    {
        foreach ($this->messages as $message) {
            $levelName = self::getLevelName($message[1]);
            Rollbar::log(Level::$levelName(), $message[0], [
                'category' => $message[2],
                'request_id' => $this->requestId,
                'timestamp' => (int)$message[3],
            ]);
        }
    }

    protected static function getLevelName($level)
    {
        if (in_array($level,
                [Logger::LEVEL_PROFILE, Logger::LEVEL_PROFILE_BEGIN, Logger::LEVEL_PROFILE_END, Logger::LEVEL_TRACE])) {
            return 'debug';
        }

        return Logger::getLevelName($level);
    }
}