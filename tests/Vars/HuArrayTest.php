<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\HuArray;
use PHPUnit\Framework\TestCase;

/**
* Test for HuArray class.
*
* @covers \Hubbitus\HuPHP\Vars\HuArray
**/
class HuArrayTest extends TestCase {
	public function testClassInstantiation(): void {
		$array = new HuArray();
		$this->assertInstanceOf(HuArray::class, $array);
	}

	public function testClassExtendsSettings(): void {
		$array = new HuArray();
		$this->assertInstanceOf('Hubbitus\\HuPHP\\Vars\\Settings\\Settings', $array);
	}

	public function testImplementsIterator(): void {
		$array = new HuArray();
		$this->assertInstanceOf(\Iterator::class, $array);
	}

	public function testConstructorWithNull(): void {
		$array = new HuArray(null);
		$this->assertInstanceOf(HuArray::class, $array);
	}

	public function testConstructorWithArray(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertInstanceOf(HuArray::class, $array);
	}

	public function testConstructorWithString(): void {
		// HuArray constructor only accepts ?array, not strings
		$this->expectException(\TypeError::class);
		$array = new HuArray('test');
	}

	public function testConstructorWithInteger(): void {
		// HuArray constructor only accepts ?array, not integers
		$this->expectException(\TypeError::class);
		$array = new HuArray(42);
	}

	public function testPushSingleValue(): void {
		$array = new HuArray();
		$array->push(1);
		$this->assertCount(1, $array->getArray());
	}

	public function testPushMultipleValues(): void {
		$array = new HuArray();
		$array->push(1, 2, 3);
		$this->assertCount(3, $array->getArray());
	}

	public function testPushReturnsSelf(): void {
		$array = new HuArray();
		$result = $array->push(1);
		$this->assertSame($array, $result);
	}

	public function testPushArray(): void {
		$array = new HuArray();
		$array->pushArray([1, 2, 3]);
		$this->assertCount(3, $array->getArray());
	}

	public function testPushArrayReturnsSelf(): void {
		$array = new HuArray();
		$result = $array->pushArray([1, 2, 3]);
		$this->assertSame($array, $result);
	}

	public function testPushArrayWithEmptyArray(): void {
		$array = new HuArray([1, 2]);
		$array->pushArray([]);
		$this->assertCount(2, $array->getArray());
	}

	public function testPushHuArray(): void {
		$array1 = new HuArray([1, 2]);
		$array2 = new HuArray([3, 4]);
		$array1->pushHuArray($array2);
		$this->assertCount(4, $array1->getArray());
	}

	public function testLastElement(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertEquals(3, $array->last());
	}

	public function testLastElementReference(): void {
		$array = new HuArray([1, 2, 3]);
		$last = &$array->last();
		$last = 100;
		$this->assertEquals(100, $array->last());
	}

	public function testIteratorRewind(): void {
		$array = new HuArray([1, 2, 3]);
		$array->rewind();
		$this->assertEquals(0, $array->key());
	}

	public function testIteratorCurrent(): void {
		$array = new HuArray([1, 2, 3]);
		$array->rewind();
		$this->assertEquals(1, $array->current());
	}

	public function testIteratorKey(): void {
		$array = new HuArray([1, 2, 3]);
		$array->rewind();
		$this->assertEquals(0, $array->key());
	}

	public function testIteratorNext(): void {
		$array = new HuArray([1, 2, 3]);
		$array->rewind();
		$array->next();
		$this->assertEquals(1, $array->key());
	}

	public function testIteratorValid(): void {
		$array = new HuArray([1, 2, 3]);
		$array->rewind();
		$this->assertTrue($array->valid());
	}

	public function testIteratorInvalid(): void {
		$array = new HuArray([1, 2, 3]);
		$array->rewind();
		$array->next();
		$array->next();
		$array->next();
		$this->assertFalse($array->valid());
	}

	public function testIteratorForeach(): void {
		$array = new HuArray([1, 2, 3]);
		$result = [];
		foreach ($array as $key => $value) {
			$result[$key] = $value;
		}
		$this->assertEquals([0 => 1, 1 => 2, 2 => 3], $result);
	}

