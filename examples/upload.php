<?php
require '../bayphp/bay.php';
?><!DOCTYPE html>
<html>
	
	<head>
		<title>Upload example</title>
	</head>
	
	<body>
		<?php
		try
		{
			$login = 'login';
			$password = 'password';
			$account = new bay\Account($login, $password);
		
			$uploader = new bay\Uploader();
			$uploader->prepare($account);
		}
		catch (bay\Exception $ex)
		{
			echo $ex;
		}
		?>
		
		<form action="<?php echo $uploader->getUploadUrl(); ?>" method="post" enctype="multipart/form-data">
			<input type="file" name="file" />
			<input type="submit" />
		</form>
		
	</body>
	
</html>
