<?php
class wb{
  function connect(){
    $url = "https://statistics-api.wildberries.ru/public/api/v1/info";
    $dateFormat=date('Y-m-d');
    $headers = [
        'Content-Type: application/json',
        'Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6IjdlYjE3OTFiLTJkZDAtNDI5YS1hZDY0LWEwNDBiNWVhZmI3MCJ9.GMvVChR6QU5iIPwn5doMiS8NARutwCMGQSEwTfFqY6Q'
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
         CURLOPT_HEADER => false,
        CURLOPT_CUSTOMREQUEST =>'POST'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    var_dump($result); exit();
  }
  function get_card(){
    $url = "https://statistics-api.wildberries.ru/public/api/v1/info";
    $dateFormat=date('Y-m-d');
    $headers = [
        'Content-Type: application/json',
        'Authorization:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6IjdlYjE3OTFiLTJkZDAtNDI5YS1hZDY0LWEwNDBiNWVhZmI3MCJ9.GMvVChR6QU5iIPwn5doMiS8NARutwCMGQSEwTfFqY6Q'
    ];


    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
         CURLOPT_HEADER => false,
        CURLOPT_CUSTOMREQUEST =>'POST'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    var_dump($result); exit();
  }
}
 ?>
