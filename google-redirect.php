<?php
require_once('google-api-settings.php');
require_once('google-login-api.php');

if(isset($_GET['code'])) {
    try {
        $gapi = new GoogleLoginApi();

        // Get the access token
        $data = $gapi->GetAccessToken(CLIENT_ID, CLIENT_REDIRECT_URL, CLIENT_SECRET, $_GET['code']);

        // Get user information
        $user_info = $gapi->GetUserProfileInfo($data['access_token']);
    }
    catch(Exception $e) {
        echo $e->getMessage();
        exit();
    }
}
?>
<head>
    <style type="text/css">

        #information-container {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #cccccc;
        }

        .information {
            margin: 0 0 30px 0;
        }

        .information label {
            display: inline-block;
            vertical-align: middle;
            width: 150px;
            font-weight: 700;
        }

        .information span {
            display: inline-block;
            vertical-align: middle;
        }

        .information img {
            display: inline-block;
            vertical-align: middle;
            width: 100px;
        }

    </style>
</head>

<body>

<div id="information-container">
    <div class="information">
        <label>İsim </label><span><?= $user_info['name'] ?></span>
    </div>
    <div class="information">
        <label>Google ID </label><span><?= $user_info['id'] ?></span>
    </div>
    <div class="information">
        <label>E-Posta</label><span><?= $user_info['email'] ?></span>
    </div>
    <div class="information">
        <label>E-Posta Doğrulandı mı ?</label><span><?= $user_info['verified_email'] == true ? 'Evet' : 'Hayır' ?></span>
    </div>
    <div class="information">
        <label>Resim</label><img src="<?= $user_info['picture'] ?>" />
    </div>
</div>

</body>
</html>