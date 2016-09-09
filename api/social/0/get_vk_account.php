<?

require_once('com/db.php');
require_once('com/config.php');
require_once('bot/kernel.php');
require_once('com/func_avk.php');

error_reporting(0);

$db = new database();
$db->connect();

$assoc_type_p['photo']='photo';
$assoc_type_p['text']='text';
$assoc_type_p['link']='link';
$assoc_type_p['comment']='comment';
$assoc_type_p['video']='video';
$assoc_type_p['poll']='poll';
$assoc_type_p['like']='like';
$assoc_type_p['reposts']='reposts';
////////
$db->query('UPDATE group_orders SET group_finished=1 WHERE id='.$_SERVER['argv'][1]);
$qpr=$db->query('SELECT * FROM tp_proxys WHERE valid=1 LIMIT '.(($_SERVER['argv'][1] % 10)*10).',10');
while ($proxy=$db->fetch($qpr))
{
	$proxys[]=$proxy['proxy'];
}
$q=$db->query('SELECT * FROM group_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.id='.$_SERVER['argv'][1]);
$ord=$db->fetch($q);
$ord['group_link']=preg_replace('/acc_/isu','',$ord['group_link']);
$ord['group_link']=preg_replace('/^id(\d+)/isu','$1',$ord['group_link']);
$json=get_accout_vk($ord['group_link'],$ord['group_start'],$ord['group_end'],7,($ord['vk_token']==''?'cf65276ccf101f2dcf101f2defcf3a60e0ccf10cf1f97d3fa830c78cd43e697':$ord['vk_token']));
$db->query('UPDATE group_orders SET group_json=\''.json_encode($json).'\',group_finished='.time().' WHERE id='.$_SERVER['argv'][1]);

?>