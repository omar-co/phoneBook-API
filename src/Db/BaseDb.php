<?php


namespace Db;

use src\Services\Db;

/**
 * Clase Base for CRUD operations (Can be refactored to work better)
 *
 * Class BaseDb
 * @package Db
 */
class BaseDb
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var Db
     */
    protected $db;

    /**
     * BaseDb constructor.
     */
    public function __construct()
    {
        $this->db = new Db();
    }

    /**
     * Get all from instanced model
     *
     * @return mixed
     */
    public function All()
    {
        return $this->db->query("select * from {$this->table}");
    }

    /**
     * Get data form specific user id
     *
     * @param int $id
     * @return mixed
     */
    public function Get($id)
    {
        return $this->db->query("select * from {$this->table} where user_id = {$id}");
    }

    /**
     * Get data dynamically depends on instancied model
     *
     * @param int $id
     * @return mixed
     */
    public function GetById($id)
    {
        $primaryColumn = $this->getidColumnMap()[$this->table];
        return $this->db->query("select * from {$this->table} where {$primaryColumn} = {$id}");
    }

    /**
     * Delete data dynamically depends on instancied model
     *
     * @param int $userId
     * @return mixed
     */
    public function Delete($userId)
    {
        $primaryColumn = $this->getidColumnMap()[$this->table];
        $sql = $this->db->query("DELETE FROM {$this->table} WHERE {$primaryColumn}={$userId}");
        return $sql;
    }

    /**
     * Get data depends on user id
     *
     * @param int $userId
     * @return mixed
     */
    public function DeleteByUserId($userId)
    {
        $sql = $this->db->query("DELETE FROM {$this->table} WHERE user_id={$userId}");
        return $sql;
    }

    /**
     * Used to calculate the primary key column
     *
     * @return array
     */
    private function getidColumnMap()
    {
        return [
            'user' => 'user_id',
            'phone' => 'phone_id',
            'email' => 'email_id'
        ];
    }
}