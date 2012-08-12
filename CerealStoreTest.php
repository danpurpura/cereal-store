<?php

require_once('CerealStore.php');

class CerealStoreTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test array access
	 */
	public function testArrayAccess() {
		$store = new CerealStore();

		// set
		$store[] = 'test';
		$this->assertEquals('test', $store->current());

		// set & get
		$store['test'] = 'test2';
		$this->assertEquals('test2', $store['test']);

		// unset
		$this->assertTrue(isset($store['test']));
		unset($store['test']);
		$this->assertFalse(isset($store['test']));
	}

	/**
	 * Test countability
	 */
	public function testCountable() {
		$store = new CerealStore();

		// empty
		$this->assertEquals(0, count($store));

		// add an item
		$store[] = 1;
		$this->assertEquals(1, count($store));

		// add another item
		$store[] = 2;
		$this->assertEquals(2, count($store));

		// remove an item
		unset($store[count($store) - 1]);
		$this->assertEquals(1, count($store));
	}

	/**
	 * Test iteration
	 */
	public function testIterator() {
		$data = array(1, 2, 3, 4);
		$store = new CerealStore();

		$store[] = 1;
		$store[] = 2;
		$store[] = 3;
		$store[] = 4;

		// iterate over our values...
		foreach($store as $key => $value) {
			// compare with data
			$this->assertEquals($value, $data[$key]);
			// remove from data, see below
			unset($data[$key]);
		}

		// if the iterator worked properly, data will now be empty
		$this->assertTrue(empty($data));
	 }

	/**
	 * Test add(key, value)
	 */
	public function testAdd() {
		$store = new CerealStore();
		$store->add('key', 'value');
		$this->assertEquals('value', $store['key']);
	}

	/**
	 * Test get(key)
	 */
	public function testGet() {
		$store = new CerealStore();
		// if not added, should return null
		$this->assertEquals(null, $store->get('key'));
		$store['key'] = 'value';
		$this->assertEquals('value', $store->get('key'));
	}

	/**
	 * Test has()
	 */
	public function testHas() {
		$store = new CerealStore();
		// 1 - empty store => false
		$this->assertFalse($store->has('key'));
		// 2 - add string => true
		$store['key'] = 'value';
		$this->assertTrue($store->has('key'));
		// 3 - false value => true
		$store['key'] = false;
		$this->assertTrue($store->has('key'));
		// 4 - null value => true
		$store['key'] = null;
		$this->assertTrue($store->has('key'));
	}

	/**
	 * Test remove()
	 */
	public function testRemove() {
		$store = new CerealStore();
		$store['key'] = 'value';
		$this->assertTrue($store->has('key'));
		$store->remove('key');
		$this->assertFalse($store->has('key'));
	}

	/**
	 * Test addArray()
	 */
	public function testAddArray() {
		$data = array('one' => 1, 'two' => 2, 'three' => 3);

		$store = new CerealStore();
		$store->addArray($data);

		// should have the same count
		$this->assertEquals(count($data), count($store));

		foreach($data as $key => $value) {
			// we should have the key
			$this->assertTrue($store->has($key));
			// values should be equal
			$this->assertEquals($value, $store->get($key));
		}
	}

	/**
	 * Tests serialize(), unserialize(), __toString()
	 * and accepting a serialized string in the constructor
	 */
	public function testSerialization() {
		$data = array('one' => 1, 'two' => 2, 'three' => 3);

		$store = new CerealStore();
		$store->addArray($data);

		// serialize our store
		$string = $store->serialize();

		// verify we have non-emtpy string
		$this->assertTrue(!empty($string));

		// create a second store using this string
		$store2 = new CerealStore();
		$store2->unserialize($string);

		// verify that it matches our original data
		$count = 0;
		foreach($data as $key => $value) {
			$count++;
			$this->assertTrue($store2->has($key));
			$this->assertEquals($value, $store2->get($key));
		}

		// this check ensures we made it into the loop
		$this->assertEquals($count, count($data));

		// create another store, this time using the constructor
		$store3 = new CerealStore($string);

		// perform the same tests
		$count = 0;
		foreach($data as $key => $value) {
			$count++;
			$this->assertTrue($store3->has($key));
			$this->assertEquals($value, $store3->get($key));
		}

		$this->assertEquals($count, count($data));

		// verify that our new stores serialize to the same string
		// we also test out the toString functionality
		$this->assertEquals($string, $store2->__toString());
		$this->assertEquals($string, (string)$store3);
	}

}
