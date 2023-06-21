<!DOCTYPE HTML>

<html>

<head>
  <title>Untitled</title>
</head>
<script type="text/javascript" src="../../includes/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.js"></script>
<body>
<div id="allow">▲ ▲ ▲ Разрешите использовать камеру ▲ ▲ ▲ <br/> ( Сверху текущей страницы )</div>

<div class="item">
  <span> video </span>
  <video id="video" width="160" height="120" autoplay="autoplay" ></video>
</div>
<div class="item">
  <span> canvas </span>
  <canvas id="canvas" width="160" height="120" ></canvas>
</div>

<input id="button" type="button" value="Жми!" />



<script>
  window.onload = function () {
    var canvas = document.getElementById('canvas');
    var video = document.getElementById('video');
    var button = document.getElementById('button');
    var allow = document.getElementById('allow');
    var context = canvas.getContext('2d');
    var videoStreamUrl = false;

    // функция которая будет выполнена при нажатии на кнопку захвата кадра
    var captureMe = function () {
      if (!videoStreamUrl) alert('То-ли вы не нажали "разрешить" в верху окна, то-ли что-то не так с вашим видео стримом')
      // переворачиваем canvas зеркально по горизонтали (см. описание внизу статьи)
      context.translate(canvas.width, 0);
      context.scale(-1, 1);
      // отрисовываем на канвасе текущий кадр видео
      context.drawImage(video, 0, 0, video.width, video.height);
      // получаем data: url изображения c canvas
      var base64dataUrl = canvas.toDataURL('image/png');
      context.setTransform(1, 0, 0, 1, 0, 0); // убираем все кастомные трансформации canvas
      // на этом этапе можно спокойно отправить  base64dataUrl на сервер и сохранить его там как файл (ну или типа того)
      // но мы добавим эти тестовые снимки в наш пример:
      var img = new Image();
      img.src = base64dataUrl;
    $('#txt').val(base64dataUrl);
    image = encodeURIComponent(base64dataUrl)

    //var base64dataUrl = encodeURIComponent('+');
    var geturl;
    geturl = $.ajax({
    type: "POST",
    url: 'upload_image.php',
	data : '&image='+image,
    success: function () {
    resp = geturl.responseText

   $('#txt').val(resp);
    $('#link').html("<a href=\"image.php?id="+resp+"\" target=\"_blank\">ссылка</a>");


}
})



      window.document.body.appendChild(img);
    }

    button.addEventListener('click', captureMe);

    // navigator.getUserMedia  и   window.URL.createObjectURL (смутные времена браузерных противоречий 2012)
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    window.URL.createObjectURL = window.URL.createObjectURL || window.URL.webkitCreateObjectURL || window.URL.mozCreateObjectURL || window.URL.msCreateObjectURL;

    // запрашиваем разрешение на доступ к поточному видео камеры
    navigator.getUserMedia({video: true}, function (stream) {
      // разрешение от пользователя получено
      // скрываем подсказку
      allow.style.display = "none";
      // получаем url поточного видео
      videoStreamUrl = window.URL.createObjectURL(stream);
      // устанавливаем как источник для video
      video.src = videoStreamUrl;
    }, function () {
      console.log('что-то не так с видеостримом или пользователь запретил его использовать :P');
    });
  };
</script>

 <textarea name="txt" id="txt" cols="30" rows="10"></textarea>

 <br>
 <span id="link"></span>

</body>

</html>