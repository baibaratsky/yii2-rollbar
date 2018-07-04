<?php

namespace baibaratsky\yii\rollbar;

use Rollbar\Rollbar as BaseRollbar;
use Yii;
use yii\base\BaseObject;

class Rollbar extends BaseObject
{
    public $enabled = true;
    public $accessToken;
    public $baseApiUrl = 'https://api.rollbar.com/api/1/';
    public $batchSize;
    public $batched;
    public $branch;
    public $codeVersion;
    public $environment;
    public $host;
    public $includedErrno;
    public $logger;
    public $personFn;
    public $root = '@app';
    public $scrubFields = ['passwd', 'password', 'secret', 'auth_token', '_csrf'];
    public $timeout = 3;
    public $proxy;
    public $enableUtf8Sanitization = true;

    /**
     * @var array Exceptions to be ignored by yii2-rollbar
     * Format: ['name of the exception class', 'exception_property' => ['range', 'of', 'values], ...]
     */
    public $ignoreExceptions = [
        ['yii\web\HttpException', 'statusCode' => [404]],
    ];

    public function init()
    {
        BaseRollbar::init([
            'enabled' => $this->enabled,
            'access_token' => $this->accessToken,
            'base_api_url' => $this->baseApiUrl,
            'batch_size' => $this->batchSize,
            'batched' => $this->batched,
            'branch' => $this->branch,
            'code_version' => $this->codeVersion,
            'environment' => $this->environment,
            'host' => $this->host,
            'included_errno' => $this->includedErrno,
            'logger' => $this->logger,
            'person_fn' => $this->personFn,
            'root' => !empty($this->root) ? Yii::getAlias($this->root) : null,
            'scrub_fields' => $this->scrubFields,
            'timeout' => $this->timeout,
            'proxy' => $this->proxy,
            'enable_utf8_sanitization' => $this->enableUtf8Sanitization,
        ], false, false, false);
    }
}
