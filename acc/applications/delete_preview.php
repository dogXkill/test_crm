<?
$file = $_POST['file'];
$path = 'preview_img' . $file;
unlink($path);
echo '���� ������ ' . $path . ' ������';




?>
