<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/2000/REC-xhtml1-200000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo $title; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<style type="text/css">
		body {
			font-family: Verdana, serif;
			font-size: 10px;
		}
		h1 {
			color: #D1A522;
		}
		</style>
	</head>
	<body>
		<br />
		<h1>Ups! Lo sentimos ha ocurrido un error grave.</h1>
		<hr />
		<br />
		<p>Le sugerimos volver a intentar la acci&oacute;n que estaba realizando.<br />
		<a href="javascript:history.go(-1)">Regresar a la pagina que estaba viendo anteriormente.</a><br />
		</p>
		<br />
		<p><em><?php echo nl2br( $error ); ?></em></p>
	</body>
</html>