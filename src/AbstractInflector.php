<?php

namespace Zhmi\Inflector;

abstract class AbstractInflector implements InflectorInterface
{
    /**
     * @var string Хранит кодировку склонятора
     */
    protected $encoding = 'utf-8';

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

    /**
     * Основная функция, которая выполняет склонение. Ее не нужно переопределять в большинстве случаев
     * @param string $word
     * @return mixed
     */
    public function inflect($word)
    {
        $this->prepareWord($word);
        return $this->makeInflections($word);
    }

    /**
     * Устанавливает кодировку склонятора. Используется извне в гланом сервисе
     * @param $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Основная логика выборки должна быть тут. Никакого кеша тут быть не должно
     * @param $word
     * @return mixed
     */
    abstract protected function makeInflections($word);
}