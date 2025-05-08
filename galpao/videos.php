<!DOCTYPE html>
<html>
<head>
    <title>Câmera ao Vivo com HLS.js</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body>

    <h1>Visualização da Câmera ao Vivo</h1>

    <video id="video" width="640" height="480" controls autoplay muted></video>
    <script>
      if(Hls.isSupported()) {
        var video = document.getElementById('video');
        var hls = new Hls();
        hls.loadSource('/live/stream.m3u8'); // Certifique-se de que este caminho está correto para o seu servidor web
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED,function() {
          video.play();
      });
     }
     else if (video.canPlayType('application/vnd.apple.mpegurl')) {
       video.src = '/live/stream.m3u8'; // Certifique-se de que este caminho está correto para o seu servidor web
       video.addEventListener('loadedmetadata',function() {
         video.play();
       });
     }
    </script>

</body>
</html>