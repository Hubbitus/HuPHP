<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\Database;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\Database
 */
class DatabaseBaseTest extends TestCase {
    public function testDatabaseClassExists(): void {
        $this->assertTrue(class_exists(Database::class));
    }

    public function testDatabaseIsAbstract(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function testDatabaseHasErrorProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('Error'));
        $errorProperty = $reflection->getProperty('Error');
        $this->assertTrue($errorProperty->isProtected());
    }

    public function testDatabaseHasSettingsProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('_sets'));
        $setsProperty = $reflection->getProperty('_sets');
        $this->assertTrue($setsProperty->isProtected());
    }

    public function testDatabaseHasDbTypeProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertFalse($reflection->hasProperty('db_type'));
    }

    public function testDatabaseHasDbLinkProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('db_link'));
        $dbLinkProperty = $reflection->getProperty('db_link');
        $this->assertTrue($dbLinkProperty->isProtected());
    }

    public function testDatabaseHasQueryProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('Query'));
        $queryProperty = $reflection->getProperty('Query');
        $this->assertTrue($queryProperty->isProtected());
    }

    public function testDatabaseHasResultProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('result'));
        $resultProperty = $reflection->getProperty('result');
        $this->assertTrue($resultProperty->isProtected());
    }

    public function testDatabaseHasResProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('RES'));
        $resProperty = $reflection->getProperty('RES');
        $this->assertTrue($resProperty->isProtected());
    }

    public function testDatabaseHasFieldProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('Field'));
        $fieldProperty = $reflection->getProperty('Field');
        $this->assertTrue($fieldProperty->isPublic());
    }

    public function testDatabaseHasFieldsProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('Fields'));
        $fieldsProperty = $reflection->getProperty('Fields');
        $this->assertTrue($fieldsProperty->isPublic());
    }

    public function testDatabaseHasRowsTotalProperty(): void {
        $reflection = new \ReflectionClass(Database::class);
        $this->assertTrue($reflection->hasProperty('rowsTotal'));
        $rowsTotalProperty = $reflection->getProperty('rowsTotal');
        $this->assertTrue($rowsTotalProperty->isProtected());
    }

    public function testDatabaseHasAbstractMethods(): void {
        $reflection = new \ReflectionClass(Database::class);
        $abstractMethods = [
            'db_connect',
            'query',
            'query_limit',
            'ToBlob',
            'sql_next_result',
            'sql_escape_string'
        ];

        foreach ($abstractMethods as $method) {
            $methodReflection = $reflection->getMethod($method);
            $this->assertTrue($methodReflection->isAbstract(), "Method {$method} should be abstract");
        }
    }

    public function testDatabaseHasConcreteMethods(): void {
        $methods = [
            'sql_fetch_object', 'sql_fetch_array', 'sql_fetch_assoc', 'sql_fetch_row',
            'sql_free_result', 'sql_num_rows', 'sql_num_fields', 'sql_fetch_field',
            'sql_fetch_fields',
            'ping', 'select_db', 'set_names', 'db_select', 'rowsTotal', 'getError',
            'getSQL', 'iconv_result', 'iconv_query', 'collectDebugInfo', '__wakeup'
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists(Database::class, $method), "Method {$method} should exist");
        }
    }

    public function testDatabaseMagicMethods(): void {
        $this->assertTrue(method_exists(Database::class, '__get'));
        $this->assertTrue(method_exists(Database::class, '__wakeup'));
    }

    public function testDatabaseExtendsSettingsGet(): void {
        $reflection = new \ReflectionClass(Database::class);
        $parentClass = $reflection->getParentClass();
        $this->assertNotNull($parentClass);
        $this->assertEquals('Hubbitus\HuPHP\Vars\Settings\SettingsGet', $parentClass->getName());
    }

    public function testDatabaseImplementsIDatabase(): void {
        $reflection = new \ReflectionClass(Database::class);
        $interfaces = $reflection->getInterfaceNames();
        $this->assertContains('Hubbitus\HuPHP\Database\IDatabase', $interfaces);
    }

    public function testDatabaseConstructorParameters(): void {
        $reflection = new \ReflectionClass(Database::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        $this->assertEquals(2, count($parameters));
        
        // Second parameter should be boolean
        $secondParam = $parameters[1];
        $this->assertEquals('bool', $secondParam->getType()->getName());
    }

    public function testDatabaseGetErrorMethod(): void {
        $this->assertIsArray([Database::class, 'getError']);
    }

    public function testDatabaseGetSQLMethod(): void {
        $this->assertIsArray([Database::class, 'getSQL']);
    }

    public function testDatabaseRowsTotalMethod(): void {
        $this->assertIsArray([Database::class, 'rowsTotal']);
    }

    public function testDatabaseIconvResultMethod(): void {
        $reflection = new \ReflectionClass(Database::class);
        $method = $reflection->getMethod('iconv_result');
        $this->assertTrue($method->isProtected());
    }

    public function testDatabaseIconvQueryMethod(): void {
        $reflection = new \ReflectionClass(Database::class);
        $method = $reflection->getMethod('iconv_query');
        $this->assertTrue($method->isProtected());
    }

    public function testDatabaseCollectDebugInfoMethod(): void {
        $reflection = new \ReflectionClass(Database::class);
        $method = $reflection->getMethod('collectDebugInfo');
        $this->assertTrue($method->isProtected());
        $this->assertEquals(4, $method->getNumberOfParameters());
    }
}
