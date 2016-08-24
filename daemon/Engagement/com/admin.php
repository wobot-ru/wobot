<?php
	session_start();
	$i=0;
	if ($_SESSION['g']!=0)
	{
		$i=$_SESSION['g'];
	}
	//echo $_SESSION['g'];
	echo $i;
	//echo $_POST['us'],' ',$_POST['pas'];
	$link = mysql_connect('localhost','root','') or die("Не могу соединиться"); 
	mysql_select_db('admin'); 
	$result1=mysql_query('SELECT * FROM users');
	$c=0;
	while ($row = mysql_fetch_array($result1,MYSQL_NUM))
	{
		if (($_POST['us']==$row[1]) && ($_POST['pas']==$row[2]))
		{
			$c=1;
			break;
		}
		//echo $row[1],$row[2];
	}
	if ($row[3]==1)
	{
		$type=1;
	}
	else
	{
		$type=0;
	}
	echo 'C=',$c;
	if ($c==0)
	{
		$_SESSION['g']=0;
		$i=0;
	}
	if ($i==0)
	{
		$_SESSION['g']=1;
		echo '
				<html>
					<head>
						<body align="center" valign="center">
							<form name="test" method="post" action="admin.php">
								<input type="text" name="us"><br>
								<input type="password" name="pas"><br>
								<input type="submit" name="but" value="enter">
							</form>
						</body>
					</head>
				</html>
			';
			$i++;
	}
	else
	if ($i!=0)
	{
		$_SESSION['g']=0;
		echo '
				<html>
					<head>
						<body align="center">
			';
		if ($type==1)
		{
			echo '
							<form name="test" method="post" action="admin.php">
								<input type="submit" name="but" value="enter1">
							</form>
							<div align="left">
								<a href="admin.php">Пункт1</a><br>
								<a href="admin.php">Пункт2</a><br>
								<a href="admin.php">Пункт3</a><br>
								<a href="admin.php">Пункт4</a>
							</div>
			';
		}
		else
		{
			echo '
							<form name="test" method="post" action="admin.php">
								<input type="submit" name="but" value="enter1">
							</form>
							<div align="left">
								<a href="admin.php">Пункт1</a><br>
								<a href="admin.php">Пункт2</a><br>
								<a href="admin.php">Пункт3</a><br>
							</div>
			';
		}
		echo '
						</body>
					</head>
				</html>
			';
	}
?>
