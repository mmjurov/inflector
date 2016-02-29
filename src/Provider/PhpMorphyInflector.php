<?php

namespace Zhmi\Inflector\Provider;

use Zhmi\Inflector\AbstractInflector;
use Zhmi\Inflector\InflectorException;

class PhpMorphyInflector extends AbstractInflector
{
    private $word = '';

    protected function prepareWord(&$word)
    {
        parent::prepareWord($word);
        $this->word = $word;

        $word = mb_strtoupper($word, 'windows-1251');
    }

    /**
     * Восстанавливает регистры букв слова по возможности
     * @param array $inflections
     * @return array
     */
    protected function restoreWordRegister(array $inflections)
    {
        return $inflections;
    }

    protected function makeInflections($word)
    {
        $lang = 'ru_RU';
        //TODO Поменять путь до словарей
        $dicts = __DIR__ . '/../../dicts/windows-1251/'.$lang . '/';
        $inflector = new \phpMorphy($dicts, $lang);
        $forms = $inflector->findWord($word);

        if (!($forms instanceof \phpMorphy_WordDescriptor_Collection )) {
            throw new PhpMorphyInflectorException('Не удалось просклонять слово ' . $word);
        }
        /** @var \phpMorphy_WordDescriptor $form */
        $form = $forms[0];

        if (!($form instanceof \phpMorphy_WordDescriptor )) {
            throw new PhpMorphyInflectorException('Внутренняя ошибка склонятора');
        }

        $result = array();

        $grammems = array('ИМ', 'РД', 'ДТ', 'ВН', 'ТВ', 'ПР');
        foreach ($grammems as $g) {

            /** @var \phpMorphy_WordForm  $wordForm */
            $wordForm = current($form->getWordFormsByGrammems(array($g, 'ЕД')));
            $result[] = $wordForm->getWord();
        }

        return $this->restoreWordRegister($result);
    }
}

class PhpMorphyInflectorException extends InflectorException {}