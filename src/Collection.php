<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 18/01/2018
 * Time: 09:42
 */

namespace Dorian\Collection;

use Dorian\Collection\CollectionException;
use Dorian\Collection\Comparable;

class Collection implements \ArrayAccess, \Iterator
{

    /**
     * @var int Current element
     */
    private $current;

    /**
     * @var array|null
     */
    private $array;

    /**
     * Liste des paramètres disponibles
     * @var array
     */
    private $params = [
        "isJson" => false,
        "sorted" => false,
        "comparable" => false,
        "type" => null
    ];

    /**
     * Collection constructor.
     * @param array $array
     * @param array|null $params
     */
    public function __construct($array = [], ?array $params = [])
    {
        $this->makeParams($params);
        $this->array = $this->initialiseFromJsonString($array);
        $this->sortCollection();
        $this->rewind();
    }

    /**
     * Initialise les paramètres à tous moment
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->makeParams($params);
    }

    /**
     * @param $string
     * @return array
     * @throws CollectionException
     */
    private function initialiseFromJsonString($string): array
    {
        if ($this->params["isJson"]) {
            if (!is_array($string)) {
                $newArray = json_decode((string)$string, true);
                if (!is_null($newArray)) {
                    return $newArray;
                }
                throw new CollectionException("Undefined JSON: $string");
            }
            throw new CollectionException("You must give a JSON not an Array");
        }
        foreach ($string as $object) {
            $this->checkAddNewValueInCollection($object);
        }
        return $string;
    }

    /**
     * @param array $params
     * @throws CollectionException
     */
    private function makeParams(array $params)
    {
        foreach ($params as $key => $value) {
            if ($key == "type") {
                $this->params[$key] = $value;
            } else {
                if (isset($this->params[$key]) && is_bool($value)) {
                    $this->params[$key] = $value;
                } else {
                    throw new CollectionException("Undefined params $key => $value");
                }
            }
        }
    }

    /**
     * Tri la collection en fonction des paramètres qui lui sont données
     * @throws CollectionException
     */
    public function sortCollection(): self
    {
        if ($this->params["sorted"]) {
            if ($this->params["comparable"]) {
                $this->sortComparableELements();
            } else {
                if (!sort($this->array)) {
                    throw new CollectionException("Can't execute sort on this collection");
                }
            }
        }
        return $this;
    }

    /**
     * @param $object
     */
    private function checkAddNewValueInCollection($object)
    {
        if ($this->params["comparable"]) {
            $this->implementComparable($object);
        }
        $this->checkTypeElement($object);
    }

    /**
     * @param $element
     * @throws CollectionException
     */
    private function checkTypeElement($element)
    {
        if ($this->params["type"] != null) {
            $typeOfElement = strtolower(gettype($element));
            if ($typeOfElement == "object") {
                $test = $element instanceof $this->params["type"];
                if (!$test) {
                    throw new CollectionException("Elements must be {$this->params["type"]}");
                }
            } else {
                if ($typeOfElement !== $this->params["type"]) {
                    throw new CollectionException("Elements must be {$this->params["type"]}");
                }
            }
        }
    }

    /**
     * @param $chars
     * @return bool
     */
    private function isJson($chars): bool
    {
        return !is_array($chars) && json_encode($chars) !== null;
    }

    /**
     * Vérifie si la collection contient l'objet passé en paramètre
     * @param $object
     * @return bool
     */
    public function contains($object): bool
    {
        return in_array($object, $this->array);
    }

    /**
     * Ajoute un objet à la collection en effectuant des vérification en fonction des paramètres
     * @param $object
     */
    public function add($object)
    {
        $this->checkAddNewValueInCollection($object);
        $this->array[] = $object;
        $this->sortCollection();
    }

    /**
     * Modifie un objet de la collection si ce dernier n'existe pas une Exception est levee
     * @param int $key Clef de la valeur à modifier
     * @param $value Valeur à insérer
     * @throws CollectionException La clef n'est pas définie
     */
    public function set(int $key, $value)
    {
        if (!$this->exist($key)) {
            throw new CollectionException("Undefined index $key");
        }
        $this->checkAddNewValueInCollection($value);
        $this->array[$key] = $value;
        $this->sortCollection();
    }

