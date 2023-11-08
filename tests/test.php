<?php

use ElvisLeite\RecordSetDatabase\RecordSet;
use ElvisLeite\RecordSetDatabase\Connection;

require '../vendor/autoload.php';
$testConnection = false;
$testInsert = false;
$testAutocod = true;
$testUpdate = false;
$testDelete = false;
$testSelect = false;
$testSelecGetField = false;

if ($testInsert) {
    $rs = new RecordSet();
    $data = [
        'id' => (string) $rs->setAutoCode('id', 'user'),
        'name' => 'Tatys',
        'mail' => 'tatiss@gmail.com',
        'pass' => 's$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
    ];

    try {
        $rs->Insert($data, "users");
        echo 'Inserção bem-sucedida!';
    } catch (Exception $e) {
        echo 'Ocorreu um erro: ' . $e->getMessage();
    }
}
if ($testAutocod) {
    $rs = new RecordSet();    
    $numUser = $rs->setAutoCode('id', 'user');        
    
    $data = [
        'id' => $rs->setAutoCode('id', 'user'),
        'name' => 'Maria',
        'mail' => 'maria@gmail.com',
        'pass' => 's$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
    ];

    try {
        $rs->Insert($data, "user");
        $num = $rs->getField("id", 'user', "id = $numUser" );
        if($num == $numUser){
            echo 'Inserção bem-sucedida!';
        }
    } catch (Exception $e) {
        echo 'Ocorreu um erro: ' . $e->getMessage();
    }
}
if ($testUpdate) {
    $rs = new RecordSet();
    $data = [
        'name' => 'Tatys',
        'mail' => 'tatiss@gmail.com',
        // 'mail' => 'camily@gmail.com; DROP TABLE user;'
        'pass' => 's$2y$10$zKdjHmKbmJ6GVOIrApOiTO5sOpZSZkbHiscY9Kab/CnsKF.2dVt3S'
    ];
    try {
        $rs->Update($data, "user", "id = 5");
        echo 'Atualização bem-sucedida!';
    } catch (Exception $e) {
        echo 'Ocorreu um erro: ' . $e->getMessage();
    }
}
if ($testDelete) {
    $rs = new RecordSet();
    try {
        $rs->Delete('user', 'id = 4');
        echo 'Delete bem-sucedida!';
    } catch (Exception $e) {
        echo 'Ocorreu um erro: ' . $e->getMessage();
    }
}
if ($testSelect) {
    $rs = new RecordSet();
    try {
        // $rs->Select('user', 'ids > 0', 'name desc', '1');
        $rs->Select('user', 'id <> 0', 'name ASC','10');
        while ($rs->getDataGenerator()) {
    ?>
            <tr>
                <td><?= $rs->fld('id') ?></td>
            </tr>
    <?php
        }
        echo 'Dados encontrados bem-sucedida!';
    } catch (Exception $e) {
        echo 'Ocorreu um erro: ' . $e->getMessage();
    }
}
if ($testSelecGetField) {
    $rs = new RecordSet();
    try {
        echo $rs->getField('id', 'user', 'id = ');

        echo 'Inserção bem-sucedida!';
    } catch (Exception $e) {
        echo 'Ocorreu um erro: ' . $e->getMessage();
    }
}
if($testConnection){
    $connection = new Connection('localhost', 'root', '', 'mvc', 'utf8');
    try {
        $connection->openConnection();
        $connection->getConnection();
        // $connection->closeConnection();
        echo "<pre>";
        var_dump($connection);
    } catch (Exception $e) {
        echo 'Ocorreu um erro: ' . $e->getMessage();
    }
}
