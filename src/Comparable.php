<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 20/01/2018
 * Time: 09:15
 */

namespace Dorian\Collection;


interface Comparable
{
    /**
     * @param Comparable $object
     * @return int
     */
    public function compareTo(Comparable $object): int;
}