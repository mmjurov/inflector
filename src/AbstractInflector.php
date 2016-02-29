<?php

namespace Zhmi\Inflector;

abstract class AbstractInflector implements InflectorInterface
{
    /**
     * Функция подготавливает слово к обработке
     * @param $word
     * @return int
     */
    protected function prepareWord(&$word)
    {
        trim($word);

        $length = strlen($word);

        if ($length <= 0) {
            throw new \LogicException('Ошибка при поиске формы слова. Слово не должно быть пустым');
        }

        return true;
    }

    public function inflect($word)
    {
        $this->prepareWord($word);
        return $this->makeInflections($word);
    }

    /**
     * Основная логика выборки должна быть тут. Никакого кеша тут быть не должно
     * @param $word
     * @return mixed
     */
    abstract protected function makeInflections($word);
}