<?php

use Bitrix\Main\HttpApplication;
use \Bitrix\Main\Localization\Loc;

use Bitrix\Main\Config\Option;

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);

$requestOptions = $request->getValues()['options'];

foreach ($requestOptions as $key => $value) {
    Option::set($module_id, $key, $value);
}

if (!check_bitrix_sessid()) return;

global $APPLICATION;

if ($ex = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => 'HTML'
    ));
} else {
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
}
?>
<?php ?>
<form action="<?= $APPLICATION->GetCurPage(); ?>" name="blank-install">
    <?=bitrix_sessid_post()?>
    <input type="submit" name="" value="<?=Loc::getMessage("MODULE_NETI_IN_MENU") ?>">
</form>