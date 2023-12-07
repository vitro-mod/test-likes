<?php

class Likes_List_Table extends WP_List_Table
{

    function __construct()
    {
        parent::__construct(array(
            'singular' => 'like',
            'plural'   => 'likes',
        ));

        $this->prepare_items();

        add_action('wp_print_scripts', [__CLASS__, '_list_table_css']);
    }

    function prepare_items()
    {
        global $wpdb;

        $posts_table = $wpdb->get_blog_prefix() . 'posts';
        $likes_table = $wpdb->get_blog_prefix() . 'post_likes';
        $sql = <<<SQL
            SELECT 
                p.ID as post_id, 
                SUM(case when l.post_id = p.ID and l.is_like > 0 then 1 else 0 end) as likes, 
                SUM(case when l.post_id = p.ID and l.is_like < 0 then 1 else 0 end) as dislikes 
            FROM 
                $posts_table p, 
                $likes_table l
            WHERE 
                p.post_status = 'publish' AND
                p.post_type = 'post'
            GROUP BY p.ID;
        SQL;
        $data = $wpdb->get_results($sql);
        usort($data, array(&$this, 'sort_data'));

        $per_page = 10;
        $this->set_pagination_args(array(
            'total_items' => count($data),
            'per_page'    => $per_page,
        ));
        $cur_page = (int) $this->get_pagenum();

        $data = array_slice($data, (($cur_page - 1) * $per_page), $per_page);

        $this->items = $data;
    }

    function get_columns()
    {
        return array(
            'post_id'    => 'ID',
            'post'       => 'Post',
            'likes'      => 'Количество лайков',
            'dislikes'   => 'Количество дизлайков',
        );
    }

    function get_sortable_columns()
    {
        return array(
            'likes' => array('likes', 'desc'),
            'dislikes' => array('dislikes', 'desc'),
        );
    }

    static function _list_table_css()
    {
        ?>
        <style>
            table.likes #post_id{ width:2em; }
        </style>
        <?php
	}

    function column_default($item, $colname)
    {
        return isset($item->$colname) ? $item->$colname : get_the_title($item->post_id);
    }

    private function sort_data($a, $b)
    {
        $orderby = 'post_id';
        $order = 'desc';

        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = (int) $a->$orderby > (int) $b->$orderby;
        if ($order === 'desc') {
            $result = (int) $a->$orderby < (int) $b->$orderby;
        }

        return $result;
    }
}
