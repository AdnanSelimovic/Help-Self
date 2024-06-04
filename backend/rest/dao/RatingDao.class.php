<?php

namespace HelpSelf;

require_once __DIR__ . '/BaseDao.class.php';

/**
 * The RatingDao class is responsible for handling all database operations for the ratings table.
 */
class RatingDao extends BaseDao
{

    /**
     * Constructor that sets the table name to 'ratings' when initializing the parent BaseDao class.
     */
    public function __construct()
    {
        parent::__construct("ratings");
    }

    
    public function add_rating($rating)
    {
        return $this->insert($this->table, $rating);
    }

    
    public function get_ratings_by_habit_id($habit_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE habit_id = :habit_id", ['habit_id' => $habit_id]);
    }

    
    public function update_rating($id, $rating)
    {
        $this->update($id, $rating);
    }

    
    public function delete_rating_by_id($id)
    {
        $this->execute("DELETE FROM {$this->table} WHERE id = :id", ["id" => $id]);
    }

    
    public function get_ratings_by_user_id($user_id)
    {
        return $this->query(
            "
            SELECT r.* FROM {$this->table} r
            JOIN habits h ON r.habit_id = h.id
            WHERE h.user_id = :user_id",
            ['user_id' => $user_id]
        );
    }
    
    public function get_all_ratings_for_user($user_id) {
        return $this->query(
            "SELECT r.value 
             FROM {$this->table} r
             JOIN habits h ON r.habit_id = h.id
             WHERE h.user_id = :user_id",
            ['user_id' => $user_id]
        );
    }
    
    public function get_average_rating_for_user($user_id) {
        return $this->query_unique(
            "SELECT AVG(r.value) as average_rating 
             FROM {$this->table} r
             JOIN habits h ON r.habit_id = h.id
             WHERE h.user_id = :user_id",
            ['user_id' => $user_id]
        );
    }
    
}
?>