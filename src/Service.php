<?php

namespace Zhmi\Inflector;

use Zhmi\Inflector\Provider\PhpMorphyInflector;
use Zhmi\Inflector\Result\EmptyInflectionResult;

class Service
{
    /**
     * ����� ����� ��� ��������� �����. �������� �������, ������� ����� ������� ��� ���������
     * @param string $word �����, ������� ����� �����������
     * @return array ������ ��������� �����
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