<?
if (isset($_FILES['photo']) ) {
  $files = $_FILES['photo'];
  $directory = $_GET['number'];
  $path = __DIR__ . '/photo-stamps/' . $directory;
  if (!file_exists($path) ) {
     mkdir($path, 0777, true);
  }

  $infolder = scandir($path);
  foreach ($infolder as $key => $value) {
    if ($value == '.' || $value == '..') {
      unset($infolder[$key]);
    }
  }
  $count_files = count($infolder);
  $file_number = $count_files + 1;
  $extension_file = mb_strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));

  for ($i = 0; $i <= $file_number; $i++) {
    $full_path = $path . '/' . $file_number . '.' . $extension_file;
    if (file_exists($full_path) ) {
      $file_number = $file_number + 1;
    }
  }

if ($files['size'] <= 350000) {
  if (move_uploaded_file($files['tmp_name'], $full_path) ) {
  //  echo '���� ��������';
  } else {
    echo '�� ������� ��������� ����';
  }
} else {
  echo '���� ���������� ������ ������ 350�� - �������� ���� �������� �������! ';
}

} else {
  echo '����� ��������� �����������';
}

?>
