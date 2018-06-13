<!DOCTYPE html>
<html>
<head><title>Qiniu Web Player in HTML5</title></head>
<link href="https://player.qiniucc.com/sdk/latest/qiniuplayer.min.css" rel="stylesheet">
<script src="https://player.qiniucc.com/sdk/latest/qiniuplayer.min.js"></script>
<body>
<h1>Qiniu Web Player</h1>
<video id="demo-video" class="video-js vjs-big-play-centered"></video>
</body>

<script type="text/javascript">
    var options = {
        controls: true,
        url: 'http://ozi3rz0v1.bkt.clouddn.com/GAiY3FzEgg-GntIkyhxLtBIs8FA=/luAfRfEvnkmwxnovCt5fS648RU8G',
        type: 'hls',
        preload: true,
        autoplay: false // 如为 true，则视频将会自动播放
    };
    var player = new QiniuPlayer('demo-video', options);
</script>
</html>
