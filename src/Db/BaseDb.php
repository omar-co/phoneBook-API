<?php


namespace Db;

use src\Services\Db;

class BaseDb
{
    protected $table;

    protected $db;

    public function __construct()
    {
        $this->db = new Db('172.20.0.2', 'phone_book', 'root', 'root');
    }

    public static function Alls()
    {
        /** @var Db $db */
        $db = (new self())->table();

        $db->query('select c.id, c.name, c.last_name, e.email, p.phone_number from contact c
join email e on c.id = e.fk_contact
join phone p on c.id = p.fk_contact');
    }
}