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

    public function __construct($encoding = 'utf-8')
    {
        if (!$encoding) {
            $encoding = mb_internal_encoding();
            $encoding = mb_strtolower($encoding);
        }

        if ($encoding == 'windows-1251' || $encoding == 'utf-8') {

            $this->encoding = $encoding;

        } else {

            throw new InflectorProviderException('Провайдер ' . __CLASS__ . ' поддерживает только windows-1251 или utf-8 кодировки');

        }
    }

    protected function prepareWord(&$word)
    {
        parent::prepareWord($word);
        $this->word = $word;
        $this->storeWordRegister($word);

        $word = mb_strtoupper($word, $this->encoding);
    }

    private function isUppercaseChar($char)
    {
        $enc = $this->getEncoding();
        $lowerChar = mb_strtolower($char, $enc);
        return $lowerChar != $char;
    }

    private function isSimilarChar($current, $compared)
    {
        $enc = $this->getEncoding();
        //current - это в нижнем регистре
        $lowerChar = mb_strtolower($compared, $enc);
        return $lowerChar == $current;
    }

    private function substrReplace($original, $replacement, $position, $length)
    {
        $enc = $this->getEncoding();
        $startString = mb_substr($original, 0, $position, $enc);
        $endString = mb_substr($original, $position + $length, mb_strlen($original), $enc);

        $out = $startString . $replacement . $endString;

        return $out;
    }

    protected function storeWordRegister($word)
    {
        $this->wordRegister = array();
        $enc = $this->getEncoding();
        $strlen = mb_strlen( $word, $enc );

        $part = $j = 0;
        //Слово может быть разделено тире. Каждое слово нужно запомнить отдельно
        for( $i = 0; $i <= $strlen; $i++ ) {

            $char = mb_substr($word, $i, 1, $enc);

            if ($char == '-') {
                $part++;
                $j = 0;
                continue;
            }

            if ( $this->isUppercaseChar($char) ) {
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
        $enc = $this->getEncoding();

        $newInflections = array();
        foreach ($inflections as $word) {
            //Возвращаем слово к нижнему регистру
            $word = mb_strtolower($word, $enc);
            if (strpos($word, '-')) {
                $words = explode('-', $word);
            } else {
                $words = array($word);
            }

            foreach ($this->wordRegister as $char) {

                $part = $char['part'];
                $word = &$words[ $part ];
                $key = $char['key'];

                $wordChar = mb_substr($word, $key, 1, $enc);

                if ($this->isSimilarChar($wordChar, $char['char'])) {
                    $word = $this->substrReplace($word, $char['char'], $wordChar, 1);
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
        $enc = $this->getEncoding();
        //TODO Поменять путь до словарей
        $dicts = __DIR__ . '/../../dicts/' . $enc . '/'. $this->loc . '/';
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
        if ($enc != 'utf-8') {
            $countableGrammem = iconv('utf-8', $enc, $countableGrammem);
        }

        foreach ($inflectionGrammems as $g) {

            if ($enc != 'utf-8') {
                $g = iconv('utf-8', $enc, $g);
            };
            /** @var \phpMorphy_WordForm  $wordForm */
            $wordForms = $form->getWordFormsByGrammems(array($g, $countableGrammem));

            //Слово может быть несклоняемым, в этом случае просто возвращаем то же слово, что и получили на вход
            if (empty($wordForms)) {
                $result[] = $word;
            } else {
                $wordForm = current($wordForms);
                $result[] = $wordForm->getWord();
            }
        }

        return $this->restoreWordRegister($result);
    }
}

class PhpMorphyInflectorException extends InflectorException {}