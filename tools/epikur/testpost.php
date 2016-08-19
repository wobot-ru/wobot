<?

require_once('infix.php');

echo check_post('((почта|"мыло"|"мылу"|"мыла"|"мыле"|"мылом"|имейл|емейл|эмейл|мыльник|ящик)&(mailru|мейлру|инбоксру|листру|бкру|инбокссру|листсру|бксру|бэкару|бэкасру|бекару|бекасру|((мейл|мэйл|майл|инбокс|mail|лист|бк|list|inbox|бэка|бека)&(ру|сру))|мэйлру|майлру|мэйлсру|майлсру|мейлсру|mail.ru|bk.ru|list.ru|inbox.ru))~~"мой мир"~~"@mail.ru"~~"@list.ru"~~"@bk.ru"~~"@inbox.ru"~~"mail@"','почта бкру');
echo check_post('RT: @_rcp rabbitmq is a best thing','@_rcp && rabbitmq');
echo '<form method="POST" action="infix_post.php">
post<input type="text" name="post">
kw<input type="text" name="kw">
<input type="submit" value="проверить">
</form>';

?>