<?php

namespace HelpSelf;

require_once __DIR__ . '/../dao/UserDao.class.php';
require_once __DIR__ . '/../../../config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;

class UserService
{
    private $user_dao;

    public function __construct()
    {
        $this->user_dao = new UserDao();
    }

    public function get_all_users()
    {
        return $this->user_dao->get_all_users();
    }

    public function add_user($user)
    {
        $reservedUsernames = ['admin', 'administrator', 'root', 'system', 'support'];

        if (strlen($user['username']) <= 3) {
            throw new \Exception('Username must be longer than 3 characters.');
        }

        if (!ctype_alnum($user['username'])) {
            throw new \Exception('Username can only include alphanumeric characters.');
        }

        if (in_array(strtolower($user['username']), $reservedUsernames)) {
            throw new \Exception('This username is reserved. Please choose a different username.');
        }

        $existingUser = $this->user_dao->get_user_by_login($user['username']);
        if ($existingUser) {
            throw new \Exception('Username already exists. Please choose a different username.');
        }

        if (!$this->isValidTLD($user['email'])) {
            throw new \Exception('Invalid email TLD.');
        }

        if (!$this->hasMXRecords($user['email'])) {
            throw new \Exception('Email domain does not have valid MX records.');
        }

        if (!$this->isValidMobileNumber($user['phone_number'], 'BA')) { 
            throw new \Exception('Invalid mobile phone number.');
        }

        if (isset($user['password'])) {
            if (strlen($user['password']) < 8) {
                throw new \Exception('Password should be at least 8 characters long.');
            }

            if ($this->isPasswordPwned($user['password'])) {
                throw new \Exception('The password has been compromised. Please choose a different password.');
            }

            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        }

        $result = $this->user_dao->add_user($user);

        if ($result) {
            $this->generateEmailVerificationToken($result['id'], $user['email']);
            $unhashed_codes = $this->generate_recovery_codes(); 
            return ['user' => $result, 'recovery_codes' => $unhashed_codes]; 
        }

        return $result;
    }

