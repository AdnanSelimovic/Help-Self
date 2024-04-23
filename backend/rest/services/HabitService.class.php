<?php
require_once __DIR__ . '/../dao/HabitDao.class.php';

/**
 * The HabitService class is responsible for handling all business logic related to habits.
 */
class HabitService
{

    private $habit_dao;

    /**
     * Constructor that initializes the HabitDao to handle data operations.
     */
    public function __construct()
    {
        $this->habit_dao = new HabitDao();
    }

    
    public function get_all_habits()
    {
        return $this->habit_dao->get_all();
    }

   
    public function add_habit($habit)
    {
        return $this->habit_dao->add_habit($habit);
    }

    
    public function get_habits($offset, $limit, $search, $order_column, $order_direction)
    {
        return $this->habit_dao->get_habits($offset, $limit, $search, $order_column, $order_direction);
    }

    
    public function get_habit_by_id($habit_id)
    {
        return $this->habit_dao->get_habit_by_id($habit_id);
    }

    public function get_habits_by_user_id($user_id) {
        return $this->habit_dao->get_habits_by_user_id($user_id);
    }
    

    
    public function update_habit($id, $habit)
    {
        $this->habit_dao->update_habit($id, $habit);
    }

    
    public function delete_habit_by_id($habit_id)
    {
        $this->habit_dao->delete_habit_by_id($habit_id);
    }
}
?>