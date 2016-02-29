<?php

namespace Zhmi\Inflector;

use Zhmi\Inflector\Provider\PhpMorphyInflector;
use Zhmi\Inflector\Result\EmptyInflectionResult;

class Service
{
    /**
     * Точка входа при склонении слова. Основная функция, которую нужно вызвать для склонения
     * @param string $word Слово, которое нужно просклонять
     * @return array Массив склонений слова
     */
    public function inflect($word)
    {
        $provider = $this->getProvider();

        try {

            $result = $provider->inflect($word);
            return $result;

        } catch (\Exception $e) {

            return new EmptyInflectionResult($word);

        }

    }

    protected function getProvider()
    {
        return new PhpMorphyInflector();
    }

}