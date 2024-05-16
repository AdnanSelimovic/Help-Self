<?php
require_once __DIR__ . '/BaseDao.class.php';

/**
 * The UserDao class is responsible for handling all database operations for the users table.
 */
class UserDao extends BaseDao
{

    /**
     * Constructor that sets the table name to 'users' when initializing the parent BaseDao class.
     */
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
                  first_name = :first_name,
                  last_name = :last_name,
                  biography = :biography,
                  location = :location
                  WHERE id = :id";
        $params = [
            ':first_name' => $user['first_name'],
            ':last_name' => $user['last_name'],
            ':biography' => $user['biography'],
            ':location' => $user['location'],
            ':id' => $user_id
        ];
        return $this->execute($query, $params);
    }
}
?>