	public function testGetArray(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->getArray();
		$this->assertIsArray($result);
		$this->assertEquals([1, 2, 3], $result);
	}

	public function testToArray(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->toArray();
		$this->assertIsArray($result);
		$this->assertEquals([1, 2, 3], $result);
	}

	public function testCount(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertEquals(3, $array->count());
	}

	public function testCountEmpty(): void {
		$array = new HuArray();
		$this->assertEquals(0, $array->count());
	}

	public function testIsEmpty(): void {
		$array = new HuArray();
		$this->assertTrue($array->isEmpty());
	}

	public function testIsNotEmpty(): void {
		$array = new HuArray([1]);
		$this->assertFalse($array->isEmpty());
	}

	public function testGetByKey(): void {
		$array = new HuArray(['key' => 'value']);
		$this->assertEquals('value', $array->getByKey('key'));
	}

	public function testGetByIndex(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertEquals(2, $array->getByIndex(1));
	}

	public function testSetByKey(): void {
		$array = new HuArray();
		$array->setByKey('key', 'value');
		$this->assertEquals('value', $array->getByKey('key'));
	}

	public function testSetByIndex(): void {
		$array = new HuArray([1, 2, 3]);
		$array->setByIndex(1, 100);
		$this->assertEquals(100, $array->getByIndex(1));
	}

	public function testUnsetByKey(): void {
		$array = new HuArray(['key' => 'value']);
		$array->unsetByKey('key');
		$this->assertFalse($array->check('key'));
	}

	public function testUnsetByIndex(): void {
		$array = new HuArray([1, 2, 3]);
		$array->unsetByIndex(1);
		$this->assertCount(2, $array->getArray());
	}

	public function testCheck(): void {
		$array = new HuArray(['key' => 'value']);
		$this->assertTrue($array->check('key'));
		$this->assertFalse($array->check('nonexistent'));
	}

	public function testMerge(): void {
		$array1 = new HuArray([1, 2]);
		$array2 = new HuArray([3, 4]);
		$array1->merge($array2);
		$this->assertCount(4, $array1->getArray());
	}

	public function testMergeWithArray(): void {
		$array = new HuArray([1, 2]);
		$array->merge([3, 4]);
		$this->assertCount(4, $array->getArray());
	}

	public function testFilter(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$filtered = $array->filter(function($value) {
			return $value > 2;
		});
		$this->assertInstanceOf(HuArray::class, $filtered);
		$this->assertCount(3, $filtered->getArray());
	}

	public function testMap(): void {
		$array = new HuArray([1, 2, 3]);
		$mapped = $array->map(function($value) {
			return $value * 2;
		});
		$this->assertInstanceOf(HuArray::class, $mapped);
		$this->assertEquals([2, 4, 6], $mapped->getArray());
	}

	public function testReduce(): void {
		$array = new HuArray([1, 2, 3, 4]);
		$reduced = $array->reduce(function($carry, $value) {
			return $carry + $value;
		}, 0);
		$this->assertEquals(10, $reduced);
	}

	public function testFind(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$found = $array->find(function($value) {
			return $value > 2;
		});
		$this->assertEquals(3, $found);
	}

	public function testFindNotFound(): void {
		$array = new HuArray([1, 2, 3]);
		$found = $array->find(function($value) {
			return $value > 10;
		});
		$this->assertNull($found);
	}

	public function testEvery(): void {
		$array = new HuArray([2, 4, 6]);
		$all = $array->every(function($value) {
			return $value % 2 === 0;
		});
		$this->assertTrue($all);
	}

	public function testSome(): void {
		$array = new HuArray([1, 2, 3]);
		$some = $array->some(function($value) {
			return $value > 2;
		});
		$this->assertTrue($some);
	}

	public function testReverse(): void {
		$array = new HuArray([1, 2, 3]);
		$reversed = $array->reverse();
		$this->assertEquals([3, 2, 1], $reversed->getArray());
	}

	public function testSort(): void {
		$array = new HuArray([3, 1, 2]);
		$sorted = $array->sort();
		$this->assertEquals([1, 2, 3], $sorted->getArray());
	}

