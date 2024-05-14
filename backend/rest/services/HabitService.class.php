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

    public function get_habits_by_user_id($user_id)
    {
        return $this->habit_dao->get_habits_by_user_id($user_id);
    }



    public function update_habit($id, $habit)
    {
        $this->habit_dao->update_habit($id, $habit);
    }


    // public function delete_habit_by_id($habit_id)
    // {
    //     $this->habit_dao->delete_habit_by_id($habit_id);
    // }

    public function delete_habit_by_id($habit_id)
    {
        return $this->habit_dao->delete_habit_by_id($habit_id);
    }


    // public function increment_habit_progress($habit_id) {
    //     $habit = $this->get_habit_by_id($habit_id);
    //     if (!$habit) throw new Exception("Habit not found");

    //     // Calculate new values
    //     $newCurrentMilestone = $habit['currentMilestone'] + $habit['increment'];
    //     $newTotalProgress = $habit['totalProgress'] + $habit['increment'];

    //     // Check for milestone completion
    //     if ($newCurrentMilestone >= $habit['milestone']) {
    //         $newCurrentMilestone -= $habit['milestone'];
    //         // If milestonesReached is tracked, increment it
    //         // $this->habit_dao->increment_milestones_reached($habit_id);
    //     }

    //     // Update the habit progress
    //     return $this->habit_dao->update_habit_progress($habit_id, $newCurrentMilestone, $newTotalProgress);
    // }

    public function increment_habit_progress($habit_id)
    {
        $habit = $this->get_habit_by_id($habit_id);
        if (!$habit)
            throw new Exception("Habit not found");

        $newCurrentMilestone = $habit['currentMilestone'] + $habit['increment'];
        $newTotalProgress = $habit['totalProgress'] + $habit['increment'];

        if ($newCurrentMilestone >= $habit['milestone']) {
            $newCurrentMilestone -= $habit['milestone'];
            $this->habit_dao->incrementMilestonesReached($habit_id); // Increment milestonesReached
        }

        return $this->habit_dao->update_habit_progress($habit_id, $newCurrentMilestone, $newTotalProgress);
    }

    public function update_habit_details($id, $description, $milestone, $increment)
    {
        return $this->habit_dao->update_habit_details($id, $description, $milestone, $increment);
    }



}
?>