<?php

use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) return;

global $APPLICATION;

?>

<form action="<?= $APPLICATION->GetCurPage(); ?>" name="blank-install">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="hidden" name="id" value="neti.restapi">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">

    <p><input type="text" name="options[option-1]" id="option-1"><label for="option-1">Опция-1</label></p>
    <p><input type="text" name="options[option-2]" id="option-2"><label for="option-2">Опция-2</label></p>
    <p><input type="text" name="options[option-3]" id="option-3"><label for="option-3">Опция-3</label></p>
    <input type="submit" name="inst" value="<?=Loc::getMessage('MOD_INSTALL_BUTTON')?>">
</form>