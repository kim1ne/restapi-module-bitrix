<?php

namespace Neti\RestApi\Tools;

use Neti\RestApi\Description;

class UrlWriter
{
    const KEY_RULE_PATH = 'URI';

    public function write(string $pathToInterface)
    {
        $this->delete();
        \Bitrix\Main\UrlRewriter::add(SITE_ID, [
            'CONDITION' => '#^' . $pathToInterface . '(\w+)#',
            'RULE' => self::KEY_RULE_PATH . '=$1',
            'ID' => Description::MODULE_NAME,
            'PATH' => $pathToInterface . '/index.php',
        ]);
    }

    public function delete()
    {
        \Bitrix\Main\UrlRewriter::delete(SITE_ID, [
            'ID' => Description::MODULE_NAME
        ]);
    }
}
