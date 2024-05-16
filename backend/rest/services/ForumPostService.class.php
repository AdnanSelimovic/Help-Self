<?php
require_once __DIR__ . '/../dao/ForumPostDao.class.php';

/**
 * The ForumPostService class is responsible for handling all business logic related to forum posts.
 */
class ForumPostService
{

    private $forum_post_dao;

    /**
     * Constructor that initializes the ForumPostDao to handle data operations.
     */
    public function __construct()
    {
        $this->forum_post_dao = new ForumPostDao();
    }


    public function get_all_forum_posts()
    {
        return $this->forum_post_dao->get_all();
    }


    public function add_forum_post($post)
    {
        return $this->forum_post_dao->add_forum_post($post);
    }


    public function get_forum_posts($offset, $limit, $search, $order_column, $order_direction)
    {
        return $this->forum_post_dao->get_forum_posts($offset, $limit, $search, $order_column, $order_direction);
    }


    public function get_forum_post_by_id($post_id)
    {
        return $this->forum_post_dao->get_forum_post_by_id($post_id);
    }


    public function get_forum_posts_sorted($order_column = 'date_posted', $order_direction = 'ASC')
    {
        return $this->forum_post_dao->get_forum_posts_sorted($order_column, $order_direction);
    }



    public function update_forum_post($id, $post)
    {
        return $this->forum_post_dao->update_forum_post($id, $post);
    }


    public function delete_forum_post_by_id($post_id)
    {
        return $this->forum_post_dao->delete_forum_post_by_id($post_id);
    }
}
?>