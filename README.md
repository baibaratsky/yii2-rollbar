Rollbar for Yii2
================
[![Packagist](https://img.shields.io/packagist/l/baibaratsky/yii2-rollbar.svg)](https://github.com/baibaratsky/yii2-rollbar/blob/master/LICENSE.md)
[![Dependency Status](https://www.versioneye.com/user/projects/55ba6130653762001a00189a/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55ba6130653762001a00189a)
[![Packagist](https://img.shields.io/packagist/v/baibaratsky/yii2-rollbar.svg)](https://packagist.org/packages/baibaratsky/yii2-rollbar)
[![Packagist](https://img.shields.io/packagist/dt/baibaratsky/yii2-rollbar.svg)](https://packagist.org/packages/baibaratsky/yii2-rollbar)

This extension is the way to integrate [Rollbar](http://rollbar.com/) service with your Yii2 application.
For Yii 1.* use [yii-rollbar](https://github.com/baibaratsky/yii-rollbar).


Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/). 

 To install, either run
 ```
 $ php composer.phar require baibaratsky/yii2-rollbar:1.8.*
 ```
 or add
 ```
 "baibaratsky/yii2-rollbar": "1.8.*"
 ```
 to the `require` section of your `composer.json` file.

If you want to use it with **Yii prior to 2.0.13**, you need yii2-rollbar of the version `1.6.*`.

Usage
-----
0. Add the component configuration in your *global* `main.php` config file:
 ```php
 'bootstrap' => ['rollbar'],
 'components' => [
     'rollbar' => [
         'class' => 'baibaratsky\yii\rollbar\Rollbar',
         'accessToken' => 'POST_SERVER_ITEM_ACCESS_TOKEN',
         
         // You can specify exceptions to be ignored by yii2-rollbar:
         // 'ignoreExceptions' => [
         //         ['yii\web\HttpException', 'statusCode' => [400, 404]],
         //         ['yii\web\HttpException', 'statusCode' => [403], 'message' => ['This action is forbidden']],
         // ],
     ],
 ],
 ```

0. Add the *web* error handler configuration in your *web* config file:
 ```php
 'components' => [
     'errorHandler' => [
         'class' => 'baibaratsky\yii\rollbar\web\ErrorHandler',
         
         // You can include additional data in a payload:
         // 'payloadDataCallback' => function (\baibaratsky\yii\rollbar\web\ErrorHandler $errorHandler) {
         //     return [
         //         'exceptionCode' => $errorHandler->exception->getCode(),
         //         'rawRequestBody' => Yii::$app->request->getRawBody(),
         //     ];
         // },
     ],
 ],
 ```

0. Add the *console* error handler configuration in your *console* config file:
 ```php
 'components' => [
     'errorHandler' => [
         'class' => 'baibaratsky\yii\rollbar\console\ErrorHandler',
     ],
 ],
 ```


Payload from your exceptions
----------------------------
If you want your exceptions to send some additional data to Rollbar,
it is possible by implementing the `WithPayload` interface.
 ```php
 use baibaratsky\yii\rollbar\WithPayload;
 
 class SomeException extends \Exception implements WithPayload
 {
     public function rollbarPayload()
     {
         return ['foo' => 'bar'];
     }
 }
 ```


Log Target
----------
You may want to collect your logs produced by `Yii::error()`, `Yii::info()`, etc. in Rollbar.
Put the following code in your config:
 ```php
 'log' => [
     'targets' => [
         [
             'class' => 'baibaratsky\yii\rollbar\log\Target',
             'levels' => ['error', 'warning', 'info'], // Log levels you want to appear in Rollbar
             
             // It is highly recommended that you specify certain categories.
             // Otherwise, the exceptions and errors handled by the error handlers will be duplicated.
             'categories' => ['application'],
         ],
     ],
 ],
 ```

The log target also appends `category` and `request_id` parameters to the log messages.
`request_id` is useful if you want to have a *yii2-debug*-like  grouping.
