<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Neti\RestApi\Description;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);

Loader::includeModule($module_id);

\Neti\RestApi\Route\RouteLoader::load();
$routes = \Neti\RestApi\Route\RouteCollection::getCollection();

$userMiddlewares = \Neti\RestApi\Bootstrap\MiddlewareLoader::load();

$html = '<h1 class="title">Карта роутов</h1>';
$html .= '<div class="head_title_route">
            <div class="font width-column">URL</div>
            <div class="font width-column">Название роута</div>
         </div>';
foreach ($routes as $method => $routesWithMethod) {
    $html .= '<div class="method">Методы ' . $method . '</div>';
    foreach ($routesWithMethod as $route) {
        /**
         * @var \Neti\RestApi\Route\RouteCollection $route
         */
        $url = $route->getUrl();
        $controllerName = $route->getControllerName();
        $action = $route->getAction();
        $name = $route->getName();
        $middlewares = $route->getMiddlewares();
        $description = $route->getDescription();

        $html .= '<div class="route">';
        $html .= '<div class="open-more font error none_border" onclick="openMore(this)">+</div>';

        $html .= '<div class="main">';
        $html .= '<div class="url font width-column">' . $url . '</div>';
        $html .= '<div class="name font width-column">' . $name . '</div>';
        $html .= '</div>';

        $html .= '<div class="more">';
        $html .= '<div class="desc">Контроллер:</div>';
        $html .= '<div class="controller font">' . $controllerName . '</div>';
        $html .= '<div class="desc">Метод класса:</div>';
        $html .= '<div class="action font">' . $action . '()</div>';
        $html .= '<div class="middlewares font">';
        $html .= '</div>';


        if (!empty($middlewares)) {
            $html .= '<div class="desc">Миддлвары:</div>';
            $html .= '<ul>';
            foreach ($middlewares as $middleware) {
                $html .= '<li>';
                $html .= '<div class="middleware font">';
                $namespace = $userMiddlewares[$middleware];
                if ($namespace === null) {
                    $namespace = '<span class="error">Такого миддлвара не существует</span>';
                }
                $html .= $middleware . ' - ' . $namespace;
                $html .= '</div>';
                $html .= '</li>';
            }
            $html .= '</ul>';
        }

        if ($description !== null) {
            $html .= '<div class="description"><div class="desc">Описание метода:</div>' . $description . '</div>';
        }

        $html .= '</div>';


        $html .= '</div>';
    }
}

$aTabs = array(
    array(

        'DIV'     => 'neti.restapi',
        'TAB'     => Loc::getMessage('NETIAPI_OPTIONS_TAB_GENERAL'),
        'TITLE'   => Loc::getMessage('NETIAPI_OPTIONS_TAB_GENERAL'),
        'OPTIONS' => array(
            array(
                Description::PARAMETER_NAME_CORS_DOMAINS,
                Loc::getMessage('NETIAPI_OPTIONS_DOMAINS'),
                '',
                array('textarea', 4, 35)
            ),
            array(
                Description::PARAMETER_NAME_PREFIX_INTERFACE,
                Loc::getMessage('NETIAPI_OPTIONS_PREFIX_REST_API'),
                '',
                array('text', 45)
            ),
            array(
                Description::PARAMETER_LIVE_TOKEN_IN_HOURS,
                Loc::getMessage('NETIAPI_OPTIONS_PARAMETER_LIVE_TOKEN_IN_HOURS'),
                '',
                array('text', 45)
            ),
            array(
                Description::PARAMETER_LIVE_REFRESH_TOKEN_IN_HOURS,
                Loc::getMessage('NETIAPI_OPTIONS_PARAMETER_LIVE_REFRESH_TOKEN_IN_HOURS'),
                '',
                array('text', 45)
            ),
            array(
                Description::PARAMETER_SECRET_KEY,
                Loc::getMessage('NETIAPI_OPTIONS_PARAMETER_SECRET_KEY'),
                '',
                array('text', 45)
            ),
        )
    ),
    array(
        'DIV'     => 'restapi-more',
        'TAB'     => 'Карта роутов',
        'TITLE'   => 'Карта роутов',
        'OPTIONS' => array(
            array(
                "ROUTE_MAP",
                "",
                "<div class='dashbord'>" . $html . "</div>",
                array(
                    "statichtml"
                )
            )
        )
    )
);

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->begin();
?>
    <form action="<?= $APPLICATION->getCurPage(); ?>?mid=<?=$module_id; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
        <?= bitrix_sessid_post(); ?>
        <?php
        foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->beginNextTab();
                __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
            }
        }
        $tabControl->buttons();
        ?>
        <input type="submit" name="apply"
               value="<?= Loc::GetMessage('NETI_BLANK_OPTIONS_INPUT_APPLY'); ?>" class="adm-btn-save" />
        <input type="submit" name="default"
               value="<?= Loc::GetMessage('NETI_BLANK_OPTIONS_INPUT_DEFAULT'); ?>" />
    </form>

<?php
$tabControl->end();

if ($request->isPost() && check_bitrix_sessid()) {
}
if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) {
                continue;
            }
            if ($arOption['note']) {
                continue;
            }
            if ($request['apply']) {
                $optionValue = $request->getPost($arOption[0]);
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
            } elseif ($request['default']) {
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->getCurPage().'?mid='.$module_id.'&lang='.LANGUAGE_ID);

}
?>

<style>
    #restapi-more_edit_table .adm-detail-valign-top.adm-detail-content-cell-l {
        display:none !important;
        width: 0 !important
    }
    .main {
        display: flex;
    }

    .route {
        position:relative;
        padding: 10px;
        background-color: #edda7491;
        margin-bottom: 5px;
        border-radius: 5px;
    }

    .font {
        color: #e83168;
        font-weight: bold;
        font-size: 18px;
    }

    .width-column {
        width: 50%
    }

    .head_title_route {
        display: flex;
        margin-bottom: 1rem;
        border-bottom: solid black 1px;
    }

    .desc {
        color: black;
        font-weight: 700;
        margin: 10px 0;
        font-size: 13px !important;
    }

    .more {
        display:none;
        margin-left: 2rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .more.block {
        display: block !important;
    }

    .open-more {
        cursor: pointer;
        position: absolute;
        height: 100%;
        top: 0;
        display: flex;
        align-items: center;
        font-size: 25px;
        padding: 0 10px;
        left: 0;
    }

    .open-more.open {
        align-items:start;
    }

    .method {
        font-size: 22px;
        margin: 20px 0;
    }

    .title {
        margin-top: 0;
        margin-bottom: 2rem;
    }

    .none_border {
        border: none !important;
    }

    .head_title_route > div:first-child,
    .main > div:first-child{
        padding-left: 26px;
    }

    .url {
        font-style: italic;
    }

    .error {
        color: red !important;
        border-bottom: 1px solid red
    }

    .description {
        margin-top: 1rem;
        font-size: 16px;
    }

    .description .desc {
        margin-bottom: 1rem;
    }
</style>

<script>
    function openMore(el) {
        el.classList.toggle('open');
        let parent = el.parentNode;
        let more = parent.querySelector('.more');

        if (more.classList.contains('block')) {
            more.classList.remove('block')
            el.textContent = '+'

        } else {
            more.classList.add('block')
            el.textContent = '-'
        }
    }
</script>
