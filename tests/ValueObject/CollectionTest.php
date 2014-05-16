<?php

namespace Message\Cog\Test\ValueObjects;

use Message\Cog\ValueObject\Collection;


class CollectionTest extends \PHPUnit_Framework_TestCase
{
	public function testSettersAreFluidInterface()
	{
		$collection = new Collection;

		$this->assertSame($collection, $collection->setKey('test'));
		// TODO: do this for the others
	}

	public function testDefaultSortingIsByKey()
	{
		$collection = new Collection;
		$collection->setKey('pos');
		$collection->add(['pos' => 3]);
		$collection->add(['pos' => 5]);
		$collection->add(['pos' => -1]);
		$collection->add(['pos' => 0]);

		$sorted = [
			['pos' => -1],
			['pos' => 0],
			['pos' => 3],
			['pos' => 5],
		];

		$this->assertSame($sorted, array_values($collection->all()));
	}

	/**
	 * Checks counting items in an empty array is zero.
	 * Checks all() returns nothing.
	 */
	public function testEmptyArrayHasZeroCount()
	{
		$collection = new Collection([]);

		$this->assertEquals(0,$collection->count());
	 	$this->assertEquals([], $collection->all());
	}

	/**
	 * Checks counting items in an non-empty array is correct.
	 * Checks all() returns all values in an non-empty array.
	 */
	public function testInstantiateWithOneItem()
	{
		$values = [0 => "hello"];
		$collection = new Collection($values);

		$this->assertEquals(1,$collection->count());
		$this->assertEquals($values, $collection->all());

		foreach ($collection as $key => $item) {
			$this->assertEquals($values[$key], $item);
		}

		return $collection;
	}

	/**
	 * Checks adding items works correctly.
	 *
	 * @depends testInstantiateWithOneItem
	 */
	public function testAddingItems(Collection $collection)
	{
		$values = [0 => "hello"];

		$collection->add('hello again');

		$this->assertEquals(array_merge($values, ['hello again']), $collection->all());
		$this->assertEquals(2, $collection->count());
		$this->assertCount(2, $collection);
	}

	/**
	 * Checks removing an item which doesn't exist throws the correct exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testRemovingItemDoesntExist()
	{
		$item = [0 => "hello"];
		$collection = new Collection($item);

		$collection->remove(3);
	}

	/**
	 * Checks getting an item which doesn't exist throws the correct exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testGettingItemThatDoesntExist()
	{
		$item = [0 => "hello"];
		$collection = new Collection($item);

		$collection->get(3);
	}

	/**
	 * Checks setting a key on a non-empty collection throws the correct exception.
	 *
	 * @expectedException \LogicException
	 */
	public function testSetKeyOnNotEmptyThrowsException()
	{
		$item = [0 => "hello"];
		$collection = new Collection($item);

		$collection->setKey("key");
	}

	/**
	 * Checks setting a type on a non-empty collection throws the correct exception.
	 *
	 * @expectedException \LogicException
	 */
	public function testSetTypeOnNotEmptyThrowsException()
	{
		$item = [0 => "hello"];
		$collection = new Collection($item);

		$collection->setType("\DateTime");
	}

	/**
	 * Checks adding an item with a key that already exists throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	// public function testAddingItemWithSameKeyThrowsException()
	// {

	// }

	/**
	 * Checks adding an item with a different type throws the correct exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddItemTypeDoesntMatch()
	{
		$collection = new Collection();

		$collection->setType("\DateTime");

		$object = (object) ['key' => 'hi'];

		$collection->add($object);
	}

	/**
	 * Checks adding an object item with no key throws the correct exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddObjectItemDoesntHaveKey()
	{
		$collection = new Collection();
		$collection->setKey('hello');

		$object = (object) [NULL => 'hi'];

		$collection->add($object);
	}

	/**
	 * Checks adding an array item with no key throws the correct exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddArrayItemDoesntHaveKey()
	{
		$collection = new Collection();
		$collection->setKey('hello');

		$item = [NULL => 'hi'];

		$collection->add($item);
	}

	/**
	 * Check using array notation throws the correct exception.
	 *
	 * @expectedException \BadMethodCallException
	 */
	public function testArrayAccessSettingThrowsException()
	{
		$collection = new Collection;

		$collection['hello'] = 'my thing';
	}

	/**
	 *
	 *
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Item must be array or object
	 */
	public function testAddingItemMustBeArrayOrOjectWhenHasKey()
	{
		$collection = new Collection;
		$collection->setKey('hello');

		$collection->add("hi");
	}

	/**
	 * Check adding item to array with no key set.
	 */
	public function testArrayAccessWithDefaultZeroIndexedKeys()
	{
		$collection = new Collection;
		$item1 = ['hello' => 'hi'];

		$this->assertFalse(isset($collection[0]));

		$collection->add($item1);

		$this->assertTrue(isset($collection[0]));
		$this->assertSame($item1, $collection[0]);

		unset($collection[0]);

		$this->assertFalse(isset($collection[0]));
		$this->assertSame([], $collection->all());
	}

	/**
	 * Check adding mulitple items are added and removed correctly.
	 */
	public function testArrayAccessWithCustomKeys()
	{
		$collection = new Collection;
		$item1 = ['hello' => 'hi'];
		$item2 = ['hello' => 'hey'];

		$collection->setKey('hello');

		$this->assertFalse(isset($collection['hi']));

		$collection->add($item1);
		$collection->add($item2);

		$this->assertTrue(isset($collection['hi']));
		$this->assertSame($item1, $collection['hi']);

		$this->assertTrue(isset($collection['hey']));
		$this->assertSame($item2, $collection['hey']);

		unset($collection['hi']);

		$this->assertFalse(isset($collection['hi']));

		unset($collection['hey']);

		$this->assertFalse(isset($collection['hi']));

		$this->assertSame([], $collection->all());
	}

	/**
	 * Check adding the key to an empty array doesn't throw an error.
	 */
	public function testSetKeyOnObjectValue()
	{
		$collection = new Collection();

		$collection->setKey("key");

		$object = (object) ['key' => 'hi'];

		$collection->add($object);

		$this->assertSame($object, $collection->get('hi'));
	}

	public function testSetKeyToCallable()
	{
		$collection = new Collection;
		$collection->setKey(function($item) {
			return $item['key']['thing'];
		});

		$value = [
			'key' => [
				'thing' => 'Hi there',
			],
			'test' => 'value',
		];

		$collection->add($value);

		$this->assertSame($value, $collection->get('Hi there'));
	}
}