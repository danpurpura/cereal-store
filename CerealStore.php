<?php

class CerealStore implements Iterator, ArrayAccess, Countable {

	protected $store = array();

	public function __construct($data = null) {
		if (isset($data)) {
			$this->unserialize($data);
		}
	}

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

	public function count() {
		return count($this->store);
	}

    public function offsetSet($key, $value) {
        if (is_null($key)) {
            $this->store[] = $value;
        } else {
            $this->store[$key] = $value;
        }

		return $this;
    }

    public function offsetExists($key) {
        return isset($this->store[$key]);
    }

    public function offsetUnset($key) {
        unset($this->store[$key]);
		return $this;
    }

    public function offsetGet($key) {
        return (isset($this->store[$key]) ? $this->store[$key] : null);
    }

	public function serialize() {
		return base64_encode(gzdeflate(json_encode($this->store)));
	}

	public function unserialize($data) {
		if (is_object($data)) {
			$data = $data->__toString();
		}

		if ($store = json_decode(gzinflate(base64_decode($data)))) {
			$this->store = (array)$store;
		} else {
			$this->store = array();
		}

		return $this; 
	}

	public function add($key, $value) {
		return $this->offsetSet($key, $value);
	}

	public function get($key) {
		return $this->offsetGet($key);
	}

	public function has($key) {
		return $this->offsetExists($key);
	}

	public function remove($key) {
		return $this->offsetUnset($key);
	}

	public function addArray($data) {
		foreach($data as $key => $value) {
			$this->add($key, $value);
		}
		return $this;
	}

	public function __toString() {
		return $this->serialize($this);
	}
}