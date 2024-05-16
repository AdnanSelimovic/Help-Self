<?php

require_once __DIR__ . '/../dao/UserDao.class.php';

/**
 * The UserService class is responsible for handling all business logic related to users.
 */
class UserService {

    private $user_dao;

    /**
     * Constructor that initializes the UserDao to handle data operations.
     */
    public function __construct() {
        $this->user_dao = new UserDao();
    }

    
    public function get_all_users() {
        return $this->user_dao->get_all_users();
    }

   
    public function add_user($user) {
        return $this->user_dao->add_user($user);
    }

    
    // public function validateCredentials($email, $password) {
    //     $user = $this->user_dao->get_user_by_email($email);
    //     if (!$user) return false; // User not found

    //     // Assuming password is stored hashed, use password_verify to check
    //     return password_verify($password, $user['password']);
    // }

    public function validateCredentials($email, $password) {
        $user = $this->user_dao->get_user_by_email($email);
        if (!$user) return false; // User not found
    
        // Direct comparison if passwords are not hashed
        return $password === $user['password'];
    }
    
    public function update_user($user_id, $user) {
        return $this->user_dao->update_user_by_id($user_id, $user);
    }

   
    public function get_users($offset, $limit, $search, $order_column, $order_direction) {
        return $this->user_dao->get_users($offset, $limit, $search, $order_column, $order_direction);
    }

    
    public function count_users($search) {
        return $this->user_dao->count_users($search);
    }

   
    public function get_user_by_id($user_id) {
        return $this->user_dao->get_user_by_id($user_id);
    }

    
    public function delete_user_by_id($user_id) {
        return $this->user_dao->delete_user_by_id($user_id);
    }
}

?>
