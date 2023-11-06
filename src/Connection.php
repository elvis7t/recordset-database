<?php

namespace ElvisLeite\RecordSetDatabase;

use Exception;
use mysqli;

date_default_timezone_set('America/Sao_paulo');

class Connection
{
    private string $host;
    private string $username;
    private string $password;
    private string $database;
    private string $charset;
    private $link;

    public function __construct($host, $username, $password, $database, $charset = 'utf8')
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->charset = $charset;
    }

    public function openConnection(): void
    {
        if ($this->link === null) {
            try {
                $this->link = new mysqli($this->host, $this->username, $this->password, $this->database);
                if ($this->link->connect_error) {
                    throw new Exception($this->link->connect_error, $this->link->connect_errno);
                }
                mysqli_set_charset($this->link, $this->charset);
            } catch (Exception $e) {
                echo "Erro na conexão com o banco de dados: " . $e->getMessage();
            }
        }
    }

    public function closeConnection(): void
    {
        if ($this->link !== null) {
            try {
                mysqli_close($this->link);
                $this->link = null;
            } catch (Exception $e) {
                echo "Erro ao fechar a conexão: " . $e->getMessage();
            }
        }
    }

    public function getConnection(): mixed
    {
        try {
            $this->openConnection();
            return $this->link;
        } catch (Exception $e) {
            echo "Erro ao obter a conexão: " . $e->getMessage();
            return null;
        }
    }    
}
