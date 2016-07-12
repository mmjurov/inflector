<?php

namespace Zhmi\Inflector;

use Apix\Cache;
use Zhmi\Inflector\Provider\PhpMorphyInflector;
use Zhmi\Inflector\Result\EmptyInflectionResult;
use Zhmi\Inflector\Result\InflectionResult;

class Service
{
    private $encoding;

    public function __construct($encoding = 'utf-8')
    {
        if (!$encoding) {
            $encoding = mb_internal_encoding();
            $encoding = mb_strtolower($encoding);
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

            $cache = new Cache\Files(array());
            //if ( !$r = $cache->load( $word )) {
                $r = $provider->inflect($word);
                $cache->save($r, $word, array(), 86400*365);
            //}

            $result = new InflectionResult($r[0], $r[1], $r[2], $r[3], $r[4], $r[5]);
            $result->setEncoding($this->encoding);
            return $result;

        } catch (\Exception $e) {

            return new EmptyInflectionResult($word);

        }

    }

    protected function getProvider()
    {
        return new PhpMorphyInflector($this->encoding);
    }

}