<?php
/**
 * Created by PhpStorm.
 * User: Omar
 * Date: 19/05/2019
 * Time: 12:06
 */

namespace Db;


/**
 *
 *
 * Class PhoneModel
 * @package Db
 */
class PhoneModel extends BaseDb
{
    /**
     * @var string
     */
    protected $table = 'phone';


    /**
     * Saves phones
     *
     * @param $user_id
     * @param $phone
     * @return mixed
     */
    public function Save($user_id, $phone)
    {
        $phone = htmlspecialchars($phone);
        $sql = $this->db->query("INSERT INTO phone (user_id, phone) VALUES ({$user_id}, '{$phone}')");
        return $sql;
    }

    /**
     * Updates Phones
     *
     * @param $phoneId
     * @param $phone
     * @return mixed
     */
    public function Update($phoneId, $phone)
    {
        $phone = htmlspecialchars($phone);
        $sql = $this->db->query("UPDATE phone SET phone='{$phone}' WHERE phone_id={$phoneId}");
        return $sql;
    }
}