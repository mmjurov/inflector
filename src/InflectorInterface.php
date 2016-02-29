<?php

namespace Zhmi\Inflector;

use Zhmi\Inflector\Result\InflectionResultInterface;

interface InflectorInterface {

    /**
     * @param string $word �����, ������� ����� �����������
     * @return InflectionResultInterface ��������� �� �����������
     */
    function inflect($word);
}