<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilter;
use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;
use Hubbitus\HuPHP\Vars\Settings\Filters\SettingsFilterIgnore;
use Hubbitus\HuPHP\Vars\Settings\Filters\SettingsFilterDefault;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Vars\Settings\SettingsFilter
**/
class SettingsFilterTest extends TestCase {
    private $settings;

    protected function setUp(): void {
        $this->settings = new SettingsFilter(['testProp', 'anotherProp', 'prop1', 'prop2', 'existing', 'new', '*']);
    }

    public function testSetSettingAppliesSetFilters(): void {
        $callback = function(&$name, &$value) {
            $value = strtoupper($value);
        };
        $filter = new SettingsFilterBase('testProp', $callback);

        $this->settings->addFilterSet($filter);
        $this->settings->setSetting('testProp', 'hello');

        $this->assertEquals('HELLO', $this->settings->getProperty('testProp'));
    }

    public function testGetPropertyAppliesGetFilters(): void {
        $callback = function(&$name, &$value) {
            $value = $value . '_suffix';
        };
        $filter = new SettingsFilterBase('testProp', $callback);

        $this->settings->setSetting('testProp', 'original');
        $this->settings->addFilterGet($filter);

        $value = $this->settings->getProperty('testProp');

        $this->assertEquals('original_suffix', $value);
    }

    public function testSetSettingsArrayAppliesFilters(): void {
        $callback = function(&$name, &$value) {
            $value = strtoupper($value);
        };
        $filter1 = new SettingsFilterBase('prop1', $callback);
        $filter2 = new SettingsFilterBase('prop2', $callback);

        $this->settings->addFilterSet($filter1);
        $this->settings->addFilterSet($filter2);
        $this->settings->setSettingsArray(['prop1' => 'value1', 'prop2' => 'value2']);

        $this->assertEquals('VALUE1', $this->settings->getProperty('prop1'));
        $this->assertEquals('VALUE2', $this->settings->getProperty('prop2'));
    }

    public function testMergeSettingsArrayAppliesFilters(): void {
        $callback = function(&$name, &$value) {
            $value = $value . '_merged';
        };
        $filter1 = new SettingsFilterBase('existing', $callback);
        $filter2 = new SettingsFilterBase('new', $callback);

        $this->settings->setSetting('existing', 'old');
        $this->settings->addFilterSet($filter1);
        $this->settings->addFilterSet($filter2);
        $this->settings->mergeSettingsArray(['new' => 'value', 'existing' => 'updated']);

        $this->assertEquals('updated_merged', $this->settings->getProperty('existing'));
        $this->assertEquals('value_merged', $this->settings->getProperty('new'));
    }

    public function testAddFilterGetReturnsFilterId(): void {
        $callback = function(&$name, &$value) {};
        $filter = new SettingsFilterBase('testProp', $callback);

        $filterId = $this->settings->addFilterGet($filter);

        $this->assertIsInt($filterId);
        $this->assertEquals(0, $filterId);
    }

    public function testAddFilterSetReturnsFilterId(): void {
        $callback = function(&$name, &$value) {};
        $filter = new SettingsFilterBase('testProp', $callback);

        $filterId = $this->settings->addFilterSet($filter);

        $this->assertIsInt($filterId);
        $this->assertEquals(0, $filterId);
    }

    public function testMultipleFiltersForSameProperty(): void {
        $filter1 = new SettingsFilterBase('testProp', function(&$name, &$value) {
            $value = strtoupper($value);
        });
        $filter2 = new SettingsFilterBase('testProp', function(&$name, &$value) {
            $value = $value . '_processed';
        });

        $this->settings->addFilterSet($filter1);
        $this->settings->addFilterSet($filter2);
        $this->settings->setSetting('testProp', 'hello');

        $this->assertEquals('HELLO_processed', $this->settings->getProperty('testProp'));
    }

    public function testDelFilterGetRemovesFilter(): void {
        $callback = function(&$name, &$value) {
            $value = $value . '_should_be_removed';
        };
        $filter = new SettingsFilterBase('testProp', $callback);

        $this->settings->setSetting('testProp', 'original');
        $this->settings->addFilterGet($filter);
        $filterId = $this->settings->addFilterGet(new SettingsFilterBase('testProp', function(&$name, &$value) {
            $value = $value . '_keep';
        }));

        $this->settings->delFilterGet('testProp', $filterId);

        $value = $this->settings->getProperty('testProp');

        $this->assertEquals('original_should_be_removed', $value);
    }

    public function testDelFilterSetRemovesFilter(): void {
        $callback = function(&$name, &$value) {
            $value = strtoupper($value);
        };
        $filter = new SettingsFilterBase('testProp', $callback);

        $filterId = $this->settings->addFilterSet($filter);
        $this->settings->delFilterSet('testProp', $filterId);
        $this->settings->setSetting('testProp', 'hello');

        $this->assertEquals('hello', $this->settings->getProperty('testProp'));
    }

    public function testGetFiltersForDifferentProperties(): void {
        $filter1 = new SettingsFilterBase('prop1', function(&$name, &$value) {
            $value = 'prop1_' . $value;
        });
        $filter2 = new SettingsFilterBase('prop2', function(&$name, &$value) {
            $value = 'prop2_' . $value;
        });

        $this->settings->addFilterGet($filter1);
        $this->settings->addFilterGet($filter2);

        $this->settings->setSetting('prop1', 'value1');
        $this->settings->setSetting('prop2', 'value2');

        $this->assertEquals('prop1_value1', $this->settings->getProperty('prop1'));
        $this->assertEquals('prop2_value2', $this->settings->getProperty('prop2'));
    }

    public function testWithDefaultFilter(): void {
        $defaultFilter = new SettingsFilterDefault('testProp', 'default_value');
        $this->settings->addFilterGet($defaultFilter);

        // Get non-existent property (will throw, but filter should apply if it exists)
        $this->settings->setSetting('testProp', '');

        $value = $this->settings->getProperty('testProp');

        $this->assertEquals('default_value', $value);
    }

    public function testWithIgnoreFilter(): void {
        $ignoreFilter = new SettingsFilterIgnore('testProp');
        $this->settings->addFilterGet($ignoreFilter);

        $this->settings->setSetting('testProp', 'value');

        $value = $this->settings->getProperty('testProp');

        $this->assertNull($value);
    }
}