    /**
     * @param $object
     * @return int
     * @throws CollectionException
     */
    public function getObjectPosition($object): int
    {
        if (!$this->contains($object)) {
            throw new CollectionException("Missing object in colection");
        }
        return array_search($object, $this->array);
    }

    /**
     * Modifie la valeur d'un objet si il est présent dans la collection
     * @param $object
     * @throws CollectionException L'objet n'existe pas dans la collection
     */
    public function setObject($object)
    {
        if ($this->contains($object)) {
            $this->array[array_search($object, $this->array)] = $object;
        } else {
            throw new CollectionException("Missing object in collection");
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function exist($key): bool
    {
        return array_key_exists($key, $this->array);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->array[$key] ?? null;
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return count($this->array);
    }

    /**
     * Sort key
     */
    private function changerKeyOrder(): void
    {
        $newArray = [];
        $i = 0;
        foreach ($this->array as $value) {
            $newArray[$i] = $value;
            $i++;
        }
        $this->array = $newArray;
    }

    /**
     * @return Collection
     * @throws CollectionException
     */
    public function sortComparableELements(): self
    {
        $newArray = $this->quick_sort($this->array);
        $this->array = $newArray;
        return $this;
    }

    /**
     * @param $array
     * @return array
     */
    private function quick_sort($array)
    {
        $length = count($array);
        if ($length <= 1) {
            return $array;
        } else {
            $pivot = $array[0];
            $this->implementComparable($pivot);
            $left = $right = array();
            for ($i = 1; $i < count($array); $i++) {
                $this->implementComparable($array[$i]);
                $object = $array[$i];
                if ($object->compareTo($pivot) == -1) {
                    $left[] = $array[$i];
                } else {
                    $right[] = $array[$i];
                }
            }
            return array_merge($this->quick_sort($left), array($pivot), $this->quick_sort($right));
        }
    }

    /**
     * @param $object
     * @throws CollectionException
     */
    private function implementComparable($object)
    {
        if (!is_object($object) || !$object instanceof Comparable) {
            throw new CollectionException("Any object not implement Comparable interface.");
        }
    }

    /**
     * @param $object
     * @return mixed|null
     */
    public function getObject($object)
    {
        if ($this->contains($object)) {
            return $this->array[array_search($object, $this->array)];
        }
        return null;
    }

    /**
     * @param $object
     * @throws CollectionException
     */
    public function removeObject($object)
    {
        if ($this->contains($object)) {
            unset($this->array[array_search($object, $this->array)]);
            $this->changerKeyOrder();
            $this->sortCollection();
        } else {
            throw new CollectionException("Collection not contains object $object");
        }
    }

    public function toJson(): string
    {
        return json_encode($this->array);
    }

    public function setListAttributesFromJson(string $json): void
    {
        $this->array = array_merge($this->array, json_decode($json, true));
        $this->changerKeyOrder();
        $this->sortCollection();
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        $newArray = $this->array;
        return $newArray;
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->array);
    }

    /**
     * @param $object
     * @return false|int|string
     */
    public function indexOf($object)
    {
        return array_search($object, $this->array);
    }

    /**
     * Return a clone about Collection
     * @return Collection
     */
    public function clone(): Collection
    {
        $newArray = $this->array;
        return new Collection($newArray);
    }

    /**
     * Clear collection
     */
    public function removeAll()
    {
        $this->array = [];
    }

    /**
     * @param $key
     * @throws CollectionException
     */
    public function remove($key)
    {
        if ($this->exist($key)) {
            unset($this->array[$key]);
            $this->changerKeyOrder();
        } else {
            throw new CollectionException("Undefined index $key.");
        }
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->exist($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->get($this->current);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->current;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->exist($this->current);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->current = 0;
    }
}