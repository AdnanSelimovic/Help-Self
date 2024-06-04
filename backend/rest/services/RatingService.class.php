<?php

namespace HelpSelf;

require_once __DIR__ . '/../dao/RatingDao.class.php';

/**
 * The RatingService class is responsible for handling all business logic related to ratings.
 */
class RatingService
{

    private $rating_dao;


    public function __construct()
    {
        $this->rating_dao = new RatingDao();
    }


    public function get_all_ratings()
    {
        return $this->rating_dao->get_all();
    }


    public function add_rating($rating)
    {
        return $this->rating_dao->add_rating($rating);
    }


    public function get_ratings_by_habit_id($habit_id)
    {
        return $this->rating_dao->get_ratings_by_habit_id($habit_id);
    }


    public function update_rating($id, $rating)
    {
        $this->rating_dao->update_rating($id, $rating);
    }


    public function delete_rating_by_id($id)
    {
        $this->rating_dao->delete_rating_by_id($id);
    }


    public function get_ratings_by_user_id($user_id)
    {
        return $this->rating_dao->get_ratings_by_user_id($user_id);
    }


    public function get_all_user_ratings($user_id)
    {
        return $this->rating_dao->get_all_ratings_for_user($user_id);
    }

    public function get_average_rating_for_user($user_id) {
        return $this->rating_dao->get_average_rating_for_user($user_id);
    }
    

}
?>