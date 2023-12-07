<?php

/*
 * Plugin Name: Лайки для тестового задания
 * Author: Victor Troitskii
 */

require_once 'activation.php';
register_activation_hook(__FILE__, function () {
    create_likes_table();
});

require_once 'handlers.php';
add_action('wp_ajax_post_likes', 'set_post_likes');
add_action('wp_ajax_nopriv_post_likes', 'set_post_likes');

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('likes', plugin_dir_url(__FILE__) . 'js/likes.js', array(), '1.0');
    wp_add_inline_script(
        'likes',
        'window.wp_ajax_url = "' . admin_url('admin-ajax.php') . '";'
    );
});

require_once 'admin-page.php';
add_action('admin_menu', function () {
    $hook = add_menu_page('Статистика лайков', 'Лайки', 'manage_options', 'page-likes', 'likes_table_page', 'dashicons-heart', 100);

    add_action("load-$hook", 'likes_table_page_load');
});
