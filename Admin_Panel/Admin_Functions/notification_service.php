<?php

class NotificationService {

    public function success($title, $message, $position = 'top-end', $timer = 3000) {
        return $this->generateSweetAlert('success', $title, $message, $position, $timer);
    }
    
    public function error($title, $message, $position = 'center', $timer = 0) {
        return $this->generateSweetAlert('error', $title, $message, $position, $timer);
    }
    

    public function warning($title, $message, $position = 'center', $timer = 0) {
        return $this->generateSweetAlert('warning', $title, $message, $position, $timer);
    }
    

    public function info($title, $message, $position = 'center', $timer = 3000) {
        return $this->generateSweetAlert('info', $title, $message, $position, $timer);
    }
    

    public function welcomeGreeting($role, $position = 'top-end') {
        $title = 'Welcome!';
        $message = '';
        
        switch ($role) {
            case 'super_admin':
                $message = 'Welcome back, Super Admin. You have full access to all system features.';
                break;
            case 'care_admin':
                $message = 'Welcome back, Care Admin. You have access to medical records and appointment management.';
                break;
            default:
                $message = 'Welcome to the E-CLINIC system.';
        }
        
        return $this->generateSweetAlert('success', $title, $message, $position, 3000);
    }
    

    public function confirmation($title, $message, $confirmButtonText = 'Yes', $cancelButtonText = 'No', $confirmCallback = '') {
        $script = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '{$title}',
                text: '{$message}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: '{$confirmButtonText}',
                cancelButtonText: '{$cancelButtonText}'
            }).then((result) => {
                if (result.isConfirmed) {
                    {$confirmCallback}
                }
            });
        });
        </script>
        ";
        
        return $script;
    }
    
    private function generateSweetAlert($icon, $title, $message, $position, $timer) {
        $timerConfig = $timer > 0 ? "timer: {$timer}," : '';
        
        $script = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{$icon}',
                title: '{$title}',
                text: '{$message}',
                position: '{$position}',
                {$timerConfig}
                showConfirmButton: " . ($timer > 0 ? 'false' : 'true') . "
            });
        });
        </script>
        ";
        
        return $script;
    }
    

    public function toast($icon, $title, $position = 'top-end', $timer = 3000) {
        $script = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: '{$position}',
                showConfirmButton: false,
                timer: {$timer},
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: '{$icon}',
                title: '{$title}'
            });
        });
        </script>
        ";
        
        return $script;
    }
    
    public function loginSuccess($role) {
        $title = 'Login Successful';
        $message = '';
        
        switch ($role) {
            case 'super_admin':
                $message = 'Welcome back, Super Admin! You now have access to all administrative features.';
                break;
            case 'care_admin':
                $message = 'Welcome back, Care Admin! You now have access to medical records and appointment management.';
                break;
            default:
                $message = 'You have successfully logged in to the E-CLINIC system.';
        }
        
        return $this->generateSweetAlert('success', $title, $message, 'center', 2000);
    }
}
?>