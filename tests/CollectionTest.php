<?php

use Dorian\Collection\CollectionException;
use Dorian\Collection\Collection;
use Tests\Framework\Objects\Comparable1;
use Tests\Framework\Objects\Comparable2;
use Tests\Framework\Objects\TypeCommun;

/**
 * Created by PhpStorm.
 * User: doria
 * Date: 18/01/2018
 * Time: 10:48
 */
class CollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testAddElements()
    {
        $collection = new Collection();
        $collection->add("test1");
        $collection->add("test2");
        $this->assertEquals(2, $collection->size());
    }

    public function testCreateCollectionFromArray()
    {
        $array = ["test", "test"];
        $collection = new Collection();
        $collection->addArray($array);
        $this->assertEquals(2, $collection->size());
    }

    public function testDeleteElements()
    {
        $object = new stdClass();
        $object->test = "dorian";
        $array = [new stdClass(), $object];
        $collection = new Collection();
        $collection->addArray($array);
        $collection->removeObject($object);
        $this->assertEquals(1, $collection->size());
        $collection->remove(0);
        $this->assertEquals(0, $collection->size());
    }

    public function testSortCollectionWithComparableElements()
    {
        $array = [
            new Comparable1(),
            new Comparable2()
        ];
        $arraySorted = [
            new Comparable2(),
            new Comparable1()
        ];
        $collection = new Collection();
        $collection->addArray($array);
        $collection->sort(true);
        $this->assertEquals($arraySorted, $collection->toArray());
    }

    public function testCollectionSortComparableWithElementNotImplementComparableInterface()
    {
        $array = [
            new Comparable1(),
            "test",
            new Comparable2()
        ];
        $collection = new Collection();
        $collection->addArray($array);
        $this->expectException(CollectionException::class);
        $collection->sort(true);

    }


    public function testChangeOrderKey()
    {
        $array = ["test", "test", "test"];
        $keys = [0, 1];
        $collection = new Collection();
        $collection->addArray($array);
        $collection->remove(1);
        $this->assertEquals($keys, $collection->getKeys());
    }

    public function testIterable()
    {
        $counter = 0;
        $array = ["test", "test", "test"];
        $collection = new Collection();
        $collection->addArray($array);
        foreach ($collection as $value) {
            $counter++;
        }
        $this->assertEquals(3, $counter);
    }

    public function testArrayAccess()
    {
        $array = ["test1", "test2", "test3"];
        $collection = new Collection();
        $collection->addArray($array);
        $this->assertEquals("test1", $collection[0]);
        $this->assertEquals("test3", $collection[2]);
    }

    public function testUsingBaseFunctionCollection()
    {
        $array = ["test1", "test2", "test3"];
        $collection = new Collection();
        $collection->addArray($array);
        $this->assertEquals(3, $collection->size());
        $this->assertEquals("test2", $collection->getObject("test2"));
        $this->assertFalse($collection->contains("test8"));
        $this->assertTrue($collection->contains("test3"));
        $this->assertEquals("test1", $collection->get(0));
        $collection->remove(0);
        $this->assertEquals(null, $collection->getObject("test1"));
        $collection->removeObject("test3");
        $this->assertEquals(null, $collection->getObject("test3"));
        $this->assertEquals(1, $collection->size());
    }

    public function testGeneralAutoSystemWithType()
    {
        $array = [
            new Comparable1(),
            new Comparable2()
        ];
        $arraySorted = [
            new Comparable2(),
            new Comparable1()
        ];
        $collection = new Collection(["sorted" => true, "comparable" => true, "type" => TypeCommun::class]);
        $collection->addArray($array);
        $this->assertEquals($arraySorted, $collection->toArray());
    }

    public function testTypesCollection()
    {
        $collection = new Collection(["sorted" => true, "type" => "datetime"]);
        $collectionString = new Collection(["type" => "string"]);
        $collectionComparable1 = new Collection(["type" => Comparable1::class, "comparable" => true]);
        $collectionComparable1->add(new Comparable1());
        $this->assertEquals(1, $collectionComparable1->size());
        $collectionString->add("test");
        $this->assertEquals(1, $collectionString->size());
        $this->expectException(CollectionException::class);
        $collectionString->add(52);
        $this->expectException(CollectionException::class);
        $collection->add(new Comparable1());
    }

    public function testAutoSortedWithComparableElements()
    {
        $arraySorted = [
            new Comparable2(),
            new Comparable1(),
            new Comparable1()
        ];
        $collection = new Collection(["sorted" => true, "comparable" => true]);
        $collection->add(new Comparable1());
        $collection->add(new Comparable2());
        $collection->add(new Comparable1());
        $this->assertEquals($arraySorted, $collection->toArray());

    }

    public function testCreateCollectionFromJson()
    {
        $array = ["test1", "test2"];
        $array = json_encode($array);
        $this->assertTrue(true);
        /*$collection = new Collection($array, true);
        $this->assertEquals(2, $collection->size());
        $this->assertEquals("test2", $collection->getObject("test2"));*/
    }
}