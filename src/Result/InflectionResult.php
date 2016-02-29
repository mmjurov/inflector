<?php

namespace Zhmi\Inflector\Result;

class InflectionResult implements InflectionResultInterface, \ArrayAccess, \Iterator, \Countable {

    private $inflections = array();
    private $iterator = 0;

    public function __construct($w1, $w2 = null, $w3 = null, $w4 = null, $w5 = null, $w6 = null)
    {
        $result = array(
            $w1,
            $w2 === null ? $w1 : $w2,
            $w3 === null ? $w1 : $w3,
            $w4 === null ? $w1 : $w4,
            $w5 === null ? $w1 : $w5,
            $w6 === null ? $w1 : $w6,
        );
    }

    public function getOriginal()
    {
        return $this->getInflection(0);
    }

    function getNominative()
    {
        return $this->getOriginal();
    }

    function getGenitive()
    {
        return $this->getInflection(1);
    }

    function getDative()
    {
        return $this->getInflection(2);
    }

    function getAccusative()
    {
        return $this->getInflection(3);
    }

    function getInstrumental()
    {
        return $this->getInflection(4);
    }

    function getPrepositional()
    {
        return $this->getInflection(5);
    }

    function getInflections()
    {
        return $this->inflections;
    }

    function getInflection($code)
    {
        $code = strtolower($code);
        switch ($code)
        {
            case 'nominative':
            case 'именительный':
            case '0':
                $inflectionNum = 0;
                break;

            case 'genitive':
            case 'родительный':
            case '1':
                $inflectionNum = 1;
                break;

            case 'dative':
            case 'дательный':
            case '2':
                $inflectionNum = 2;
                break;

            case 'accusative':
            case 'винительный':
            case '3':
                $inflectionNum = 3;
                break;

            case 'instrumental':
            case 'творительный':
            case '4':
                $inflectionNum = 4;
                break;

            case 'prepositional':
            case 'предложный':
            case '5':
                $inflectionNum = 5;
                break;

            default:
                $inflectionNum = 0;
                break;
        }

        return (!empty($this->inflections) && strlen($this->inflections[$inflectionNum]) > 0) ?
            $this->inflections[$inflectionNum] : null;
    }

    /**
     * Имплемент для ArrayAccess. Проверяем наличие элемента в массиве
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $i = $this->getInflection($offset);
        return $i !== null;
    }

    /**
     * Имплемент для ArrayAccess. Возвращаем значение элемента массива
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->getInflection($offset) : null;
    }

    /**
     * Запрещаем устанавливать значение для массива
     * @param mixed $offset
     * @param mixed $value
     * @return null
     */
    public function offsetSet($offset, $value)
    {
        return null;
    }

    /**
     * Запрещаем удаление элементов из массива
     * @param mixed $offset
     * @return null
     */
    public function offsetUnset($offset)
    {
        return null;
    }

    public function current()
    {
        return $this->getInflection($this->iterator);
    }

    public function next()
    {
        return $this->iterator++;
    }

    public function key()
    {
        return $this->iterator;
    }

    public function valid()
    {
        return $this->offsetExists($this->iterator);
    }

    public function rewind()
    {
        $this->iterator = 0;
    }

    public function count()
    {
        return count($this->getInflections());
    }
}