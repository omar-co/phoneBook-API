<?php


namespace Db;


/**
 * Not used
 *
 * Class PhoneBook
 * @package Db
 */
class PhoneBook extends BaseDb
{
    /**
     * @var string
     */
    public $table = 'contact';

    /**
     * @param $userId
     * @return mixed
     */
    public function userData($userId)
    {
        $sql = $this->db->query("select c.user_id as id, c.name, c.last_name, e.email, p.phone
 from user c
join email e on c.user_id = e.user_id
join phone p on c.user_id = p.user_id
where c.user_id = {$userId} 
group by c.user_id");

        return $sql;
    }


}