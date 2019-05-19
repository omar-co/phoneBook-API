<?php
/**
 * Created by PhpStorm.
 * User: Omar
 * Date: 19/05/2019
 * Time: 12:06
 */

namespace Db;


/**
 * Class EmailModel
 * @package Db
 */
class EmailModel extends BaseDb
{
    /**
     * @var string
     */
    protected $table = 'email';

    /**
     * Saves email data
     *
     * @param int $user_id
     * @param string $email
     * @return mixed
     */
    public function Save($user_id, $email)
    {
        $email = htmlspecialchars($email);
        $sql = $this->db->query("INSERT INTO email (user_id, email) VALUES ({$user_id}, '{$email}')");
        return $sql;
    }

    /**
     * Update email data
     *
     * @param int $email_id
     * @param string $email
     * @return mixed
     */
    public function Update($email_id, $email)
    {
        $email = htmlspecialchars($email);
        $sql = $this->db->query("UPDATE email SET email='{$email}' WHERE email_id={$email_id}");
        return $sql;
    }
}