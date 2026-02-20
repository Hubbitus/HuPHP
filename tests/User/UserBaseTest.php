<?php
declare(strict_types=1);

/**
 * Test for UserBase class.
 */

namespace Hubbitus\HuPHP\Tests\User;

use Hubbitus\HuPHP\User\UserBase;
use Hubbitus\HuPHP\User\UserSettings;
use Hubbitus\HuPHP\User\UserAuthenticateException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\User\UserBase
 * @covers \Hubbitus\HuPHP\User\UserSettings
 * @covers \Hubbitus\HuPHP\User\UserAuthenticateException
 */
class UserBaseTest extends TestCase
{
    public function testUserSettingsInstantiation(): void {
        $settings = new UserSettings();
        $this->assertInstanceOf(UserSettings::class, $settings);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Vars\\Settings\\Settings', $settings);
    }

    public function testUserAuthenticateExceptionInstantiation(): void {
        $exception = new UserAuthenticateException('Test message');
        $this->assertInstanceOf(UserAuthenticateException::class, $exception);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Exceptions\\BaseException', $exception);
    }

    public function testUserAuthenticateExceptionMessage(): void {
        $exception = new UserAuthenticateException('Custom error message');
        $this->assertEquals('Custom error message', $exception->getMessage());
    }

    public function testUserSettingsInheritance(): void {
        $settings = new UserSettings();
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Vars\\Settings\\Settings', $settings);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Vars\\Settings\\SettingsGet', $settings);
    }

    public function testUserSettingsCanSetAndGetValues(): void {
        $settings = new UserSettings();
        $settings->testKey = 'testValue';
        $this->assertEquals('testValue', $settings->testKey);
    }

    public function testUserSettingsMergeSettingsArray(): void {
        $settings = new UserSettings();
        $settings->mergeSettingsArray(['key1' => 'value1', 'key2' => 'value2']);
        $this->assertEquals('value1', $settings->key1);
        $this->assertEquals('value2', $settings->key2);
    }

    public function testUserSettingsToArray(): void {
        $settings = new UserSettings();
        $settings->key1 = 'value1';
        $settings->key2 = 'value2';
        $array = $settings->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('key1', $array);
        $this->assertArrayHasKey('key2', $array);
    }

    public function testUserSettingsCheck(): void {
        $settings = new UserSettings();
        $settings->key1 = 'value1';
        $this->assertTrue($settings->check('key1'));
        $this->assertFalse($settings->check('nonexistent'));
    }

    public function testUserSettingsGetWithDefault(): void {
        $settings = new UserSettings();
        $settings->key1 = 'value1';
        $this->assertEquals('value1', $settings->get('key1'));
        $this->assertEquals('default', $settings->get('nonexistent', 'default'));
    }

    public function testUserSettingsSet(): void {
        $settings = new UserSettings();
        $settings->set('key1', 'value1');
        $this->assertEquals('value1', $settings->key1);
    }

    public function testUserSettingsUnset(): void {
        $settings = new UserSettings();
        $settings->key1 = 'value1';
        $settings->unsetKey('key1');
        $this->assertFalse($settings->check('key1'));
    }

    public function testUserSettingsIsEmpty(): void {
        $settings = new UserSettings();
        $this->assertTrue($settings->isEmpty());
        $settings->key1 = 'value1';
        $this->assertFalse($settings->isEmpty());
    }

    public function testUserSettingsMergeWithAnotherSettings(): void {
        $settings1 = new UserSettings();
        $settings1->key1 = 'value1';

        $settings2 = new UserSettings();
        $settings2->key2 = 'value2';

        $settings1->mergeSettings($settings2);
        $this->assertEquals('value1', $settings1->key1);
        $this->assertEquals('value2', $settings1->key2);
    }

    public function testUserSettingsCount(): void {
        $settings = new UserSettings();
        $this->assertEquals(0, $settings->count());
        $settings->key1 = 'value1';
        $settings->key2 = 'value2';
        $this->assertEquals(2, $settings->count());
    }

    public function testUserSettingsIterator(): void {
        $settings = new UserSettings();
        $settings->key1 = 'value1';
        $settings->key2 = 'value2';

        $this->assertInstanceOf(\Iterator::class, $settings);

        $values = [];
        foreach ($settings as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertArrayHasKey('key1', $values);
        $this->assertArrayHasKey('key2', $values);
    }

    public function testUserSettingsArrayAccess(): void {
        $settings = new UserSettings();
        $settings['key1'] = 'value1';
        $this->assertEquals('value1', $settings['key1']);
        $this->assertTrue(isset($settings['key1']));
        unset($settings['key1']);
        $this->assertFalse(isset($settings['key1']));
    }

    public function testUserSettingsSerializable(): void {
        $settings = new UserSettings();
        $settings->key1 = 'value1';
        $settings->key2 = 'value2';

        $serialized = serialize($settings);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(UserSettings::class, $unserialized);
        $this->assertEquals('value1', $unserialized->key1);
        $this->assertEquals('value2', $unserialized->key2);
    }

    public function testUserAuthenticateExceptionToString(): void {
        $exception = new UserAuthenticateException('Error message');
        $string = (string) $exception;
        $this->assertIsString($string);
    }

    public function testUserAuthenticateExceptionGetCode(): void {
        $exception = new UserAuthenticateException('Error message', 42);
        $this->assertEquals(42, $exception->getCode());
    }

    public function testUserAuthenticateExceptionGetFile(): void {
        $exception = new UserAuthenticateException('Error message');
        $file = $exception->getFile();
        $this->assertIsString($file);
    }

    public function testUserAuthenticateExceptionGetLine(): void {
        $exception = new UserAuthenticateException('Error message');
        $line = $exception->getLine();
        $this->assertIsInt($line);
    }

    public function testUserAuthenticateExceptionGetTrace(): void {
        $exception = new UserAuthenticateException('Error message');
        $trace = $exception->getTrace();
        $this->assertIsArray($trace);
    }

    public function testUserAuthenticateExceptionGetTraceAsString(): void {
        $exception = new UserAuthenticateException('Error message');
        $traceString = $exception->getTraceAsString();
        $this->assertIsString($traceString);
    }

    public function testUserAuthenticateExceptionGetPrevious(): void {
        $previous = new \Exception('Previous exception');
        $exception = new UserAuthenticateException('Error message', 0, $previous);
        $prev = $exception->getPrevious();
        $this->assertInstanceOf(\Throwable::class, $prev);
    }
}
