<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 18/01/2018
 * Time: 11:39
 */

namespace Tests\Framework\Objects;


use Dorian\Collection\Comparable;

class Comparable2 extends TypeCommun implements Comparable
{
    public function getChiffre(): int
    {
        return 10;
    }

    /**
     * @param Comparable $object
     * @return int
     */
    public function compareTo(Comparable $object): int
    {
        if ($this->getChiffre() > $object->getChiffre()) {
            return 1;
        }
        return -1;
    }
}