<?php

use PHPUnit\Framework\TestCase;
use ElvisLeite\RecordSetDatabase\Connection;

class ConnectionTest extends TestCase
{
    public function testConnection()
    {
        // Positive test case
        $connection = new Connection('localhost', 'root', '', 'mvc', 'utf8');

        $this->assertInstanceOf(Connection::class, $connection);
        $this->assertNotNull($connection->getConnection());
        $this->assertIsObject($connection->getConnection());
        $this->assertIsObject($connection->getConnection());

        // Negative test case       
        $connection = new Connection('name', 'root', '', 'mvc', 'utf8');
        // $this->assertIsObject($connection);
        // $this->assertNull($connection->getConnection());        
    }
}
