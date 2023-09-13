<?
IncludeModuleLangFile(__FILE__);

$ADV_RIGHT = $APPLICATION->GetGroupRight("advertising");
if($ADV_RIGHT!="D")
{
    $aMenu = array(
        "parent_menu" => "global_menu_settings",
        "section" => "neti-restapi",
        "sort" => 10,
        "text" => "Настройка модуля NETI:RestAPI",
        "title" => "Настройка модуля NETI:RestAPI",
        "icon" => "neti-restapi-module-icon",
        "page_icon" => "neti-restapi-module-page-icon",
        "items_id" => "neti_restapi_module",
        "items" => array(
            array(
                array(
                    "text" => "Карта роутов",
                    "url" => "routemap.php?lang=".LANGUAGE_ID,
                    "more_url" => array("routemap.php"),
                    "title" => "Карта роутов"
                ),
            ),
        )
    );

    return $aMenu;
}
return false;
?>