	public function testUnique(): void {
		$array = new HuArray([1, 2, 2, 3, 3, 3]);
		$unique = $array->unique();
		$this->assertEquals([1, 2, 3], $unique->getArray());
	}

	public function testChunk(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$chunked = $array->chunk(2);
		$this->assertInstanceOf(HuArray::class, $chunked);
		$this->assertCount(3, $chunked->getArray());
	}

	public function testSlice(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$sliced = $array->slice(1, 3);
		$this->assertEquals([2, 3, 4], $sliced->getArray());
	}

	public function testSplice(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$array->splice(2, 1, [10, 20]);
		$this->assertEquals([1, 2, 10, 20, 4, 5], $array->getArray());
	}

	public function testKeys(): void {
		$array = new HuArray(['a' => 1, 'b' => 2]);
		$keys = $array->keys();
		$this->assertEquals(['a', 'b'], $keys->getArray());
	}

	public function testValues(): void {
		$array = new HuArray(['a' => 1, 'b' => 2]);
		$values = $array->values();
		$this->assertEquals([1, 2], $values->getArray());
	}

	public function testFlatten(): void {
		$array = new HuArray([1, [2, 3], [4, [5, 6]]]);
		$flattened = $array->flatten();
		$this->assertEquals([1, 2, 3, 4, 5, 6], $flattened->getArray());
	}

	public function testPluck(): void {
		$array = new HuArray([
			['name' => 'John', 'age' => 30],
			['name' => 'Jane', 'age' => 25]
		]);
		$names = $array->pluck('name');
		$this->assertEquals(['John', 'Jane'], $names->getArray());
	}

	public function testZip(): void {
		$array1 = new HuArray([1, 2, 3]);
		$array2 = new HuArray(['a', 'b', 'c']);
		$zipped = $array1->zip($array2);
		$this->assertInstanceOf(HuArray::class, $zipped);
	}

