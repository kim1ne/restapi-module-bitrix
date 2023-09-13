<?php

use Bitrix\Main\Context;
use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\Error as BitrixError;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Neti\RestApi\Bootstrap\Application;
use Neti\RestApi\Bootstrap\HandlerStart;
use Neti\RestApi\Bootstrap\Middleware\CorsMiddleware;
use Neti\RestApi\Bootstrap\Middleware\PipelineRouteMiddleware;
use Neti\RestApi\Bootstrap\Middleware\RouteMiddleware;
use Neti\RestApi\Bootstrap\Pipeline\Pipeline;

class NetiRestApi extends CBitrixComponent
{
    public function __construct($component = null)
    {
    	$this->result = new Result();

        if (!Loader::includeModule('neti.restapi')) {
            $this->result->addError(
                new BitrixError(Loc::getMessage('MODULE_INCLUDE_ERROR', ['#MODULE_NAME#' => 'neti.restapi']), 500)
            );
        }

        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams)
    {

        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent(): void
    {
        try {
            require $_SERVER['DOCUMENT_ROOT'] . '/local/modules/neti.restapi/config/routes.php';

            $pipeline = new Pipeline();
            $pipeline->setMiddleware([
                new CorsMiddleware(),
                new RouteMiddleware(),
                new PipelineRouteMiddleware()
            ]);
            $pipeline->setHandler(
                new HandlerStart()
            );

            $application = new Application(
                pipeline: $pipeline
            );

            $request = Context::getCurrent()->getRequest();
            $response = $application->handle($request);
        } catch (\Exception $exception) {
            $message = json_decode($exception->getMessage(), true);
            $response = new Json($message);
            $response->setStatus($exception->getCode());
        } finally {
            $response->send();
        }
    }
}
