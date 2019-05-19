<?php
/**
 * Created by PhpStorm.
 * User: Omar
 * Date: 19/05/2019
 * Time: 11:25
 */

namespace src\Config;

/**
 * Class Db
 * @package src\Config
 */
class Db

{
    /**
     * COnfiguration for the connection with the database
     *
     * @return array
     */
    public static function dbConfig()
    {
        return [

            'host' => 'localhost',


            'database' => 'phone_book',


            'username' => 'root',


            'password' => 'toor',

        ];
    }
}




