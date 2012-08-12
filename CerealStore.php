<?php

/**
 * CerealStore
 *
 * A key-value store that serializes to a base64 encoded string.
 *
 * Example:
 *
 * $store = new CerealStore();
 * $store->add('key', 'value)
 *       ->add('key2', 'value2')
 *       ->add('key3', 'value3');
 *
 * $serialized_string = (string)$store;
 *
 * $store2 = new CerealStore($serialized_string);
 *
 */
class CerealStore implements Iterator, ArrayAccess, Countable {

	protected $store = array();

	/**
	 * Constructor
	 *
	 * @param string - optional serialized data to unserialize into store
	 */
	public function __construct($data = null) {
		if (isset($data)) {
			$this->unserialize($data);
		}
	}

	/**
	 * create() - static method to create an instance, useful for chaining
	 *
	 * @return CerealStore
	 */
	public static function create() {
		return new self();
	}

	/**
	 * add() - adds value to store
	 *
	 * @param string - key
	 * @param mixed - value
	 *
	 * @return $this
	 */
	public function add($key, $value) {
		return $this->offsetSet($key, $value);
	}

	/**
	 * addArray() - adds keys and values from array to the store
	 *
	 * @param array
	 *
	 * @return $this
	 */
	public function addArray($data) {
		foreach($data as $key => $value) {
			$this->add($key, $value);
		}
		return $this;
	}

	/**
	 * get() - returns the value for the given key
	 *
	 * @param string - key
	 *
	 * @return value - returns the value if the key is in the store, null otherwise
	 */
	public function get($key) {
		return $this->offsetGet($key);
	}

	/**
	 * has() - returns whether the key is in the store
	 *
	 * @param string - key
	 *
	 * @return bool - returns true if key exists in store, false otherwise
	 */
	public function has($key) {
		return $this->offsetExists($key);
	}

	/**
	 * remove() - removes the specified key from the store
	 *
	 * @param string - key
	 *
	 * @return $this
	 */
	public function remove($key) {
		return $this->offsetUnset($key);
	}

	/**
	 * isEmpty() - returns whether the store contains any data
	 *
	 * @return bool - returns true if there's no data in the store
	 */
	public function isEmpty() {
		return count($this->store) == 0;
	}

	/**
	 * serialize() - returns the contents of the store as a base64 encoded string
	 *
	 * The store is JSON encoded, GZ deflated, and then base64 encoded.
	 *
	 * @return string
	 */
	public function serialize() {
		return base64_encode(gzdeflate(json_encode($this->store)));
	}

	/**
	 * unserialize() - sets the store to the contents of the serialized data
	 *
	 * @param mixed - a serialized string or object (cast to a string)
	 *
	 * If the data cannot be unserialized, the store will be an empty array
	 *
	 * @return $this
	 */
	public function unserialize($data) {
		// if passed an object, call toString on it
		// this allows us to do $store->unserialize($store2)
		if (is_object($data)) {
			$data = $data->__toString();
		}

		// try to unserialize the data, if anything fails set the store to an empty
		// array
		if ($store = json_decode(gzinflate(base64_decode($data)))) {
			$this->store = $this->objectToArray($store);
		} else {
			$this->store = array();
		}
	
		return $this; 
	}
	
	public function __toString() {
		return $this->serialize($this);
	}

	/**
	 * @internal
	 *
	 * fixKey() - applies formatting to the key as necessary
	 *
	 * This is required to mimic php's behavour where $a[1] === $a['1']
	 *
	 * @param mixed
	 *
	 * @return mixed - if the key is an interger value, casts to an int,
	 * otherwise it returns the key as is
	 */
	protected function fixKey($key) {
		return (is_numeric($key) && intval($key) == $key ? intval($key) : $key);
	}

	/**
	 * @internal
	 *
	 * objectToArray() - converts an object to an associative array
	 *
	 * We can't just cast the object because JSON encoding/decoding may
	 * have turned our numeric keys into strings, which really confuses php.
	 *
	 * @param object
	 *
	 * @return array
	 */
	protected function objectToArray($object) {
		$array = array();
		foreach((array)$object as $key => $value) {
			$array[$this->fixKey($key)] = $value;
		}
		return $array;
	}

	// Iterator
	public function current() {
		return current($this->store);
	}

	public function key() {
		return key($this->store);
	}

	public function next() {
		return next($this->store);
	}

	public function rewind() {
		return reset($this->store);
	}

	public function valid() {
		return key($this->store) !== null;
	}

	// Countable
	public function count() {
		return count($this->store);
	}

	// Array Access
    public function offsetSet($key, $value) {
	    $key = $this->fixKey($key);

        if (is_null($key)) {
            $this->store[] = $value;
        } else {
            $this->store[$key] = $value;
        }

		return $this;
    }

    public function offsetExists($key) {
	    $key = $this->fixKey($key);
	    // isset works in all cases except when the value is null,
	    // so fallback to array_key_exists in that case
	    // this offers a slight performance gain over just using array_key_exists
        return isset($this->store[$key]) || array_key_exists($key, $this->store);
    }

    public function offsetUnset($key) {
	    $key = $this->fixKey($key);
        unset($this->store[$key]);
		return $this;
    }

    public function offsetGet($key) {
	    $key = $this->fixKey($key);
        return (isset($this->store[$key]) ? $this->store[$key] : null);
    }
}