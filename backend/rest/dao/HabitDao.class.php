<?php
require_once __DIR__ . '/BaseDao.class.php';

/**
 * The HabitDao class is responsible for handling all database operations for the habits table.
 */
class HabitDao extends BaseDao {

    /**
     * Constructor that sets the table name to 'habits' when initializing the parent BaseDao class.
     */
    public function __construct() {
        parent::__construct("habits");
    }

    
    public function add_habit($habit) {
        return $this->insert($this->table, $habit);
    }

    
    public function get_habits($offset, $limit, $search, $order_column, $order_direction) {
        $query = "SELECT * FROM habits
                  WHERE LOWER(title) LIKE CONCAT('%', :search, '%') OR 
                        LOWER(description) LIKE CONCAT('%', :search, '%')
                  ORDER BY {$order_column} {$order_direction}
                  LIMIT :offset, :limit";
        return $this->query($query, [
            'search' => strtolower($search),
            'offset' => $offset,
            'limit' => $limit,
            'order_column' => $order_column,
            'order_direction' => $order_direction
        ]);
    }

    
    public function get_habit_by_id($habit_id) {
        return $this->get_by_id($habit_id);
    }

    public function get_habits_by_user_id($user_id) {
        return $this->query("SELECT * FROM habits WHERE user_id = :user_id", ['user_id' => $user_id]);
    }    

    
    public function update_habit($id, $habit) {
        $this->update($id, $habit);
    }

    
    public function delete_habit_by_id($habit_id) {
        $this->execute("DELETE FROM habits WHERE id = :id", ["id" => $habit_id]);
    }
    
}
?>
