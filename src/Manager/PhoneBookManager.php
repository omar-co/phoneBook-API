<?php

namespace src\Manager;

use Db\EmailModel;
use Db\PhoneBook;
use Db\PhoneModel;
use Db\UserModel;

/**
 * Class PhoneBookManager
 * @package src\Manager
 */
class PhoneBookManager
{
    /**
     * @var PhoneBook
     */
    private $model;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var PhoneModel
     */
    private $phoneModel;

    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * PhoneBookManager constructor.
     */
    public function __construct()
    {
        $this->model = new PhoneBook();
        $this->userModel = new UserModel();
        $this->phoneModel = new PhoneModel();
        $this->emailModel = new EmailModel();
    }

    /**
     * Gets all the contact with the contact information (email & phones)
     */
    public static function All()
    {
        $these = (new self);

        $users = $these->userModel->All();
        $emails = $these->emailModel->All();
        $phones = $these->phoneModel->All();

        $these->arrangeUserValues($users, $emails, $phones);
    }

    /**
     * Saves the contact, can be simple, complex or multi
     *
     * @param array $data contains all the information to be saved
     */
    public static function Save($data)
    {
        $these = (new self);
        $result = ['success' => true];
        $saved = 0;
        try {
            if (isset($data['contact'])) {
                foreach ($data['contact'] as $key => $contact) {
                    $isValid = $these->verifyData($contact);
                    if ($isValid) {
                        $userId = $these->userModel->Save($contact['Name'], $contact['LastName']);
                        $these->saveEmails($userId, $contact);
                        $these->savePhones($userId, $contact);
                        $saved++;
                    } else {
                        $result['error'] = 'Malformed Request';
                    }
                }
                $result['message'] = $saved . ' Contacts saved successfully!';
            } else {
                http_response_code(400);
                $result['message'] = 'Malformed Request';
            }

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);
    }

    /**
     * Get specific contact information with user id, name, last name, phones & emails.
     *
     * @param int $id
     */
    public static function Get($id)
    {
        $these = (new self);

        $users = $these->userModel->Get($id);
        $emails = $these->emailModel->Get($id);
        $phones = $these->phoneModel->Get($id);

        $these->arrangeUserValues($users, $emails, $phones);
    }

    /**
     * Update user based on user id
     *
     * @param int $id
     * @param array $data
     */
    public static function UpdateUser($id, $data)
    {
        $these = (new self);
        $result = ['success' => true];
        $user = current($data['user']);

        try {
            if ($these->userModel->Get($id)) {
                $these->userModel->Update($id, $user['Name'], $user['LastName']);
                $result['message'] ='User updated successfully!';
            } else {
                http_response_code(400);
                $result['message'] = 'User Dont Exist';
            }

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);

    }

    /**
     * Update email based on email id
     *
     * @param int $id
     * @param array $data
     */
    public static function UpdateEmail($id, $data)
    {
        $these = (new self);
        $result = ['success' => true];
        $email = current($data['email']);

        try {
            if ($these->emailModel->Get($id)) {
                $these->emailModel->Update($id, $email['Email']);
                $result['message'] ='Email updated successfully!';
            } else {
                http_response_code(400);
                $result['message'] = 'Email Dont Exist';
            }

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);

    }

    /**
     * Update phone based on phone id
     *
     * @param int $id
     * @param array $data
     */
    public static function UpdatePhone($id, $data)
    {
        $these = (new self);
        $result = ['success' => true];
        $phone = current($data['phone']);

        try {
            if ($these->phoneModel->Get($id)) {
                $these->phoneModel->Update($id, $phone['Phone']);
                $result['message'] ='Phone updated successfully!';
            } else {
                http_response_code(400);
                $result['message'] = 'Phone Dont Exist';
            }

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);

    }

    /**
     * Delete full contact information
     *
     * @param int $id
     */
    public static function DeleteUser($id)
    {
        $these = (new self);
        $result = ['success' => true];
        try {
            if ($these->userModel->GetById($id)) {
                $these->phoneModel->DeleteByUserId($id);
                $these->emailModel->DeleteByUserId($id);
                $these->userModel->Delete($id);
                $result['message'] ='Contact & contact information deleted successfully!';
            } else {
                http_response_code(400);
                $result['message'] = 'Contact Dont Exist';
            }

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);

    }

    /**
     * Delete email based on email id
     *
     * @param int $id
     */
    public static function DeleteEmail($id)
    {
        $these = (new self);
        $result = ['success' => true];
        try {
            if ($these->emailModel->GetById($id)) {
                $these->emailModel->Delete($id);
                $result['message'] ='Email deleted successfully!';
            } else {
                http_response_code(400);
                $result['message'] = 'Email Dont Exist';
            }

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);

    }

    /**
     * Delete phone based on phone id
     *
     * @param int $id
     */
    public static function DeletePhone($id)
    {
        $these = (new self);
        $result = ['success' => true];
        try {
            if ($these->phoneModel->GetById($id)) {
                $these->phoneModel->Delete($id);
                $result['message'] ='Phone deleted successfully!';
            } else {
                http_response_code(400);
                $result['message'] = 'Phone Dont Exist';
            }

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);

    }

    /**
     * Arrange the data to be displayed in correct format
     *
     * used by All() & Get()
     *
     * @param array $users
     * @param array $emails
     * @param array $phones
     */
    private function arrangeUserValues($users, $emails, $phones)
    {
        $result = ['success' => true];
        try {
            foreach ($users as $user) {
                $result['message'][$user['user_id']] = [
                    'user_id' => $user['user_id'],
                    'name' => $user['name'],
                    'last_name' => $user['last_name'],
                    'phones' => $this->getDataUser('phone', $user['user_id'], $phones),
                    'emails' => $this->getDataUser('email', $user['user_id'], $emails),
                ];
            }
            $result['message'] = array_values($result['message']) ?: 'Without Results.';

        } catch (\Exception $e) {
            http_response_code(403);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode($result);

    }

    /**
     * Arrange on the correct format the data for current contact
     *
     * used indirectly by All() & Get()
     *
     * @param string $dataName
     * @param int $user_id
     * @param array $data
     * @return array
     */
    private function getDataUser($dataName, $user_id, &$data)
    {
        $result = [];
        foreach ($data as $key => $datum) {
            if ($datum['user_id'] === $user_id) {
                $result[] = [
                    $dataName . '_id' => $datum[$dataName . '_id'],
                    $dataName => $datum[$dataName]
                ];
                unset($data[$key]);
            }
        }
        return $result;
    }

    /**
     * Verifies that the data in te body call be ok
     *
     * @param array $contact
     * @return bool
     */
    private function verifyData($contact)
    {
        return
            array_key_exists('Name', $contact) &&
            array_key_exists('LastName', $contact) &&
            array_key_exists('Phones', $contact) &&
            array_key_exists('Emails', $contact);
    }

    /**
     * Save email based on user id
     *
     * @param int $userId
     * @param array $data
     */
    private function saveEmails($userId, $data)
    {
        if (is_array($data['Emails'])) {
            foreach ($data['Emails'] as $email) {
                $this->emailModel->Save($userId, $email);
            }
        } else {
            $this->emailModel->Save($userId, $data['Emails']);
        }
    }

    /**
     * Save Phone based on user id
     *
     * @param int $userId
     * @param array $data
     */
    private function savePhones($userId, $data)
    {
        if (is_array($data['Phones'])) {
            foreach ($data['Phones'] as $email) {
                $this->phoneModel->Save($userId, $email);
            }
        } else {
            $this->phoneModel->Save($userId, $data['Phones']);
        }
    }
}