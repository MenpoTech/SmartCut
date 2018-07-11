<?php

class DatabaseConfig
{
    const Live_db_host = "localhost";
    const live_db_port = "3306";
    const live_db_name = "smartcut";
    const live_db_username = "root";
    const live_db_password = "";

    public static function getLiveDbConnection()
    {
        return [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.self::Live_db_host.';port='.self::live_db_port.';dbname='.self::live_db_name.'',
            'username' => self::live_db_username,
            'password' => self::live_db_password,
            'charset' => 'utf8',
        ];
    }
}
