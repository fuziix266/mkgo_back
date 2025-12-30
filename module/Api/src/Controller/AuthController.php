<?php

namespace Api\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Adapter\Adapter;

class AuthController extends AbstractActionController
{
    private $dbAdapter;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new JsonModel(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            return new JsonModel(['status' => 'error', 'message' => 'Missing credentials']);
        }

        $sql = "SELECT * FROM users WHERE email = ?";
        $statement = $this->dbAdapter->createStatement($sql, [$email]);
        $result = $statement->execute();

        if ($result->valid()) {
            $user = $result->current();
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Remove password from response
                unset($user['password']);
                return new JsonModel(['status' => 'success', 'user' => $user]);
            }
        }

        return new JsonModel(['status' => 'error', 'message' => 'Invalid credentials']);
    }

    public function registerAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new JsonModel(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $fullName = $data['full_name'] ?? '';

        if (empty($email) || empty($password) || empty($fullName)) {
             return new JsonModel(['status' => 'error', 'message' => 'Missing fields']);
        }

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)";
        try {
            $statement = $this->dbAdapter->createStatement($sql, [$email, $passwordHash, $fullName]);
            $statement->execute();
            return new JsonModel(['status' => 'success', 'message' => 'User registered']);
        } catch (\Exception $e) {
            return new JsonModel(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
