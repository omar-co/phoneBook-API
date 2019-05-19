<?php
/**
 * Created by PhpStorm.
 * User: Omar
 * Date: 19/05/2019
 * Time: 12:00
 */

namespace Db;


/**
 * Class UserModel
 * @package Db
 */
class UserModel extends BaseDb
{
    /**
     * @var string
     */
    protected $table = 'user';


    /**
     * Saves user data
     *
     * @param string $name
     * @param string $lastName
     * @return mixed
     */
    public function Save($name, $lastName)
    {
        $name = htmlspecialchars($name);
        $lastName = htmlspecialchars($lastName);
        $sql = $this->db->query("INSERT INTO user (name, last_name) VALUES ('{$name}', '{$lastName}')");
        return $sql;
    }


    /**
     * Update user data
     *
     * @param int $userId
     * @param string $name
     * @param string $lastName
     * @return mixed
     */
    public function Update($userId, $name, $lastName)
    {
        $name = htmlspecialchars($name);
        $lastName = htmlspecialchars($lastName);
        $sql = $this->db->query("UPDATE user SET name='{$name}', last_name='{$lastName}' WHERE user_id={$userId}");
        return $sql;
    }
}