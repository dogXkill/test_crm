<?
$file = $_POST['file'];
$path = 'result_files_img' . $file;
unlink($path);
echo 'Файл результата работ ' . $path . ' удален';

?>