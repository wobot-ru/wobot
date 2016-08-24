<?

$outmas[0][0]['id']='post_host';
$outmas[0][0]['name']='Площадка';
$outmas[0][0]['desc']='Площадка на которой написано упоминание';

$outmas[0][1]['id']='blog_location';
$outmas[0][1]['name']='Город автора';
$outmas[0][1]['desc']='Город автора сообщения';

$outmas[0][2]['id']='blog_gender';
$outmas[0][2]['name']='Пол автора';
$outmas[0][2]['desc']='Пол автора сообщения';

$outmas[1][0]['id']='post_count';
$outmas[1][0]['name']='Количество сообщений';
$outmas[1][0]['desc']='По количеству сообщений';

$outmas[1][1]['id']='author_count';
$outmas[1][1]['name']='Количество уникальных авторов';
$outmas[1][1]['desc']='По количеству уникальных авторов';

$outmas[1][2]['id']='value';
$outmas[1][2]['name']='Охват';
$outmas[1][2]['desc']='По количеству друзей автора';

$outmas[1][3]['id']='retweet';
$outmas[1][3]['name']='Ретвиты';
$outmas[1][3]['desc']='По количеству ретвитов сообщений';

$outmas[1][4]['id']='likes';
$outmas[1][4]['name']='Лайки';
$outmas[1][4]['desc']='По количеству лайков сообщений';

$outmas[1][5]['id']='comments';
$outmas[1][5]['name']='Комментарии';
$outmas[1][5]['desc']='По количеству комментариев сообщений';

$outmas[1][6]['id']='engage';
$outmas[1][6]['name']='Вовлеченность';
$outmas[1][6]['desc']='По вовлеченности сообщений';

echo json_encode($outmas);

?>