<?php
class AdminService {
    private $adminCredentials = [
        'super_admin' => [
            'email' => 'superadmin@eclinic',
            'password' => 'admin'
        ],
        'medical_data_admin' => [
            'email' => 'medicaladmin@eclinic',
            'password' => 'admin'
        ],
        'operation_admin' => [
            'email' => 'operationadmin@eclinic',
            'password' => 'admin'
        ]
    ];

    // Method to identify the role of an admin based on their email and password
    public function getAdminRole($email, $password) {
        foreach ($this->adminCredentials as $role => $credentials) {
            if ($email === $credentials['email'] && $password === $credentials['password']) {
                return $role;
            }
        }
        return null;
    }

    // Method to retrieve all stored admin credentials
    public function getAdminCredentials() {
        return $this->adminCredentials;
    }
}
?>