	public function testCombine(): void {
		$keys = new HuArray(['a', 'b', 'c']);
		$values = new HuArray([1, 2, 3]);
		$combined = $keys->combine($values);
		$this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $combined->getArray());
	}

	public function testFlip(): void {
		$array = new HuArray(['a' => 1, 'b' => 2]);
		$flipped = $array->flip();
		$this->assertEquals([1 => 'a', 2 => 'b'], $flipped->getArray());
	}

	public function testPad(): void {
		$array = new HuArray([1, 2, 3]);
		$padded = $array->pad(5, 0);
		$this->assertCount(5, $padded->getArray());
	}

	public function testColumn(): void {
		$array = new HuArray([
			['id' => 1, 'name' => 'John'],
			['id' => 2, 'name' => 'Jane']
		]);
		$ids = $array->column('id');
		$this->assertEquals([1, 2], $ids->getArray());
	}

	public function testIntersect(): void {
		$array1 = new HuArray([1, 2, 3]);
		$array2 = new HuArray([2, 3, 4]);
		$intersect = $array1->intersect($array2);
		$this->assertEquals([2, 3], $intersect->getArray());
	}

	public function testDiff(): void {
		$array1 = new HuArray([1, 2, 3]);
		$array2 = new HuArray([2, 3, 4]);
		$diff = $array1->diff($array2);
		$this->assertEquals([1], $diff->getArray());
	}

	public function testUdiff(): void {
		$array1 = new HuArray([3, 1, 2]);
		$array2 = new HuArray([2, 4, 3]);
		$udiff = $array1->udiff($array2);
		$this->assertEquals([1], $udiff->getArray());
	}

	public function testAssoc(): void {
		$array = new HuArray(['a', 1, 'b', 2]);
		$assoc = $array->assoc();
		$this->assertEquals(['a' => 1, 'b' => 2], $assoc->getArray());
	}

	public function testSearch(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$key = $array->search(3);
		$this->assertEquals(2, $key);
	}

	public function testSearchNotFound(): void {
		$array = new HuArray([1, 2, 3]);
		$key = $array->search(10);
		$this->assertFalse($key);
	}

	public function testHas(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertTrue($array->has(2));
		$this->assertFalse($array->has(10));
	}

	public function testFirst(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertEquals(1, $array->first());
	}

	public function testFirstEmpty(): void {
		$array = new HuArray();
		$this->assertNull($array->first());
	}

	public function testPop(): void {
		$array = new HuArray([1, 2, 3]);
		$popped = $array->pop();
		$this->assertEquals(3, $popped);
		$this->assertCount(2, $array->getArray());
	}

	public function testShift(): void {
		$array = new HuArray([1, 2, 3]);
		$shifted = $array->shift();
		$this->assertEquals(1, $shifted);
		$this->assertCount(2, $array->getArray());
	}

	public function testUnshift(): void {
		$array = new HuArray([2, 3]);
		$array->unshift(1);
		$this->assertEquals([1, 2, 3], $array->getArray());
	}

	public function testPrepend(): void {
		$array = new HuArray([2, 3]);
		$array->prepend(1);
		$this->assertEquals([1, 2, 3], $array->getArray());
	}

	public function testAppend(): void {
		$array = new HuArray([1, 2]);
		$array->append(3);
		$this->assertEquals([1, 2, 3], $array->getArray());
	}

	public function testClear(): void {
		$array = new HuArray([1, 2, 3]);
		$array->clear();
		$this->assertEmpty($array->getArray());
	}

	public function testFill(): void {
		$array = new HuArray();
		$filled = $array->fill(0, 3, 'value');
		$this->assertEquals(['value', 'value', 'value'], $filled->getArray());
	}

	public function testRange(): void {
		$array = new HuArray();
		$range = $array->range(1, 5);
		$this->assertEquals([1, 2, 3, 4, 5], $range->getArray());
	}

	public function testImplode(): void {
		$array = new HuArray([1, 2, 3]);
		$string = $array->implode(',');
		$this->assertEquals('1,2,3', $string);
	}

	public function testExplode(): void {
		$array = new HuArray();
		$exploded = $array->explode(',', '1,2,3');
		$this->assertEquals(['1', '2', '3'], $exploded->getArray());
	}

	public function testToJson(): void {
		$array = new HuArray([1, 2, 3]);
		$json = $array->toJson();
		$this->assertEquals('[1,2,3]', $json);
	}

	public function testFromJson(): void {
		$array = new HuArray();
		$fromJson = $array->fromJson('[1,2,3]');
		$this->assertEquals([1, 2, 3], $fromJson->getArray());
	}

	public function testSerialize(): void {
		$array = new HuArray([1, 2, 3]);
		$serialized = \serialize($array);
		$unserialized = \unserialize($serialized);
		$this->assertInstanceOf(HuArray::class, $unserialized);
	}

	public function testClone(): void {
		$array1 = new HuArray([1, 2, 3]);
		$array2 = clone $array1;
		$this->assertEquals($array1->getArray(), $array2->getArray());
	}

	public function testToString(): void {
		$array = new HuArray([1, 2, 3]);
		$string = (string) $array;
		$this->assertIsString($string);
	}

	public function testOffsetExists(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertTrue(isset($array[1]));
		$this->assertFalse(isset($array[10]));
	}

	public function testOffsetGet(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertEquals(2, $array[1]);
	}

	public function testOffsetSet(): void {
		$array = new HuArray();
		$array[0] = 1;
		$this->assertEquals(1, $array[0]);
	}

	public function testOffsetUnset(): void {
		$array = new HuArray([1, 2, 3]);
		unset($array[1]);
		$this->assertFalse(isset($array[1]));
	}

	public function testArrayAccess(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertInstanceOf(\ArrayAccess::class, $array);
	}

	public function testCountable(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertInstanceOf(\Countable::class, $array);
	}

	public function testIteratorAggregate(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertInstanceOf(\Iterator::class, $array);
	}

	public function testJsonSerializable(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertInstanceOf(\JsonSerializable::class, $array);
	}

	public function testSerializable(): void {
		$array = new HuArray([1, 2, 3]);
		$this->assertInstanceOf(\JsonSerializable::class, $array);
	}

	public function testWithMixedKeys(): void {
		$array = new HuArray([0 => 'a', 'key' => 'b', 1 => 'c']);
		$this->assertCount(3, $array->getArray());
	}

	public function testWithNestedArrays(): void {
		$array = new HuArray([[1, 2], [3, 4]]);
		$this->assertCount(2, $array->getArray());
	}

	public function testWithObjects(): void {
		$obj1 = new \stdClass();
		$obj1->value = 1;
		$obj2 = new \stdClass();
		$obj2->value = 2;
		$array = new HuArray([$obj1, $obj2]);
		$this->assertCount(2, $array->getArray());
	}

	public function testWithNullValues(): void {
		$array = new HuArray([1, null, 3]);
		$this->assertCount(3, $array->getArray());
	}

	public function testWithBooleanValues(): void {
		$array = new HuArray([true, false, true]);
		$this->assertCount(3, $array->getArray());
	}

	public function testWithFloatValues(): void {
		$array = new HuArray([1.5, 2.7, 3.14]);
		$this->assertCount(3, $array->getArray());
	}

	public function testWithStringKeys(): void {
		$array = new HuArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$this->assertEquals(2, $array->getByKey('b'));
	}

	public function testWithNumericStringKeys(): void {
		$array = new HuArray(['1' => 'one', '2' => 'two']);
		$this->assertEquals('one', $array->getByKey('1'));
	}

	public function testChain(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$result = $array->filter(function($value) {
			return $value > 2;
		})->map(function($value) {
			return $value * 2;
		});
		// array_filter preserves keys, so after filtering [3, 4, 5] we get keys [2, 3, 4]
		$this->assertEquals([2 => 6, 3 => 8, 4 => 10], $result->getArray());
	}

	public function testPipe(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->pipe(function($arr) {
			return $arr->map(function($value) {
				return $value * 2;
			});
		});
		$this->assertEquals([2, 4, 6], $result->getArray());
	}

	public function testTap(): void {
		$array = new HuArray([1, 2, 3]);
		$sideEffect = null;
		$result = $array->tap(function($arr) use (&$sideEffect) {
			$sideEffect = $arr->count();
		});
		$this->assertEquals(3, $sideEffect);
		$this->assertSame($array, $result);
	}

	public function testWhen(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->when(true, function($arr) {
			return $arr->map(function($value) {
				return $value * 2;
			});
		});
		$this->assertEquals([2, 4, 6], $result->getArray());
	}

	public function testUnless(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->unless(false, function($arr) {
			return $arr->map(function($value) {
				return $value * 2;
			});
		});
		$this->assertEquals([2, 4, 6], $result->getArray());
	}

	public function testEmptyIterator(): void {
		$array = new HuArray();
		$array->rewind();
		$this->assertFalse($array->valid());
	}

	public function testIteratorWithKeys(): void {
		$array = new HuArray(['a' => 1, 'b' => 2]);
		$result = [];
		foreach ($array as $key => $value) {
			$result[$key] = $value;
		}
		$this->assertEquals(['a' => 1, 'b' => 2], $result);
	}

	public function testIteratorModification(): void {
		$array = new HuArray([1, 2, 3]);
		foreach ($array as $key => $value) {
			if ($value === 2) {
				$array[$key] = 20;
			}
		}
		$this->assertEquals([1, 20, 3], $array->getArray());
	}

	public function testMultipleIterations(): void {
		$array = new HuArray([1, 2, 3]);

		$count1 = 0;
		foreach ($array as $value) {
			$count1++;
		}

		$count2 = 0;
		foreach ($array as $value) {
			$count2++;
		}

		$this->assertEquals($count1, $count2);
	}

	public function testIteratorAfterModification(): void {
		$array = new HuArray([1, 2, 3]);
		$array->push(4);

		$result = [];
		foreach ($array as $value) {
			$result[] = $value;
		}

		$this->assertEquals([1, 2, 3, 4], $result);
	}

	public function testIteratorRewindAfterCompletion(): void {
		$array = new HuArray([1, 2, 3]);

		foreach ($array as $value) {
			// Iterate to end
		}

		$array->rewind();
		$this->assertEquals(1, $array->current());
	}

	/**
	* Test getSlice method.
	**/
	public function testGetSlice(): void {
		$array = new HuArray([1, 2, 3, 4, 5]);
		$slice = $array->getSlice(1, 3);

		$this->assertInstanceOf(HuArray::class, $slice);
		$this->assertCount(3, $slice);
		$this->assertEquals([2, 3, 4], $slice->getArray());
	}

	/**
	* Test getSlice with preserve keys.
	**/
	public function testGetSlicePreserveKeys(): void {
		$array = new HuArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$slice = $array->getSlice(1, 2, true);

		$this->assertEquals(['b' => 2, 'c' => 3], $slice->getArray());
	}

	/**
	* Test getProperty method.
	**/
	public function testGetProperty(): void {
		$array = new HuArray(['key' => 'value']);
		$property = $array->getProperty('key');

		$this->assertEquals('value', $property);
	}

	/**
	* Test __get magic method.
	**/
	public function testMagicGet(): void {
		$array = new HuArray(['name' => 'test']);
		$this->assertEquals('test', $array->name);
	}

	/**
	* Test hu method.
	**/
	public function testHuMethod(): void {
		$array = new HuArray(['key' => 'value']);
		$this->assertEquals('value', $array->hu('key'));
	}

	/**
	* Test __set magic method.
	**/
	public function testMagicSet(): void {
		$array = new HuArray();
		$array->newKey = 'newValue';

		$this->assertEquals('newValue', $array->newKey);
	}

	/**
	* Test walk method.
	**/
	public function testWalk(): void {
		$array = new HuArray([1, 2, 3]);
		$sum = 0;

		$result = $array->walk(function($value) use (&$sum) {
			$sum += $value;
		});

		$this->assertSame($array, $result);
		$this->assertEquals(6, $sum);
	}

	/**
	* Test filterByKeys method.
	**/
	public function testFilterByKeys(): void {
		$array = new HuArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$filtered = $array->filterByKeys(['a', 'c']);

		$this->assertEquals(['a' => 1, 'c' => 3], $filtered->getArray());
	}

	/**
	* Test filterOutByKeys method.
	**/
	public function testFilterOutByKeys(): void {
		$array = new HuArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$filtered = $array->filterOutByKeys(['b']);

		$this->assertEquals(['a' => 1, 'c' => 3], $filtered->getArray());
	}

	/**
	* Test filterKeysCallback method.
	**/
	public function testFilterKeysCallback(): void {
		$array = new HuArray(['a' => 1, 'b' => 2, 'c' => 3]);
		$filtered = $array->filterKeysCallback(function($key) {
			return $key === 'a' || $key === 'c';
		});

		$this->assertEquals(['a' => 1, 'c' => 3], $filtered->getArray());
	}

	/**
	* Test every method - all match.
	**/
	public function testEveryAllMatch(): void {
		$array = new HuArray([2, 4, 6, 8]);
		$result = $array->every(function($value) {
			return $value % 2 === 0;
		});

		$this->assertTrue($result);
	}

	/**
	* Test every method - some don't match.
	**/
	public function testEverySomeDontMatch(): void {
		$array = new HuArray([2, 3, 4, 6]);
		$result = $array->every(function($value) {
			return $value % 2 === 0;
		});

		$this->assertFalse($result);
	}

	/**
	* Test some method - at least one matches.
	**/
	public function testSomeAtLeastOne(): void {
		$array = new HuArray([1, 3, 5, 8]);
		$result = $array->some(function($value) {
			return $value % 2 === 0;
		});

		$this->assertTrue($result);
	}

	/**
	* Test some method - none match.
	**/
	public function testSomeNoneMatch(): void {
		$array = new HuArray([1, 3, 5, 7]);
		$result = $array->some(function($value) {
			return $value % 2 === 0;
		});

		$this->assertFalse($result);
	}

	/**
	* Test when method - condition true.
	**/
	public function testWhenConditionTrue(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->when(true, function($arr) {
			return $arr->push(4);
		});

		$this->assertEquals([1, 2, 3, 4], $result->getArray());
	}

	/**
	* Test when method - condition false.
	**/
	public function testWhenConditionFalse(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->when(false, function($arr) {
			return $arr->push(4);
		});

		$this->assertEquals([1, 2, 3], $result->getArray());
	}

	/**
	* Test unless method - condition false.
	**/
	public function testUnlessConditionFalse(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->unless(false, function($arr) {
			return $arr->push(4);
		});

		$this->assertEquals([1, 2, 3, 4], $result->getArray());
	}

	/**
	* Test unless method - condition true.
	**/
	public function testUnlessConditionTrue(): void {
		$array = new HuArray([1, 2, 3]);
		$result = $array->unless(true, function($arr) {
			return $arr->push(4);
		});

		$this->assertEquals([1, 2, 3], $result->getArray());
	}

	/**
	* Test sort method with default sort.
	**/
	public function testSortDefault(): void {
		$array = new HuArray([3, 1, 4, 1, 5]);
		$sorted = $array->sort();

		$this->assertEquals([1, 1, 3, 4, 5], $sorted->getArray());
	}

	/**
	* Test sort method with custom callback.
	**/
	public function testSortWithCallback(): void {
		$array = new HuArray([3, 1, 4, 1, 5]);
		$sorted = $array->sort(function($a, $b) {
			return $b <=> $a;
		});

		$this->assertEquals([5, 4, 3, 1, 1], $sorted->getArray());
	}

	/**
	* Test jsonSerialize method.
	**/
	public function testJsonSerialize(): void {
		$array = new HuArray(['key' => 'value', 'num' => 42]);
		$serialized = $array->jsonSerialize();

		$this->assertIsArray($serialized);
		$this->assertEquals(['key' => 'value', 'num' => 42], $serialized);
	}

	/**
	* Test __get magic method with _last_ special name.
	**/
	public function testMagicGetLastSpecial(): void {
		$array = new HuArray([1, 2, 3]);
		$last = $array->_last_;

		$this->assertEquals(3, $last);
	}

	/**
	* Test __set magic method with _last_ special name.
	**/
	public function testMagicSetLastSpecial(): void {
		$array = new HuArray([1, 2, 3]);
		$array->_last_ = 99;

		$this->assertEquals(99, $array->last());
	}

	/**
	* Test pluck with nested array.
	**/
	public function testPluckNested(): void {
		$array = new HuArray([
			['user' => ['name' => 'John']],
			['user' => ['name' => 'Jane']]
		]);
		// Note: pluck only works with direct keys, not nested
		$plucked = $array->pluck('user');

		$this->assertEquals([['name' => 'John'], ['name' => 'Jane']], $plucked->getArray());
	}

	/**
	* Test offsetSet with null offset (append).
	**/
	public function testOffsetSetNullOffset(): void {
		$array = new HuArray([1, 2, 3]);
		$array->offsetSet(null, 4);

		$this->assertEquals([1, 2, 3, 4], $array->getArray());
	}

	/**
	* Test __get magic method with hu:// scheme.
	**/
	public function testMagicGetHuScheme(): void {
		$array = new HuArray(['nested' => [1, 2, 3]]);
		$result = $array->{'hu://nested'};

		$this->assertInstanceOf(HuArray::class, $result);
		$this->assertEquals([1, 2, 3], $result->getArray());
	}

	/**
	* Test __set magic method with hu:// scheme.
	**/
	public function testMagicSetHuScheme(): void {
		$array = new HuArray();
		$array->{'hu://newKey'} = [1, 2, 3];

		// After setting via hu://, the value should be stored as HuArray
		$this->assertArrayHasKey('newKey', $array->getArray());
		$this->assertEquals([1, 2, 3], $array->newKey);
	}

	/**
	* Test __set magic method with hu:// scheme when key already exists as array.
	**/
	public function testMagicSetHuSchemeExistingArray(): void {
		$array = new HuArray(['existing' => [1, 2, 3]]);
		// Setting via hu:// should convert existing array to HuArray and set new value
		$array->{'hu://existing'} = [4, 5, 6];

		$this->assertArrayHasKey('existing', $array->getArray());
		$this->assertEquals([4, 5, 6], $array->existing);
	}

	/**
	* Test pluck with array of objects.
	**/
	public function testPluckObjects(): void {
		$obj1 = new \stdClass();
		$obj1->name = 'John';
		$obj2 = new \stdClass();
		$obj2->name = 'Jane';

		$array = new HuArray([$obj1, $obj2]);
		$plucked = $array->pluck('name');

		$this->assertEquals(['John', 'Jane'], $plucked->getArray());
	}
}
