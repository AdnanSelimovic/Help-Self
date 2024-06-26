<?php

namespace HelpSelf;

require_once __DIR__ . '/BaseDao.class.php';

class ForumPostDao extends BaseDao {

    /**
     * Constructor that sets the table name to 'forum_posts' when initializing the parent BaseDao class.
     */
    public function __construct() {
        parent::__construct("forum_posts");
    }

   
    public function add_forum_post($post) {
        return $this->insert($this->table, $post);
    }

    
    public function get_forum_posts($offset, $limit, $search, $order_column, $order_direction) {
        $query = "SELECT * FROM forum_posts
                  WHERE LOWER(title) LIKE CONCAT('%', :search, '%') OR 
                        LOWER(content) LIKE CONCAT('%', :search, '%')
                  ORDER BY {$order_column} {$order_direction}
                  LIMIT :offset, :limit";
        return $this->query($query, [
            'search' => strtolower($search),
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function get_forum_posts_sorted($order_column = 'date_posted', $order_direction = 'ASC') {
        // Sanitize input parameters
        $order_column = filter_var($order_column, FILTER_SANITIZE_SPECIAL_CHARS);
        $order_direction = strtoupper($order_direction) === 'ASC' ? 'ASC' : 'DESC';

        // Construct the SQL query directly with sanitized parameters
        $query = "SELECT * FROM {$this->table} ORDER BY {$order_column} {$order_direction}";
        return $this->query($query, []);
    }
    
    public function get_forum_post_by_id($post_id) {
        return $this->get_by_id($post_id);
    }

    
    public function update_forum_post($id, $post) {
        $this->update($id, $post);
    }

    
    public function delete_forum_post_by_id($post_id) {
        $this->execute("DELETE FROM forum_posts WHERE id = :id", ["id" => $post_id]);
    }
}
?>
