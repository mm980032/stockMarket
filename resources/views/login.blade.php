<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Line Login Demo</title>

    <!-- Bootstrap core CSS -->
    <link href="https://bootstrap.hexschool.com/docs/4.2/dist/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://bootstrap.hexschool.com/docs/4.2/examples/floating-labels/floating-labels.css" rel="stylesheet">
</head>
<body>
<form class="form-signin">
    <div class="text-center mb-4">
        <!-- <a href="https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2004719903&redirect_uri=http://stockmarket.com/api/line/callback/token&state=test&scope=profile%20openid%20email&prompt=consent&bot_prompt=">1234567</a> -->
        <a href="https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2004719903&redirect_uri=http://stockmarket.com/api/line/callback/token&state=test&scope=profile%20openid%20email&bot_prompt=aggressive">1234567</a>
        @if($friendStatus == 'false')
        <a href="https://lin.ee/ZPmljzj"><img src="https://scdn.line-apps.com/n/line_add_friends/btn/zh-Hant.png" alt="加入好友" height="36" border="0"></a>
        @endif
    </div>
    <p class="mt-5 mb-3 text-muted text-center">Yulin &copy; 2019</p>
</form>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>