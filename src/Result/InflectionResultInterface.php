<?php

namespace Zhmi\Inflector\Result;

/**
 * Interface InflectionResultInterface Результат с падежами
 * @package Zhmi\Inflector\Result
 */
interface InflectionResultInterface {

    function getOriginal();
    function getNominative();
    function getGenitive();
    function getDative();
    function getAccusative();
    function getInstrumental();
    function getPrepositional();
    function getInflections();
    function getInflection($code);
}