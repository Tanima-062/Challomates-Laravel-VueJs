<!DOCTYPE html>
<html>
<!-- Video.js base CSS -->
<link
  href="https://unpkg.com/video.js@7/dist/video-js.min.css"
  rel="stylesheet"
/>

<!-- City -->
<link
  href="https://unpkg.com/@videojs/themes@1/dist/city/index.css"
  rel="stylesheet"
/>

<head>

</head>

<body>
    <div style="display: flex; align-items:center">
        <video id="video-frame" class="video-js vjs-theme-city" controls autoplay style="width: 100%; height: 365px;">
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a
                web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
        </video>
    </div>
    <script>
        const stream_id = {{ $stream_id }};
        const user_id = {{ $user_id }};
        const csrf = @json(csrf_token());
    </script>
    <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
    <script src={{ mix('/js/stream.js') }}></script>

</body>

</html>

{{--
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />
<title>videojs-contrib-hls embed</title> --}}

  <!--

  Uses the latest versions of video.js and videojs-http-streaming.

  To use specific versions, please change the URLs to the form:

  <link href="https://unpkg.com/video.js@6.7.1/dist/video-js.css" rel="stylesheet">
  <script src="https://unpkg.com/video.js@6.7.1/dist/video.js"></script>
  <script src="https://unpkg.com/@videojs/http-streaming@0.9.0/dist/videojs-http-streaming.js"></script>

  -->

  {{-- <link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
</head>
<body>
  <h1>Video.js Example Embed</h1>

  <video-js id="my_video_1" class="vjs-default-skin" controls preload="auto" width="640" height="268">
    <source src="http://localhost/live/4033/index.m3u8" type="application/x-mpegURL">
  </video-js>

  <script src="https://unpkg.com/video.js/dist/video.js"></script>
  <script src="https://unpkg.com/@videojs/http-streaming/dist/videojs-http-streaming.js"></script>

  <script>
    var player = videojs('my_video_1');
  </script>

</body>
</html> --}}
