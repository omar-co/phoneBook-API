<?php

namespace src\Manager;

use Db\PhoneBook;

class PhoneBookManager
{
    private $model;

    public function __construct()
    {
        $this->model = new PhoneBook();
    }

    public static function All() {
        $rows = (new self)->model->all();

        return self::arrangeValues($rows);
    }

    private static function arrangeValues($values) {

        $result = [];
        foreach($values as $key => $value){
            $result[$value['id']]['id'] = $value['id'];
            $result[$value['id']]['name'] = $value['name'];
            $result[$value['id']]['last_name'] = $value['last_name'];
            if (isset($result[$value['id']]['phones'])) {
                foreach ($result[$value['id']]['phones'] as $phone) {
                    if ($phone['phone_id'] !== $value['phone_id']) {
                        $result[$value['id']]['phones'][] = ['phone_id' => $value['phone_id'], 'phone' => $value['phone_number']];
                    }
                }
            } else {
                $result[$value['id']]['phones'][] = ['phone_id' => $value['phone_id'], 'phone' => $value['phone_number']];
            }

            if (isset($result[$value['id']]['emails'])) {
                foreach ($result[$value['id']]['emails'] as $email) {
                    if ($email['email_id'] !== $value['email_id']) {
                        $result[$value['id']]['emails'][] = ['email_id' => $value['email_id'], 'email' => $value['email']];
                    }
                }
            } else {
                $result[$value['id']]['emails'][] = ['email_id' => $value['email_id'], 'email' => $value['email']];
            }
        }
        return array_values($result);
    }
}