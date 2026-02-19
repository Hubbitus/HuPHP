<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Exceptions\SerializeException;
use Hubbitus\HuPHP\Vars\SerializedData;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\SerializedData
 */
class SerializedDataTest extends TestCase
{
    public function testConstructorCreatesEmptyContainer(): void
    {
        $data = new SerializedData();

        $this->assertIsObject($data);
    }

    public function testConstructorWithValidSerializedString(): void
    {
        $original = ['key1' => 'value1', 'key2' => 'value2'];
        $serialized = \serialize($original);

        $data = new SerializedData($serialized);

        $this->assertEquals('value1', $data->key1);
        $this->assertEquals('value2', $data->key2);
    }

    public function testConstructorThrowsExceptionForInvalidSerializedString(): void
    {
        $this->expectException(SerializeException::class);

        $invalidSerialized = 'not_a_valid_serialized_string';
        new SerializedData($invalidSerialized);
    }

    public function testMagicGetReturnsPropertyValue(): void
    {
        $original = ['test_key' => 'test_value'];
        $serialized = \serialize($original);

        $data = new SerializedData($serialized);
        $value = $data->test_key;

        $this->assertEquals('test_value', $value);
    }

    public function testMagicSetAddsProperty(): void
    {
        $data = new SerializedData();
        $data->new_key = 'new_value';

        $this->assertEquals('new_value', $data->new_key);
    }

    public function testMagicSetUpdatesExistingProperty(): void
    {
        $original = ['existing_key' => 'old_value'];
        $serialized = \serialize($original);

        $data = new SerializedData($serialized);
        $data->existing_key = 'new_value';

        $this->assertEquals('new_value', $data->existing_key);
    }

    public function testToStringReturnsSerializedData(): void
    {
        $original = ['key1' => 'value1', 'key2' => 'value2'];
        $serialized = \serialize($original);

        $data = new SerializedData($serialized);
        $result = (string) $data;

        $this->assertEquals($serialized, $result);
    }

    public function testToStringMethodReturnsSerializedData(): void
    {
        $original = ['key1' => 'value1', 'key2' => 'value2'];
        $serialized = \serialize($original);

        $data = new SerializedData($serialized);
        $result = $data->toString();

        $this->assertEquals($serialized, $result);
    }

    public function testRoundTripSerialization(): void
    {
        $original = ['key1' => 'value1', 'key2' => 'value2', 'nested' => ['subkey' => 'subvalue']];
        $serialized = \serialize($original);

        $data = new SerializedData($serialized);
        $reserialized = (string) $data;
        $restored = \unserialize($reserialized);

        $this->assertEquals($original, $restored);
    }

    public function testMagicSetAndToStringCombined(): void
    {
        $data = new SerializedData();
        $data->key1 = 'value1';
        $data->key2 = 'value2';

        $serialized = (string) $data;
        $restored = \unserialize($serialized);

        $this->assertEquals('value1', $restored['key1']);
        $this->assertEquals('value2', $restored['key2']);
    }
}
