<?php

namespace Zhmi\Inflector;

abstract class AbstractInflector implements InflectorInterface
{
    /**
     * ������� �������������� ����� � ���������
     * @param $word
     * @return int
     */
    protected function prepareWord(&$word)
    {
        trim($word);

        $length = strlen($word);

        if ($length <= 0) {
            throw new \LogicException('������ ��� ������ ����� �����. ����� �� ������ ���� ������');
        }

        return true;
    }

    public function inflect($word)
    {
        $this->prepareWord($word);
        return $this->makeInflections($word);
    }

    /**
     * �������� ������ ������� ������ ���� ���. �������� ���� ��� ���� �� ������
     * @param $word
     * @return mixed
     */
    abstract protected function makeInflections($word);
}