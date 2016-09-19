<?php

namespace dinsocial\Facebook;

use dinsocial\Exceptions\InvalidPermissionException;
use dinsocial\Exceptions\InvalidPostException;
use Facebook\Facebook;

class PageFeed
{

  private $facebook;

  public function __construct ( Facebook $facebook )
  {
    $this->facebook = $facebook;
  }

  public function postPageFeed ( $page, $url, $name, $picture = null, $description = '', $message = '' )
  {

    $response = $this->facebook->get('/me/accounts');
    $body = $response->getDecodedBody();

    $page_access_token = null;
    foreach ( $body['data'] as $account ) {
      if ( $account['id'] == $page ) {
        $page_access_token = $account['access_token'];
      }
    }

    if (is_null($page_access_token)) {
      throw new InvalidPermissionException('Usuário não tem permissãoo para editar esta página');
    }

    $linkData = [
      'link' => $url,
      'message' => $message,
    ];

    try {

      $request = $this->facebook->request(
        'POST',
        '/' . $page . '/feed',
        $linkData,
        $page_access_token
      );

      $response = $this->facebook->getClient()->sendRequest($request);

      $graphNode = $response->getGraphNode();
      return $graphNode['id'];

    } catch (\Exception $e) {

      throw new InvalidPostException('Não foi possível publicar no facebook');

    }

  }

}