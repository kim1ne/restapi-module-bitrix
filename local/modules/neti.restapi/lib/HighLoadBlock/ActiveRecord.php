<?php

namespace Neti\RestApi\HighLoadBlock;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\SystemException;

abstract class ActiveRecord
{
    private $error;

    public function getErorr()
    {
        return $this->error;
    }

    public function create(): bool
    {
        if (!empty($this->getHLIblock()->fetch())) return true;

        $result = HL\HighloadBlockTable::add([
            'NAME' => $this->getHLBlockName(),
            'TABLE_NAME' => $this->getHLBlockTableName(),
        ]);

        if ($result->isSuccess()) {
            $id = $result->getId();
            foreach ($this->langName() as $lang_key => $lang_val) {
                HL\HighloadBlockLangTable::add([
                    'ID' => $id,
                    'LID' => $lang_key,
                    'NAME' => $lang_val
                ]);
            }
        } else {
            $errors = $result->getErrorMessages();
            $this->error = $errors;
            return false;
        }

        $fields = $this->getFields('HLBLOCK_' . $id);

        $savedFields = [];
        $obUserField = new \CUserTypeEntity();
        foreach ($fields as $arCartField) {
            $ID = $obUserField->Add($arCartField);
            $savedFields[] = $ID;
        }
        return true;
    }

    public function getById(int $id)
    {
        try {
            $class = $this->getClassName();
            return $class::getById($id);
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function count(array $filter = [])
    {
        try {
            $class = $this->getClassName();
            return $class::getCount($filter);
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function update(int $id, array $args)
    {
        try {
            $class = $this->getClassName();
            return $class::update($id, $args);
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws \Exception
     */
    public function add(array $args): AddResult|false
    {
        $class = $this->getClassName();
        return $class::add($args);
    }

    public function delete(int $id): bool
    {
        try {
            $class = $this->getClassName();
            $class::delete($id);
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getClassName(): \Bitrix\Main\ORM\Data\DataManager|string
    {
        $hlId = $this->getHLIblock()->fetch()['ID'];
        $hlblock   = HL\HighloadBlockTable::getById($hlId)->fetch();
        $entity   = HL\HighloadBlockTable::compileEntity( $hlblock ); //генерация класса
        return $entity->getDataClass();
    }

    public function deleteHL(): bool
    {
        try {
            $res = $this->getHLIblock()->fetch();
            if (!empty($res)) {
                $this->dropTable();
                $this->dropLangTable($res['ID']);
                HL\HighloadBlockTable::delete($res['ID']);
            }

            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    private function dropLangTable($id)
    {
        $res = HL\HighloadBlockLangTable::getList([
            'filter' => ['ID' => $id]
        ]);

        while($item = $res->fetch()) {
            HL\HighloadBlockLangTable::delete(['ID' => $item['ID'], 'LID' => $item['LID']]);
        }
    }

    private function dropTable()
    {
        $connection = Application::getConnection();
        $connection->dropTable($this->getHLBlockTableName());
    }

    public function getHLIblock()
    {
        return HL\HighloadBlockTable::getList([
            'filter' => [
                'NAME' => $this->getHLBlockName(),
                'TABLE_NAME' => $this->getHLBlockTableName()
            ]
        ]);
    }

    public abstract function langName(): array;
    public abstract function getFields(string $UFObject): array;
    public abstract function getHLBlockName(): string;
    public abstract function getHLBlockTableName(): string;
}