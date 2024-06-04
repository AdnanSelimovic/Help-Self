<?php

namespace HelpSelf;

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

    
    // public function delete_habit_by_id($habit_id) {
    //     $this->execute("DELETE FROM habits WHERE id = :id", ["id" => $habit_id]);
    // }

    public function delete_habit_by_id($habit_id) {
        $stmt = $this->execute("DELETE FROM habits WHERE id = :id", ["id" => $habit_id]);
        return $stmt->rowCount() > 0;
    }
    

    public function update_habit_progress($habit_id, $currentMilestone, $totalProgress) {
        $query = "UPDATE {$this->table} 
                  SET currentMilestone = :currentMilestone, 
                      totalProgress = :totalProgress 
                  WHERE id = :habit_id";
        $bindings = [
            'currentMilestone' => $currentMilestone,
            'totalProgress' => $totalProgress,
            'habit_id' => $habit_id
        ];
        return $this->execute($query, $bindings);
    }

    // public function update_habit_details($id, $description, $currentMilestone, $increment) {
    //     $query = "UPDATE {$this->table} 
    //               SET description = :description, 
    //                   currentMilestone = :currentMilestone, 
    //                   increment = :increment 
    //               WHERE id = :id";
    //     $bindings = [
    //         'description' => $description,
    //         'currentMilestone' => $currentMilestone,
    //         'increment' => $increment,
    //         'id' => $id
    //     ];
    //     return $this->execute($query, $bindings);
    // }

    public function update_habit_details($id, $description, $milestone, $increment) {
        $query = "UPDATE {$this->table} 
                  SET description = :description, 
                      milestone = :milestone,  
                      increment = :increment 
                  WHERE id = :id";
        $bindings = [
            'description' => $description,
            'milestone' => $milestone,
            'increment' => $increment,
            'id' => $id
        ];
        return $this->execute($query, $bindings);
    }
    

    public function incrementMilestonesReached($habit_id) {
        $query = "UPDATE {$this->table}
                  SET milestonesReached = milestonesReached + 1
                  WHERE id = :habit_id";
        $bindings = ['habit_id' => $habit_id];
        return $this->execute($query, $bindings);
    }
    
    
    
}
?>
