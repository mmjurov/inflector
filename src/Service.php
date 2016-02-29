<?php

namespace Zhmi\Inflector;

use Zhmi\Inflector\Provider\PhpMorphyInflector;
use Zhmi\Inflector\Result\EmptyInflectionResult;
use Zhmi\Inflector\Result\InflectionResult;

class Service
{
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
            return new InflectionResult($r[0], $r[1], $r[2], $r[3], $r[4], $r[5]);

        } catch (\Exception $e) {

            return new EmptyInflectionResult($word);

        }

    }

    protected function getProvider()
    {
        return new PhpMorphyInflector();
    }

}