<?php

namespace HelpSelf;

require_once __DIR__ . '/BaseDao.class.php';

class UserDao extends BaseDao
{

    public function __construct()
    {
        parent::__construct("users");
    }

    public function add_user($user)
    {
        return $this->insert('users', $user);
    }

    public function get_users($offset, $limit, $search, $order_column, $order_direction)
    {
        $query = "SELECT * 
                  FROM users
                  WHERE LOWER(username) LIKE CONCAT('%', :search, '%') OR 
                        LOWER(email) LIKE CONCAT('%', :search, '%')
                  ORDER BY {$order_column} {$order_direction}
                  LIMIT {$offset}, {$limit}";
        return $this->query($query, ['search' => strtolower($search)]);
    }

    public function get_user_by_email($email)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :email", ["email" => $email]);
    }

    public function get_user_by_login($login)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :login OR username = :login", ["login" => $login]);
    }

    public function count_users($search)
    {
        $query = "SELECT COUNT(*) AS count 
                  FROM users
                  WHERE LOWER(username) LIKE CONCAT('%', :search, '%') OR 
                        LOWER(email) LIKE CONCAT('%', :search, '%')";
        return $this->query_unique($query, ['search' => strtolower($search)]);
    }

    public function get_all_users()
    {
        return $this->get_all(0, 100000);
    }

    public function get_user_by_id($user_id)
    {
        return $this->query_unique("SELECT * FROM users WHERE id = :id", ["id" => $user_id]);
    }

    public function delete_user_by_id($user_id)
    {
        $this->execute("DELETE FROM users WHERE id = :id", ["id" => $user_id]);
    }

    public function update_user_by_id($user_id, $user) {
        $query = "UPDATE users SET
                --   first_name = :first_name,
                --   last_name = :last_name,
                  biography = :biography,
                  location = :location
                  WHERE id = :id";
        $params = [
            // ':first_name' => $user['first_name'],
            // ':last_name' => $user['last_name'],
            ':biography' => $user['biography'],
            ':location' => $user['location'],
            ':id' => $user_id
        ];
        return $this->execute($query, $params);
    }

    public function insert_email_verification_token($user_id, $token, $expires_at)
    {
        $this->execute("INSERT INTO email_verifications (user_id, verification_token, expires_at) VALUES (:user_id, :verification_token, :expires_at)", [
            "user_id" => $user_id,
            "verification_token" => $token,
            "expires_at" => $expires_at
        ]);
    }

    public function get_email_verification_by_token($token)
    {
        return $this->query_unique("SELECT * FROM email_verifications WHERE verification_token = :token", ["token" => $token]);
    }

    public function delete_email_verification_token($id)
    {
        $this->execute("DELETE FROM email_verifications WHERE id = :id", ["id" => $id]);
    }

    public function verify_email($user_id)
    {
        $this->execute("UPDATE users SET is_email_verified = 1 WHERE id = :id", ["id" => $user_id]);
    }

    public function is_email_verified($login)
    {
        $query = "SELECT is_email_verified FROM users WHERE email = :login OR username = :login";
        $result = $this->query_unique($query, ['login' => $login]);
        return $result ? $result['is_email_verified'] : false;
    }

    public function insert_password_reset_token($user_id, $token, $expires_at)
    {
        $this->execute("INSERT INTO password_resets (user_id, reset_token, expires_at) VALUES (:user_id, :reset_token, :expires_at)", [
            "user_id" => $user_id,
            "reset_token" => $token,
            "expires_at" => $expires_at
        ]);
    }

    public function get_password_reset_by_token($token)
    {
        return $this->query_unique("SELECT * FROM password_resets WHERE reset_token = :token", ["token" => $token]);
    }

    public function delete_password_reset_token($id)
    {
        $this->execute("DELETE FROM password_resets WHERE id = :id", ["id" => $id]);
    }

    public function update_user_password($user_id, $password)
    {
        $this->execute("UPDATE users SET password = :password WHERE id = :id", ["password" => $password, "id" => $user_id]);
    }

    public function update_temp_password($user_id, $temp_password)
    {
        $this->execute("UPDATE users SET temp_password = :temp_password WHERE id = :id", ["temp_password" => $temp_password, "id" => $user_id]);
    }

    public function delete_temp_password($user_id)
    {
        $this->execute("UPDATE users SET temp_password = NULL WHERE id = :id", ["id" => $user_id]);
    }

    public function insert_recovery_code($user_id, $code_hash)
    {
        $this->execute("INSERT INTO recovery_codes (user_id, code_hash) VALUES (:user_id, :code_hash)", [
            "user_id" => $user_id,
            "code_hash" => $code_hash
        ]);
    }

    public function get_recovery_codes_by_user_id($user_id)
    {
        return $this->query("SELECT code_hash FROM recovery_codes WHERE user_id = :user_id", ["user_id" => $user_id]);
    }



}
?>