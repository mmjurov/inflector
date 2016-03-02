<?php

namespace Zhmi\Inflector\Provider;

use Zhmi\Inflector\AbstractInflector;
use Zhmi\Inflector\InflectorException;

class PhpMorphyInflector extends AbstractInflector
{
    private $word = '';
    private $wordRegister = array();
    protected $encoding = 'utf-8';
    private $loc = 'ru_RU';

    protected function prepareWord(&$word)
    {
        parent::prepareWord($word);
        $this->word = $word;
        $this->storeWordRegister($word);

        $word = mb_strtoupper($word, $this->encoding);
    }

    protected function storeWordRegister($word)
    {
        $this->wordRegister = array();
        $strlen = strlen( $word );

        $enc = $this->encoding;

        $isUppercaseChar = function($char) use ($enc)
        {
            $lowerChar = mb_strtolower($char, $enc);
            return $lowerChar != $char;
        };

        $part = $j = 0;
        //Слово может быть разделено тире. Каждое слово нужно запомнить отдельно
        for( $i = 0; $i <= $strlen; $i++ ) {

            $char = $word[ $i ];

            if ($char == '-') {
                $part++;
                $j = 0;
                continue;
            }

            if ( $isUppercaseChar($char) ) {
                $this->wordRegister[ $i ] = array(
                    'char' => $char,
                    'key' => $j,
                    'part' => $part
                );
            }
            $j++;
        }
    }

    /**
     * Восстанавливает регистры букв слова по возможности
     * @param array $inflections
     * @return array
     */
    protected function restoreWordRegister(array $inflections)
    {
        $enc = $this->encoding;

        $isSimilarChar = function($current, $compared) use ($enc) {
            //current - это в нижнем регистре
            $lowerChar = mb_strtolower($compared, $enc);
            return $lowerChar == $current;
        };

        $newInflections = array();
        foreach ($inflections as $word) {
            //Возвращаем слово к нижнему регистру
            $word = mb_strtolower($word, $this->encoding);
            if (strpos($word, '-')) {
                $words = explode('-', $word);
            } else {
                $words = array($word);
            }

            foreach ($this->wordRegister as $char) {

                $part = $char['part'];
                $word = &$words[ $part ];

                $key = $char['key'];
                if ($isSimilarChar($word[ $key ], $char['char'])) {
                    $word[ $key ] = $char['char'];
                }
            }

            if (count($words) > 0) {
                $newInflections[] = implode('-', $words);
            } else {
                $newInflections[] = $word;
            }

        }
        return $newInflections;
    }

    protected function makeInflections($word)
    {
        //TODO Поменять путь до словарей
        $dicts = __DIR__ . '/../../dicts/' . $this->encoding . '/'. $this->loc . '/';
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

        $inflectionGrammems = array('ИМ', 'РД', 'ДТ', 'ВН', 'ТВ', 'ПР');
        $countableGrammem = 'ЕД';
        if ($this->encoding != 'utf-8') {
            $countableGrammem = iconv('utf-8', $this->encoding, $countableGrammem);
        }

        foreach ($inflectionGrammems as $g) {

            if ($this->encoding != 'utf-8') {
                $g = iconv('utf-8', $this->encoding, $g);
            };
            /** @var \phpMorphy_WordForm  $wordForm */
            $wordForm = current($form->getWordFormsByGrammems(array($g, $countableGrammem)));
            $result[] = $wordForm->getWord();
        }

        return $this->restoreWordRegister($result);
    }
}

class PhpMorphyInflectorException extends InflectorException {}