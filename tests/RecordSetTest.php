<?php

use PHPUnit\Framework\TestCase;
use ElvisLeite\RecordSetDatabase\RecordSet;

class RecordSetTest extends TestCase
{
    public function testexecuteWithValidSQL()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user";
        $dbConect->execute($sql);

        $this->assertNotNull($dbConect->getDataGenerator());
    }

    public function testexecuteWithEmptySQL()
    {
        $dbConect = new RecordSet();
        $sql = "";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SQL query cannot be empty');
        $dbConect->execute($sql);
    }

    public function testexecuteWithInvalidSQL()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM non_existing_table";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(mysqli_error($dbConect->execute($sql)));
        $dbConect->execute($sql);
    }

    public function testexecuteWithInvalidSQLField()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user data = 1";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(mysqli_error($dbConect->execute($sql)));
        $dbConect->execute($sql);
    }
    public function testexecuteWithInvalidValue()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user id= ss";

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(mysqli_error($dbConect->execute($sql)));
        $dbConect->execute($sql);
    }

    public function testgetDataGeneratorWithValidSQL()
    {
        $dbConect = new RecordSet();
        $sql = "SELECT * FROM user";
        $dbConect->execute($sql);

        $this->assertIsArray($dbConect->getDataGenerator());
        $this->assertNotNull($dbConect->getDataGenerator());
    }
    public function testgetDataGeneratorWithInvalidSQL()
    {
        $dbConect = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Table 'mvc.users' doesn't exist");

        $sql = "SELECT * FROM users";
        $dbConect->execute($sql);

        $this->assertIsArray($dbConect->getDataGenerator());
        $this->assertNotNull($dbConect->getDataGenerator());
    }

    public function testgetDataGeneratorWithSelectValidSQL()
    {
        $dbConect = new RecordSet();
        $dbConect->select('user', 'id <> 0', 'name ASC');

        $this->assertIsArray($dbConect->getDataGenerator());
        $this->assertNotNull($dbConect->getDataGenerator());
    }
    public function testgetDataGeneratorWithSelectInvalidSQLTable()
    {
        $dbConect = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Table 'mvc.users' doesn't exist");

        $dbConect->select('users', 'id <> 0', 'names ASC', '10');
        $dbConect->getDataGenerator();
    }

    public function testgetDataGeneratorWithSelectInvalidSQLField()
    {
        $rs = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unknown column 'snames' in 'order clause'");

        $rs->select('user', 'id <> 0', 'snames ASC', '10');
        $rs->getDataGenerator();
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
        $this->assertIsArray($rs->getDataGenerator());
    }

    public function testSelectWhithAllParams()
    {
        $rs = new RecordSet();
        $rs->select("user", "id > 0 ", "name ASC", "100");

        $this->assertIsArray($rs->getDataGenerator());
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

    // public function testSelectWhithInvalidValue()
    // {
    //     $rs = new RecordSet();
    //     $this->expectException(Exception::class);
    //     // $this->expectExceptionMessage("Record does not exist.");
    //     $rs->select("user", "id < 0 ", "name ASC", "100");
    //     // $this->expectNotToPerformAssertions();
    // }

    public function testFld()
    {
        $rs = new RecordSet();
        $rs->select("user");
        $rs->getDataGenerator();
        $this->assertIsString($rs->fld('id'));
    }

    public function testFormatedFld()
    {
        $rs = new RecordSet();
        $rs->select("depoimentos");
        $rs->getDataGenerator();
        $this->assertIsString($rs->formatFld('data'));
    }

    public function testFormatedFldReturnsFormattedTimeDate()
    {
        $expected = "27/10/2023 às 18:43:29";

        $rs = new RecordSet();
        $rs->select("depoimentos", "id = 37", "data ASC", "1");
        $rs->getDataGenerator();
        $actual = $rs->formatFld("data");

        $this->assertEquals($actual, $expected);
    }

    public function testFormatedFldReturnsFormattedMonth()
    {
        $expected = "27 de Outubro de 2023";

        $rs = new RecordSet();
        $rs->select("depoimentos", "id = 37", "data ASC", "1");
        $rs->getDataGenerator();
        $actual = $rs->formatMonthField("data");

        $this->assertEquals($actual, $expected);
    }

    public function testFormatedFldReturnsFormattedMonthWhithHour()
    {
        $expected = "27 de Outubro de 2023 às 18:43";

        $rs = new RecordSet();
        $rs->select("depoimentos", "id = 37", "data ASC", "1");
        $rs->getDataGenerator();
        $actual = $rs->formatMonthWhithHourField("data");

        $this->assertEquals($actual, $expected);
    }

    public function testInsert()
    {
        $recordSet = new RecordSet();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Duplicate entry 'cabrita@gmail.com' for key 'mail");
        $recordSet->insert([
            'id' => $recordSet->setAutoCode('id', 'user'),
            'name' => 'Camily',
            'mail' => "cabrita@gmail.com",
            'pass' => '112312321'
        ], 'user');
        $email = $recordSet->getField('mail', 'user', "mail = 'cabrita@gmail.com'");
        $recordSet->getDataGenerator();
        $mail = 'cabrita@gmail.com';
        $this->assertEquals($mail, $email);
    }

    public function testInsertWithEmptyTableName()
    {
        $recordSet = new RecordSet();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid table or values');
        $recordSet->insert(['name' => 'Camily'], '');
    }

    public function testInsertWithInvalidValues()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Insert operation failed: Unknown column 'code' in 'field list'");

        $recordSet = new RecordSet();
        $recordSet->insert(['code' => '12s'], 'user');
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

        $this->assertTrue($recordSet->insert($data, 'user'));
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

        $this->assertTrue($recordSet->insert($data, 'table_name'));
    }
    
    public function testUpdate()
    {
        $recordSet = new RecordSet();
        $fields = ['name' => 'Camily'];
        $table = 'user';
        $where = 'id = 2';
        $recordSet->update($fields, $table, $where);

        $sql = "SELECT * FROM $table WHERE $where";
        $recordSet->execute($sql);
        $recordSet->getDataGenerator();
        $updatedRecord = $recordSet->fld('name');
        $this->assertEquals('Camily', $updatedRecord);
    }
    
    public function testUpdateWithInvalidSQLTableName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Update operation failed: Table 'mvc.users' doesn't exist");

        $recordSet = new RecordSet();
        $recordSet->update(['name' => 'Camily'], 'users', 'id = 70');
    }

    public function testUpdateWithEmptyTableName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Update operation failed: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'SET name = ? WHERE id = 1' at line 1");

        $recordSet = new RecordSet();
        $recordSet->update(['name' => 'Camily'], '', 'id = 1');
    }

    public function testDeleteWithEmptyTableName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Table name cannot be empty.');

        $recordSet = new RecordSet();
        $recordSet->delete('', '');
    }

    public function testDeleteWithEmptyConditions()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Conditions array cannot be empty.');

        $recordSet = new RecordSet();
        $recordSet->delete('user', '');
    }

    public function testDelete()
    {
        $recordSet = new RecordSet();
        $data = [
            'id' => (string) $recordSet->setAutoCode('id', 'user'),
            'name' => 'Jordan',
            'mail' => 'jordan@gmail.com',
            'pass' => 's$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
        ];
        
        $recordSet->insert($data, "user");
        
        $table = 'user';
        $conditions = "mail = 'jordan@gmail.com'";        
        $recordSet->delete($table, $conditions);

        $sql = "SELECT * FROM $table WHERE mail = 'peter@gmail.com'";
        $recordSet->execute($sql);
        $recordSet->getDataGenerator();
        $this->assertNull($recordSet->fld('name'));
    }
        
    public function testDeleteWithInvalidCondition()
    {
        $recordSet = new RecordSet();
        $table = 'user';
        $conditions = "id = 100";
        
        $recordSet->delete($table, $conditions);
        $sql = "SELECT * FROM $table WHERE id = 100";
        $recordSet->execute($sql);
        $recordSet->getDataGenerator();

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

    public function testSetAutoCode()
    {
        $database = new RecordSet();
        $numUser = $database->setAutoCode('id', 'user');
        $database->insert([
            'id' => $database->setAutoCode('id', 'user'),
            'name' => 'Juarez',
            'mail' => 'juarez@gmail.com',
            'pass' => 's$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/C'
        ], 'user');
        
        $num = $database->getField("id", 'user', "id = $numUser" );        
        $this->assertEquals($num,$numUser);
        $database->delete("user", "id = $num");
    }
    public function testSetAutoCodeWithInvalidColumm()
    {
        $database = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Database error: Database error: Unknown column 'ids' in 'field list'");

        $database->insert([
            'id' => (string) $database->setAutoCode('ids', 'user'),
            'name' => 'Elvis'
        ], 'user');
    }
    
    public function testSetAutoCodeWithEmptyTable()
    {
        $database = new RecordSet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid fieldname, tablename, or whereClause");

        $database->insert([
            'id' => (string) $database->setAutoCode('id', ''),
            'name' => 'Elvis'
        ], 'user');
    }
}
