<?php

require '../../vendor/autoload.php';

use Dotenv\Dotenv;
use dinsocial\SaveHandler\FileSystem;
use dinsocial\SocialKeys;
use dinsocial\Google\Auth as GoogleAuth;

$dotenv = new Dotenv('../');
$dotenv->load();

$fileSystem = new FileSystem('../tokens');

$googleKey = new SocialKeys();
$googleKey->setClientId(getenv('GOOGLE_CLIENT_ID'));
$googleKey->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
$googleKey->setRedirectUri(getenv('GOOGLE_REDIRECT_URL'));

$googleAuth = new GoogleAuth($googleKey, $fileSystem);

if (isset($_GET['code'])) {
    $googleAuth->setCode($_GET['code']);
    header("location: /examples/google");
} else {

    try {

        $client = new Google_Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setAccessToken($googleAuth->getToken());

        $youtube = new Google_Service_YouTube($client);

        $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
            'mine' => 'true',
        ));

        $htmlBody = '<h1>YouTube</h1>';
        foreach ( $channelsResponse['items'] as $channel ) {
            $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

            $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
                'playlistId' => $uploadsListId,
                'maxResults' => 50
            ));

            foreach ( $playlistItemsResponse['items'] as $playlistItem ) {
                $htmlBody .= sprintf('<li>%s (%s)</li>', $playlistItem['snippet']['title'],
                    $playlistItem['snippet']['resourceId']['videoId']);
            }
            $htmlBody .= '</ul>';
        }

        echo $htmlBody;

        echo '<h1>Visualizações em uma página usando Analytics</h1>';
        $analytics = new \dinsocial\Google\Analytics($client);
        $analytics->setProperty('90420994');
        $analytics->setStartDate('2016-09-01');
        $analytics->setEndDate('2016-09-18');
        $analytics->setUri('/');
        echo $analytics->getVisits();

        #$youTube = new \dinsocial\Google\YouTube($client);
        #echo $youTube->insert('./foratemer.mov', 'Fora Temer', 'Busca no google sobre fora temer');

    } catch (\Exception $e) {

        echo '<a href="'.$googleAuth->getAuthorizationUrl().'">Acessar Google</a>';

    }

}

