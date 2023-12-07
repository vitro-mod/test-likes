<?php

function set_post_likes()
{
    global $wpdb;

    $table = $wpdb->get_blog_prefix() . 'post_likes';

    $type = $_POST['type'];
    $post_id = $_POST['post_id'];
    $is_like = 0;
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $url = $_POST['url'];
    $timestamp = date('Y-m-d H:i:s');

    switch ($type) {
        case 'like':
            $is_like = 1;
            break;
        case 'dislike':
            $is_like = -1;
            break;
    }

    $sql = <<<SQL
        INSERT INTO $table (post_id, is_like, user_ip, url, timestamp) 
        VALUES (%d, %d, %s, %s, %s) ON DUPLICATE KEY UPDATE 
            post_id = %d,
            is_like = %d,
            user_ip = %s,
            url = %s,
            timestamp = %s
    SQL;

    $vars = [$post_id, $is_like, $user_ip, $url, $timestamp, $post_id, $is_like, $user_ip, $url, $timestamp];

    $wpdb->get_results($wpdb->prepare($sql, $vars));

    $response;
    if (!$wpdb->last_error) {
        $response = [
            'success' => true,
            'new_rating' => get_post_likes($post_id),
        ];
    } else {
        $response = [
            'success' => false,
        ];
    }

    echo json_encode($response);

    wp_die();
}

function get_posts_likes()
{
    global $wpdb;

    $table = $wpdb->get_blog_prefix() . 'post_likes';
    
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $sql = <<<SQL
        SELECT 
            post_id, 
            SUM(is_like) as total, 
            SUM(case when user_ip = '$user_ip' then is_like else 0 end) as user 
        FROM $table
        GROUP BY post_id;
    SQL;

    return $wpdb->get_results($sql, OBJECT_K);
}

function get_post_likes($post_id)
{
    global $wpdb;

    $table = $wpdb->get_blog_prefix() . 'post_likes';
    $sql = <<<SQL
        SELECT SUM(is_like) as total 
        FROM $table
        WHERE post_id = '%d' 
        GROUP BY post_id;
    SQL;

    return $wpdb->get_results($wpdb->prepare($sql, (int) $post_id))[0]->total;
}