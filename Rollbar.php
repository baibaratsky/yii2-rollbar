<?php

namespace baibaratsky\yii\rollbar;

use Yii;
use yii\base\Object;

class Rollbar extends Object
{
    public $accessToken;
    public $baseApiUrl;
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
    public $timeout;
    public $proxy;

    /**
     * @var array Exceptions to be ignored by yii2-rollbar
     * Format: ['name of the exception class', 'exception_property' => ['range', 'of', 'values], ...]
     */
    public $ignoreExceptions = [
            ['yii\web\HttpException', 'statusCode' => [404]],
    ];

    public function init()
    {
        \Rollbar::init(
                [
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
                ],
                false,
                false,
                false
        );

        parent::init();
    }
}