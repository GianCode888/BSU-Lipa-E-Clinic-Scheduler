<?php

class ErrorHandler {
    private $errors = [];
    
    public function validateEmail($email) {
        if (empty($email)) {
            $this->errors['email'] = 'Email is required';
            return false;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format';
            return false;
        }
        
        return true;
    }
    
    public function validateRequired($value, $fieldName) {
        if (empty($value)) {
            $this->errors[$fieldName] = ucfirst(str_replace('_', ' ', $fieldName)) . ' is required';
            return false;
        }
        
        return true;
    }
    
     // check(letters, spaces, and basic punctuation only)
    public function validateName($name, $fieldName) {
        if (empty($name)) {
            $this->errors[$fieldName] = ucfirst($fieldName) . ' is required';
            return false;
        }
        
        if (!preg_match('/^[a-zA-Z\s\'-]+$/', $name)) {
            $this->errors[$fieldName] = ucfirst($fieldName) . ' can only contain letters, spaces, and punctuation';
            return false;
        }
        
        return true;
    }
    
    public function validateRole($role) {
        $validRoles = ['student', 'doctor', 'nurse', 'medical_data_admin', 'super_admin'];
        
        if (!in_array($role, $validRoles)) {
            $this->errors['role'] = 'Invalid role selected';
            return false;
        }
        
        return true;
    }
    
    public function validateContactNumber($contactNumber) {
        // Skip validation if empty
        if (empty($contactNumber)) {
            return true;
        }
        
        // alisin ang letter
        $digitsOnly = preg_replace('/\D/', '', $contactNumber);
        
        
        if (strlen($digitsOnly) !== 11) {
            $this->errors['contact_number'] = 'Contact number must be exactly 11 digits';
            return false;
        }
        
        return true;
    }
    
    public function validateAddress($address) {
        // Skip validation if empty
        if (empty($address)) {
            return true;
        }
        
        if (strlen($address) < 5) {
            $this->errors['address'] = 'Address is too short';
            return false;
        }
        
        return true;
    }
    
     // Validate user exists
    public function validateUserExists($user) {
        if (!$user) {
            $this->errors['user'] = 'User not found';
            return false;
        }
        
        return true;
    }
    
     // Validating ng full user data bago isave sa system
    public function validateUserData($userData) {
        $isValid = true;
        
  
        if (!$this->validateName($userData['first_name'], 'first_name')) {
            $isValid = false;
        }
        
        if (!$this->validateName($userData['last_name'], 'last_name')) {
            $isValid = false;
        }
        

        if (!$this->validateEmail($userData['email'])) {
            $isValid = false;
        }
        
 
        if (!$this->validateRole($userData['role'])) {
            $isValid = false;
        }
        

        if (isset($userData['address']) && !empty($userData['address'])) {
            if (!$this->validateAddress($userData['address'])) {
                $isValid = false;
            }
        }
        

        if (isset($userData['contact_number']) && !empty($userData['contact_number'])) {
            if (!$this->validateContactNumber($userData['contact_number'])) {
                $isValid = false;
            }
        }
        
        return $isValid;
    }
    
    // Validate if changes were made to user data
    public function validateChanges($originalData, $newData) {
        $fieldsToCompare = [
            'first_name',
            'last_name',
            'email',
            'role',
            'address',
            'contact_number'
        ];
        
        $changesDetected = false;
        
        foreach ($fieldsToCompare as $field) {
            // Handle null values
            $originalValue = isset($originalData[$field]) ? trim($originalData[$field]) : '';
            $newValue = isset($newData[$field]) ? trim($newData[$field]) : '';
            
            // Compare values
            if ($originalValue !== $newValue) {
                $changesDetected = true;
                break;
            }
        }
        
        if (!$changesDetected) {
            $this->errors['no_changes'] = 'No changes were made to the user information';
            return false;
        }
        
        return true;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getError($field) {
        return isset($this->errors[$field]) ? $this->errors[$field] : null;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    public function clearErrors() {
        $this->errors = [];
    }
}
?>
