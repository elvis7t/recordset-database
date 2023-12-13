<?php

use PHPUnit\Framework\TestCase;
use ElvisLeite\RecordSetDatabase\RecordSet;

class RecordSetTest extends TestCase
{
    public function testExecuteWithValidSQL()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user";
        $dbConect->execute($sql);

        $this->assertNotNull($dbConect->getDatagenerator());
    }

    public function testExecuteWithEmptySQL()
    {
        $dbConect = new RecordSet();
        $sql = "";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SQL query cannot be empty');
        $dbConect->execute($sql);
    }

    public function testExecuteWithInvalidSQL()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM non_existing_table";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(mysqli_error($dbConect->execute($sql)));
        $dbConect->execute($sql);
    }

    public function testExecuteWithInvalidSQLField()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user data = 1";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(mysqli_error($dbConect->execute($sql)));
        $dbConect->execute($sql);
    }
    public function testExecuteWithInvalidValue()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user id= ss";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(mysqli_error($dbConect->execute($sql)));
        $dbConect->execute($sql);
    }

    public function testDataGeneratorWithValidSQL()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user";
        $dbConect->execute($sql);

        $this->assertIsArray($dbConect->getDatagenerator());
        $this->assertNotNull($dbConect->getDatagenerator());
    }
    public function testDataGeneratorWithInvalidSQL()
    {
        $dbConect = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Table 'mvc.users' doesn't exist");

        $sql = "SELECT * FROM users";
        $dbConect->execute($sql);

        $this->assertIsArray($dbConect->getDatagenerator());
        $this->assertNotNull($dbConect->getDatagenerator());
    }

    public function testDataGeneratorWithSelectValidSQL()
    {
        $dbConect = new RecordSet();
        $dbConect->Select('user', 'id <> 0', 'name ASC');

        $this->assertIsArray($dbConect->getDatagenerator());
        $this->assertNotNull($dbConect->getDatagenerator());
    }
    public function testDataGeneratorWithSelectInvalidSQLTable()
    {
        $dbConect = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Table 'mvc.users' doesn't exist");

        $dbConect->Select('users', 'id <> 0', 'names ASC', '10');
        $dbConect->getDatagenerator();
    }

    public function testDataGeneratorWithSelectInvalidSQLField()
    {
        $rs = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown column 'snames' in 'order clause'");

        $rs->Select('user', 'id <> 0', 'snames ASC', '10');
        $rs->getDatagenerator();
    }

    public function testGetCountRowsReturnsCorrectNumberOfRows()
    {
        $rs = new RecordSet();
        $sql = 'SELECT * FROM user';
        $numberOfRows = $rs->getCountRows($sql);

        $this->assertNotNull($numberOfRows);
        $this->assertIsInt($numberOfRows);
    }

    public function testGetCountRowsReturnsInvalisSQL()
    {
        $rs = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Table 'mvc.users' doesn't exist");
        $sql = 'SELECT * FROM users';
        $numberOfRows = $rs->getCountRows($sql);

        $this->assertNotNull($numberOfRows);
        $this->assertIsInt($numberOfRows);
    }

    public function testSelectTableName()
    {
        $rs = new RecordSet();
        $rs->select("user");
        $this->assertIsArray($rs->getDatagenerator());
    }

    public function testSelectWhithAllParams()
    {
        $rs = new RecordSet();
        $rs->select("user", "id > 0 ", "name ASC", "100");

        $this->assertIsArray($rs->getDatagenerator());
    }
    public function testSelectWhithInvalidSQLTableName()
    {
        $rs = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Table 'mvc.usessr' doesn't exist");
        $rs->select("usessr", "id > 0 ", "name ASC", "100");
        $this->expectNotToPerformAssertions();
    }
    public function testSelectWhithInvalidSQLFieldName()
    {
        $rs = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown column 'ids' in 'where clause'");
        $rs->select("user", "ids > 0 ", "name ASC", "100");
        $this->expectNotToPerformAssertions();
    }

    public function testSelectWhithInvalidValue()
    {
        $rs = new RecordSet();
        
        try {
            $rs->select("user", "id < 0 ", "name ASC", "100");   
            
            $this->fail('Expected an Exception to be thrown');
        } catch (Exception $e) {            
            $this->assertEquals("Expected an Exception to be thrown", $e->getMessage());
        }        
    }

    public function testFld()
    {
        $rs = new RecordSet();
        $rs->select("user");
        $rs->getDatagenerator();
        $this->assertIsString($rs->fld('id'));
    }

    public function testFormatedFld()
    {
        $rs = new RecordSet();
        $rs->select("depoimentos");
        $rs->getDatagenerator();
        $this->assertIsString($rs->formatFld('data'));
    }

    public function testFormatedFldReturnsFormattedTimeDate()
    {
        $expected = "30/09/2023 às 15:21:13";

        $rs = new RecordSet();
        $rs->select("depoimentos", "id = 1", "data ASC", "1");
        $rs->getDatagenerator();
        $actual = $rs->formatFld("data");

        $this->assertEquals($actual, $expected);
    }

    public function testFormatedFldReturnsFormattedMonth()
    {
        $expected = "30 de Setembro de 2023";

        $rs = new RecordSet();
        $rs->select("depoimentos", "id = 1", "data ASC", "1");
        $rs->getDatagenerator();
        $actual = $rs->formatMonthField("data");

        $this->assertEquals($actual, $expected);
    }

    public function testFormatedFldReturnsFormattedMonthWhithHour()
    {
        $expected = "03 de Setembro de 2023 às 09:59";

        $rs = new RecordSet();
        $rs->select("depoimentos", "id = 3", "data ASC", "1");
        $rs->getDatagenerator();
        $actual = $rs->formatMonthWhithHourField("data");

        $this->assertEquals($actual, $expected);
    }

    public function testInsert()
    {
        $recordSet = new RecordSet();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Duplicate entry 'cabrita@gmail.com' for key 'mail");
        $recordSet->Insert([
            'name' => 'Camily',
            'mail' => "cabrita@gmail.com",
            'pass' => '112312321'
        ], 'user');
        $email = $recordSet->getField('mail', 'user', "mail = 'cabrita@gmail.com'");
        $recordSet->getDatagenerator();
        $mail = 'cabrita@gmail.com';
        $this->assertEquals($mail, $email);
    }

    public function testInsertWithEmptyTableName()
    {
        $recordSet = new RecordSet();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid table or values');
        $recordSet->Insert(['name' => 'Camily'], '');
    }

    public function testInsertWithInvalidValues()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Insert operation failed: Unknown column 'code' in 'field list'");

        $recordSet = new RecordSet();
        $recordSet->Insert(['code' => '12s'], 'user');
    }

    public function testInsertWithValidDataDuplicate()
    {
        $recordSet = new RecordSet();
        $data = [
            'id' => $recordSet->setAutoCode('id', 'user'),
            'name' => 'Camily',
            'mail' => 'camily@gmail.com',
            'pass' => '$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
        ];
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Insert operation failed: Duplicate entry 'camily@gmail.com' for key 'mail'");

        $this->assertTrue($recordSet->Insert($data, 'user'));
    }
    public function testInsertWithInValidTableName()
    {
        $recordSet = new RecordSet();
        $data = [
            'id' => $recordSet->setAutoCode('id', 'user'),
            'name' => 'Camily',
            'mail' => 'camily@gmail.com',
            'pass' => '$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
        ];
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Insert operation failed: Duplicate entry 'camily@gmail.com' for key 'mail'");
        $this->expectExceptionMessage("Insert operation failed: Table 'mvc.table_name' doesn't exist");

        $this->assertTrue($recordSet->Insert($data, 'table_name'));
    }
    
    public function testUpdate()
    {
        $recordSet = new RecordSet();
        $fields = ['name' => 'Camily'];
        $table = 'user';
        $where = 'id = 2';
        $recordSet->Update($fields, $table, $where);

        $sql = "SELECT * FROM $table WHERE $where";
        $recordSet->execute($sql);
        $recordSet->getDatagenerator();
        $updatedRecord = $recordSet->fld('name');
        $this->assertEquals('Camily', $updatedRecord);
    }
    
    public function testUpdateWithInvalidSQLTableName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Update operation failed: Table 'mvc.users' doesn't exist");

        $recordSet = new RecordSet();
        $recordSet->Update(['name' => 'Camily'], 'users', 'id = 70');
    }

    public function testUpdateWithEmptyTableName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Update operation failed: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'SET name = ? WHERE id = 1");

        $recordSet = new RecordSet();
        $recordSet->Update(['name' => 'Camily'], '', 'id = 1');
    }

    public function testDeleteWithEmptyTableName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Table name cannot be empty.');

        $recordSet = new RecordSet();
        $recordSet->Delete('', '');
    }

    public function testDeleteWithEmptyConditions()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Conditions array cannot be empty.');

        $recordSet = new RecordSet();
        $recordSet->Delete('user', '');
    }

    public function testDelete()
    {
        $recordSet = new RecordSet();
        $data = [
            'id' => (string) $recordSet->setAutoCode('id', 'user'),
            'name' => 'Peter',
            'mail' => 'peter@gmail.com',
            'pass' => 's$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
        ];
        
        $recordSet->Insert($data, "user");
        
        $table = 'user';
        $conditions = "mail = 'peter@gmail.com'";
        // $this->expectException(Exception::class);
        // $this->expectExceptionMessage("Delete operation failed");
        $recordSet->Delete($table, $conditions);

        $sql = "SELECT * FROM $table WHERE mail = 'peter@gmail.com'";
        $recordSet->execute($sql);
        $recordSet->getDatagenerator();
        $this->assertNull($recordSet->fld('name'));
    }
        
    public function testDeleteWithInvalidCondition()
    {
        $recordSet = new RecordSet();
        $table = 'user';
        $conditions = "id = 100";
        // $this->expectException(Exception::class);
        // $this->expectExceptionMessage("Record does not exist");

        $recordSet->Delete($table, $conditions);
        $sql = "SELECT * FROM $table WHERE id = 100";
        $recordSet->execute($sql);
        $recordSet->getDatagenerator();

        $this->assertNull($recordSet->fld('name'));
    }

    public function testReturnsExpectedValueWhenDataExistsInTable()
    {
        $database = new RecordSet();
        $expectedValue = 'João da Silva';
        $actualValue = $database->getField('name', 'user', 'id = 6');

        $this->assertEquals($expectedValue, $actualValue);
        $this->assertNotNull($actualValue);
    }

    public function testReturnsNullWhenInvalidSQLFieldName()
    {
        $database = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Table 'mvc.clientes' doesn't exist");
        $actualValue = $database->getField('nome', 'clientes', 'id = 999');
        $this->assertNull($actualValue);
    }

    public function testReturnsNullWhenFieldDoesNotExistInTable()
    {
        $database = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown column 'endereco' in 'field list'");
        $actualValue = $database->getField('endereco', 'user', 'id = 1');

        $this->assertNull($actualValue);
    }
    public function testReturnsNullWhenFieldDoesNotExistInTableName()
    {
        $database = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown column 'endereco' in 'field list'");
        $actualValue = $database->getField('endereco', 'user', 'id = 111');

        $this->assertNull($actualValue);
    }

    public function testSetAutoCodeWithEmptyTable()
    {
        $database = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Database error: Unknown column 'isd' in 'field list'");

        $database->insert([
            'id' => (string) $database->setAutoCode('isd', 'user'),
            'name' => 'Elvis'
        ], 'user');
    }
}
