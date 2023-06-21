<?
if (isset($_FILES["photo"])) {
  $files = $_FILES["photo"];

  $directory = $_GET['uid'];
  $path = __DIR__ . '/preview_img/' . $directory;
  if (!file_exists($path) ) {
     mkdir($path, 0777, true);
  }

  $infolder = scandir($path);
  foreach ($infolder as $key => $value) {
    if ($value !== '.' && $value !== '..' && $value !== '') {
      unlink($path . '/' . $value);
    }
  }

  $extension_file = mb_strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
  $full_path = $path . '/1.' . $extension_file;

  if ($extension_file == 'png' || $extension_file == 'jpg' || $extension_file == 'jpeg') {
    if ($files['size'] <= 600000) {
      if (move_uploaded_file($files['tmp_name'], $full_path) ) {
      } else {
        echo 'Не удалось сохранить файл';
      }
    } else {
      echo 'Файл превью больше 600кб - выберите файл меньшего размера! ';
    }
  } else {
    echo 'Неверное расширение файла превью - допустимые расширения: png, jpg/jpeg';
  }


} else {
  echo 'Файлы загружены некорректно';
}
?>
