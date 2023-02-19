<?php

namespace ElvisLeite\RecordSetDatabase;

date_default_timezone_set('America/Sao_paulo');

class Connection
{
    /**
     * Hostname of the connection
     * @const 
     */
    const DB_HOSTNAME = 'localhost';

    /**
     * User of the connection
     * @const 
     */
    const DB_USERNAME = 'root';

    /**
     * Password of the connection
     * @const 
     */
    const DB_PASSWORD = '';

    /**
     * Database of the connection
     * @const 
     */
    const DB_DATABASE = 'test';

    /**
     * Charset of the connection
     * @const 
     */
    const DB_CHARSET = 'utf8';

    /**
     * Link of the connection
     * @var mysqli
     */
    private static $link;

    /**
     * Method responsible for connecting to the database
     * @return mysqli 
     */
    public static function setConnect()
    {
        try {
            self::$link = mysqli_connect(self::DB_HOSTNAME, self::DB_USERNAME, self::DB_PASSWORD, self::DB_DATABASE);
            mysqli_set_charset(self::$link, self::DB_CHARSET);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return self::$link;
    }


    /**
     * *Method responsible for Disconnecting to the database
     * @param string $link
     * @return bool
     */
    public static function setDesconnect(): bool
    {
        return mysqli_close(self::$link);
    }
}
