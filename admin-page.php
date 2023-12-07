<?php

function likes_table_page_load()
{
    require_once __DIR__ . '/class-Likes_List_Table.php';
    $GLOBALS['Likes_List_Table'] = new Likes_List_Table();
}

function likes_table_page()
{
?>

    <div class="wrap">
        <h2><?= get_admin_page_title() ?></h2>
        <form action="" method="POST">
            <?php $GLOBALS['Likes_List_Table']->display(); ?>
        </form>
    </div>
    
<?php
}
