<?php

require '../../vendor/autoload.php';

use Dotenv\Dotenv;
use dinsocial\SaveHandler\FileSystem;
use dinsocial\SocialKeys;
use dinsocial\Facebook\Auth as FacebookAuth;

$dotenv = new Dotenv('../');
$dotenv->load();

$fileSystem = new FileSystem('../tokens');

$facebookKey = new SocialKeys();
$facebookKey->setClientId(getenv('FACEBOOK_CLIENT_ID'));
$facebookKey->setClientSecret(getenv('FACEBOOK_CLIENT_SECRET'));
$facebookKey->setRedirectUri(getenv('FACEBOOK_REDIRECT_URL'));

$facebookAuth = new FacebookAuth($facebookKey, $fileSystem);

if (isset($_GET['code'])) {
    $facebookAuth->setCode($_GET['code']);
    header("location: /examples/facebook");
} else {

    try {

        $fb = new \Facebook\Facebook([
            'app_id' => getenv('FACEBOOK_CLIENT_ID'),
            'app_secret' => getenv('FACEBOOK_CLIENT_SECRET'),
            'default_graph_version' => 'v2.7',
            'default_access_token' => $facebookAuth->getToken()['access_token']
        ]);

        $response = $fb->get('/me');
        $body = $response->getDecodedBody();

        echo '<h1>Facebook</h1>';
        echo '<p>Usuário: '.$body['name'].'</p>';

        echo '<h1>Facebook Post</h1>';
        $facebookPost = new \dinsocial\Facebook\PageFeed($fb);
        echo $facebookPost->postPageFeed('179034515474527', 'http://din.digital', 'DIN DIGITAL');

    } catch (\Exception $e) {

        echo $e->getMessage();
        echo '<br />';
        echo '<br />';
        echo '<a href="'.$facebookAuth->getAuthorizationUrl().'">Acessar facebook</a>';

    }

}

