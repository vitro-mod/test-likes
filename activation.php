<?php

function create_likes_table()
{
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $posts_table = $wpdb->get_blog_prefix() . 'posts';
    $likes_table = $wpdb->get_blog_prefix() . 'post_likes';
    $charset = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

    $sql = "CREATE TABLE {$likes_table} (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id BIGINT UNSIGNED NOT NULL default 0 COMMENT 'ID поста',
        is_like TINYINT NOT NULL default 0 COMMENT 'Лайк или дизлайк',
        user_ip VARCHAR(255) NOT NULL default '' COMMENT 'IP пользователя',
        url VARCHAR(255) NOT NULL default '' COMMENT 'Адрес страницы',
        timestamp TIMESTAMP COMMENT 'Дата и время',
        FOREIGN KEY (post_id) REFERENCES {$posts_table} (ID) ON DELETE CASCADE,
		KEY post_id (post_id),
        PRIMARY KEY (id),
        UNIQUE KEY post_user_ip (post_id, user_ip)
    )
    {$charset};";

    dbDelta($sql);

    echo $wpdb->last_error;
}