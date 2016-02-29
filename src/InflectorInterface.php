<?php

namespace Zhmi\Inflector;

use Zhmi\Inflector\Result\InflectionResultInterface;

interface InflectorInterface {

    /**
     * @param string $word Слово, которое нужно просклонять
     * @return InflectionResultInterface Результат со склонениями
     */
    function inflect($word);
}