<?php

use \Bitrix\Main\Localization\Loc;

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
<form action="<?= $APPLICATION->GetCurPage(); ?>" name="blank-install">
    <?=bitrix_sessid_post()?>
    <input type="submit" name="" value="<?=Loc::getMessage("MODULE_NETI_IN_MENU") ?>">
</form>