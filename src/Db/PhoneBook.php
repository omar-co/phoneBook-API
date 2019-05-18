<?php


namespace Db;


class PhoneBook extends BaseDb
{
    public $table = 'contact';

    public function all()
    {
        $sql = $this->db->query('select c.id, c.name, c.last_name, e.email, p.phone_number, p.id as phone_id, e.id as email_id
 from contact c
join email e on c.id = e.fk_contact
join phone p on c.id = p.fk_contact');

        return $sql;
    }


}