<?php
require '../bayphp/bay.php';

echo "<pre>\n";
try
{
	$login = 'login';
	$password = 'password';
	
	$account = new bay\Account($login, $password);
	$filesCount = $account->getFilesCount();
	
	if ($filesCount == 0)
	{
		echo "You do not have any files.\n";
	}
	else
	{
		echo "You have $filesCount files:\n";
		foreach ($account->getFiles() as $file)
		{
			echo $file->getName(), ' - ', $file->getSize(), " octets\n";
		}
	}
	
	$account->logout();
}
catch (bay\Exception $ex)
{
	echo "Something went wrong:\n$ex\n";
}
echo '</pre>';
?>
