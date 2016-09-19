<?php

namespace dinsocial\Facebook;

use dinsocial\SaveHandler\iSaveHandler;
use dinsocial\SocialKeys;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Token\AccessToken;
use dinsocial\Exceptions\InvalidCodeException;
use dinsocial\Exceptions\InvalidTokenException;
use dinsocial\Exceptions\TokenNotFoundException;

class Auth
{

  private $provider;
  private $saveHandler;

  public function __construct(SocialKeys $socialKeys, iSaveHandler $saveHandler)
  {

    $this->provider = new Facebook([
      'clientId'          => $socialKeys->getClientId(),
      'clientSecret'      => $socialKeys->getClientSecret(),
      'redirectUri'       => $socialKeys->getRedirectUri(),
      'graphApiVersion'   => 'v2.6',
    ]);

    $this->saveHandler = $saveHandler;

  }

  public function getAuthorizationUrl()
  {

    $authUrl = $this->provider->getAuthorizationUrl([
      'scope' => ['manage_pages', 'publish_pages'],
    ]);

    return $authUrl;

  }

  public function setCode($code)
  {

    try {

      $token = $this->provider->getAccessToken('authorization_code', [
        'code' => $code
      ]);

      $tokenLongLived = $this->provider->getLongLivedAccessToken($token->getToken());

      $facebookToken = array(
        'access_token' => $tokenLongLived->getToken(),
        'refresh_token' => $tokenLongLived->getRefreshToken(),
        'expires' => $tokenLongLived->getExpires(),
      );

      $this->saveHandler->writeToken('facebook',  $facebookToken);

    } catch (\Exception $e) {
      throw new InvalidCodeException('Código Token Inválido');
    }

  }

  public function getToken()
  {

    $savedToken = $this->saveHandler->getToken('facebook');

    if (!$savedToken) {
      throw new TokenNotFoundException('Token não encontrado');
    }

    return array(
        'access_token' => $savedToken->access_token,
        'refresh_token' => $savedToken->refresh_token,
        'expires_in' => $savedToken->expires
    );
    
  }

}