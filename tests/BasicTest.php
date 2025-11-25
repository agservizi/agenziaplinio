<?php

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testConfigLoads()
    {
        $config = require __DIR__ . '/../includes/config.php';
        $this->assertIsArray($config);
        $this->assertArrayHasKey('site', $config);
    }

    public function testDatabaseConnection()
    {
        require __DIR__ . '/../includes/env.php';
        require __DIR__ . '/../includes/database.php';
        $pdo = ap_db();
        $this->assertInstanceOf(PDO::class, $pdo);
        $stmt = $pdo->query('SELECT 1');
        $this->assertEquals(1, $stmt->fetchColumn());
    }
}