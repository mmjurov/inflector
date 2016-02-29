<?php

namespace Zhmi\Inflector\Provider;

use Zhmi\Inflector\AbstractInflector;
use Zhmi\Inflector\InflectorException;

class PhpMorphyInflector extends AbstractInflector
{
    private $word = '';
    private $enc = 'utf8';
    private $loc = 'ru_RU';

    protected function prepareWord(&$word)
    {
        parent::prepareWord($word);
        $this->word = $word;

        $word = strtoupper($word);
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
        //TODO Поменять путь до словарей
        $dicts = __DIR__ . '/../../dicts/' . $this->enc . '/'. $this->loc . '/';
        $inflector = new \phpMorphy($dicts, $this->loc);
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