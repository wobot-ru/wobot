<?

function get_last_id()
{
    global $db;
    $qpost=$db->query('SELECT post_id FROM blog_post ORDER BY post_id DESC LIMIT 1');
    $post=$db->fetch($qpost);
    return $post['post_id'];
}

?>