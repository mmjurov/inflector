<?php

namespace Zhmi\Inflector;

use Zhmi\Inflector\Provider\PhpMorphyInflector;
use Zhmi\Inflector\Result\EmptyInflectionResult;
use Zhmi\Inflector\Result\InflectionResult;

class Service
{
    private $encoding = 'utf-8';

    public function __construct($encoding = 'utf-8')
    {
        if ($encoding == 'windows-1251' || $encoding == 'utf-8') {

            $this->encoding = $encoding;

        } else {

            throw new InflectorException('На данный момент возможно использовать только windows-1251 или urf-8 кодировки');

        }
    }

    /**
     * Точка входа при склонении слова. Основная функция, которую нужно вызвать для склонения
     * @param string $word Слово, которое нужно просклонять
     * @return InflectionResult|EmptyInflectionResult Результат склонений слова
     */
    public function inflect($word)
    {
        $provider = $this->getProvider();

        try {

            $r = $provider->inflect($word);
            $result = new InflectionResult($r[0], $r[1], $r[2], $r[3], $r[4], $r[5]);
            $result->setEncoding($this->encoding);
            return $result;

        } catch (\Exception $e) {

            return new EmptyInflectionResult($word);

        }

    }

    protected function getProvider()
    {
        $provider = new PhpMorphyInflector();
        $provider->setEncoding($this->encoding);
        return $provider;
    }

}