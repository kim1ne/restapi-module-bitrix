<?php

namespace Neti\RestApi\HighLoadBlock;


use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

Loader::IncludeModule('highloadblock');

class User extends ActiveRecord
{
    public function getHLBlockName(): string
    {
        return 'UserTokens';
    }

    public function  getHLBlockTableName(): string
    {
        return 'user_tokens';
    }

    public function langName(): array
    {
        return [
            'ru' => 'Пользательские токены',
            'en' => 'User tokens'
        ];
    }

    public function getFields(string $UFObject): array
    {
        return [
            'USER_ID' => [
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_USER_ID',
                'USER_TYPE_ID' => 'integer',
                'MANDATORY' => 'Y',
                "EDIT_FORM_LABEL" => ['ru' => 'USER_ID', 'en' => 'USER_ID'],
                "LIST_COLUMN_LABEL" => ['ru' => 'USER_ID', 'en' => 'USER_ID'],
                "LIST_FILTER_LABEL" => ['ru' => 'USER_ID', 'en' => 'USER_ID'],
                "ERROR_MESSAGE" => ['ru' => '', 'en' => ''],
                "HELP_MESSAGE" => ['ru' => '', 'en' => ''],
            ],
            'REFRESH_TOKEN' => [
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_REFRESH_TOKEN',
                'USER_TYPE_ID' => 'string',
                'MANDATORY' => 'Y',
                "EDIT_FORM_LABEL" => ['ru' => 'REFRESH_TOKEN', 'en' => 'REFRESH_TOKEN'],
                "LIST_COLUMN_LABEL" => ['ru' => 'REFRESH_TOKEN', 'en' => 'REFRESH_TOKEN'],
                "LIST_FILTER_LABEL" => ['ru' => 'REFRESH_TOKEN', 'en' => 'REFRESH_TOKEN'],
                "ERROR_MESSAGE" => ['ru' => '', 'en' => ''],
                "HELP_MESSAGE" => ['ru' => '', 'en' => ''],
            ],
            'DATE_START' => [
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_DATE_START',
                'USER_TYPE_ID' => 'datetime',
                'MANDATORY' => 'Y',
                "EDIT_FORM_LABEL" => ['ru' => 'DATE_START', 'en' => 'DATE_START'],
                "LIST_COLUMN_LABEL" => ['ru' => 'DATE_START', 'en' => 'DATE_START'],
                "LIST_FILTER_LABEL" => ['ru' => 'DATE_START', 'en' => 'DATE_START'],
                "ERROR_MESSAGE" => ['ru' => '', 'en' => ''],
                "HELP_MESSAGE" => ['ru' => '', 'en' => ''],
            ],
            'DATE_END' => [
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_DATE_END',
                'USER_TYPE_ID' => 'datetime',
                'MANDATORY' => '',
                "EDIT_FORM_LABEL" => ['ru' => 'DATE_END', 'en' => 'DATE_END'],
                "LIST_COLUMN_LABEL" => ['ru' => 'DATE_END', 'en' => 'DATE_END'],
                "LIST_FILTER_LABEL" => ['ru' => 'DATE_END', 'en' => 'DATE_END'],
                "ERROR_MESSAGE" => ['ru' => '', 'en' => ''],
                "HELP_MESSAGE" => ['ru' => '', 'en' => ''],
            ],
        ];
    }
}