    public function generate_recovery_codes()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $code = bin2hex(random_bytes(4));
            $formatted_code = substr($code, 0, 4) . '-' . substr($code, 4, 4);
            $codes[] = $formatted_code;
        }
        return $codes;
    }

    public function store_recovery_codes($user_id, $codes)
    {
        foreach ($codes as $code) {
            $code_hash = password_hash($code, PASSWORD_DEFAULT);
            $this->user_dao->insert_recovery_code($user_id, $code_hash);
        }
    }

    private function generate_and_insert_recovery_codes($user_id)
    {
        $codes = $this->generate_recovery_codes();
        $unhashed_codes = [];
        foreach ($codes as $code) {
            $code_hash = password_hash($code, PASSWORD_DEFAULT);
            $this->user_dao->insert_recovery_code($user_id, $code_hash);
            $unhashed_codes[] = $code; 
        }
        return $unhashed_codes; 
    }



    private function isValidMobileNumber($phoneNumber, $region)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($phoneNumber, $region);
            return $phoneUtil->getNumberType($numberProto) === PhoneNumberType::MOBILE;
        } catch (NumberParseException $e) {
            return false;
        }
    }

    private function isPasswordPwned($password)
    {
        $sha1Password = strtoupper(sha1($password));
        $prefix = substr($sha1Password, 0, 5);
        $suffix = substr($sha1Password, 5);

        $ch = curl_init("https://api.pwnedpasswords.com/range/" . $prefix);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \Exception('Could not retrieve data from the Pwned Passwords API.');
        }

        return str_contains($response, $suffix);
    }

    private function isValidTLD($email)
    {
        $tld = substr(strrchr($email, "."), 1);
        $tldList = @file_get_contents("https://data.iana.org/TLD/tlds-alpha-by-domain.txt");

        if ($tldList === false) {
            
            error_log("Failed to fetch TLD list from IANA.");
            
            return true;
        }

        $tlds = array_map('trim', explode("\n", $tldList));
        return in_array(strtoupper($tld), $tlds);
    }

    private function hasMXRecords($email)
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, "MX");
    }

    public function resendVerificationEmail($email)
    {
        $user = $this->user_dao->get_user_by_email($email);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        if ($user['is_email_verified']) {
            throw new \Exception('Email is already verified.');
        }

        $this->generateEmailVerificationToken($user['id'], $email);
    }

    private function generateEmailVerificationToken($user_id, $email)
    {
        $token = bin2hex(random_bytes(16));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 minute'));

        $this->user_dao->insert_email_verification_token($user_id, $token, $expires_at);
        $this->sendVerificationEmail($email, $token);
    }

    private function sendVerificationEmail($email, $token)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USERNAME, 'Mailer');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = 'Please click on the following link to verify your email: <a href="http://localhost/Help-Self/backend/rest/verify_email?token=' . $token . '">Verify Email</a>';

            $mail->send();
        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function verifyEmail($token)
    {
        $verification = $this->user_dao->get_email_verification_by_token($token);

        if ($verification && strtotime($verification['expires_at']) > time()) {
            $this->user_dao->verify_email($verification['user_id']);
            $this->user_dao->delete_email_verification_token($verification['id']);
            return true;
        }

        return false;
    }

    public function validateCredentials($login, $password)
    {
        $user = $this->user_dao->get_user_by_login($login);

        if (!$user) {
            throw new \Exception('User credentials not valid.');
        }

        $passwordValid = !empty($user['password']) && password_verify($password, $user['password']);
        $tempPasswordValid = !empty($user['temp_password']) && password_verify($password, $user['temp_password']);

        if (!$passwordValid && !$tempPasswordValid) {
            throw new \Exception('User credentials not valid.');
        }

        if (!$this->user_dao->is_email_verified($login)) {
            throw new \Exception('Email is not verified.');
        }

        
        if ($tempPasswordValid) {
            $this->user_dao->delete_temp_password($user['id']);
        }

        return true;
    }

    public function get_users($offset, $limit, $search, $order_column, $order_direction)
    {
        return $this->user_dao->get_users($offset, $limit, $search, $order_column, $order_direction);
    }

    public function count_users($search)
    {
        return $this->user_dao->count_users($search);
    }

    public function get_user_by_id($user_id)
    {
        return $this->user_dao->get_user_by_id($user_id);
    }

    public function delete_user_by_id($user_id)
    {
        return $this->user_dao->delete_user_by_id($user_id);
    }

    public function get_user_by_login($login)
    {
        return $this->user_dao->get_user_by_login($login);
    }

    public function update_user($user_id, $user) {
        return $this->user_dao->update_user_by_id($user_id, $user);
    }

    public function sendPasswordResetLink($email)
    {
        $user = $this->user_dao->get_user_by_email($email);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        
        $temp_password = bin2hex(random_bytes(8)); 
        $hashed_temp_password = password_hash($temp_password, PASSWORD_DEFAULT);

        
        $this->user_dao->update_temp_password($user['id'], $hashed_temp_password);

        
        $this->sendPasswordResetEmail($email, $temp_password);
    }

    private function sendPasswordResetEmail($email, $temp_password)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USERNAME, 'Mailer');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Temporary Password';
            $mail->Body = 'Your temporary password is: ' . $temp_password;

            $mail->send();
        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function resetPassword($token)
    {
        $reset_request = $this->user_dao->get_password_reset_by_token($token);

        if ($reset_request && strtotime($reset_request['expires_at']) > time()) {
            $user = $this->user_dao->get_user_by_id($reset_request['user_id']);
            if (empty($user['temp_password'])) {
                throw new \Exception('Temporary password not set.');
            }
            
            $this->user_dao->update_user_password($reset_request['user_id'], $user['temp_password']);
            $this->user_dao->delete_temp_password($reset_request['user_id']);
            $this->user_dao->delete_password_reset_token($reset_request['id']);
            return true;
        }

        return false;
    }

    private function sendPasswordChangedEmail($email)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USERNAME, 'Mailer');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Changed';
            $mail->Body = 'Your password has been successfully changed.';

            $mail->send();
        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function changePassword($userId, $newPassword)
    {
        
        if (strlen($newPassword) < 8) {
            throw new \Exception('Password should be at least 8 characters long.');
        }

        
        if ($this->isPasswordPwned($newPassword)) {
            throw new \Exception('The password has been compromised. Please choose a different password.');
        }

        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        
        $this->user_dao->update_user_password($userId, $hashedPassword);

        
        $user = $this->user_dao->get_user_by_id($userId);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        
        $this->sendPasswordChangedEmail($user['email']);
    }

    public function getRecoveryCodes($userId)
    {
        $recoveryCodes = $this->user_dao->get_recovery_codes_by_user_id($userId);
        return array_map(function ($code) {
            return $code['code_hash'];
        }, $recoveryCodes);
    }






}
?>