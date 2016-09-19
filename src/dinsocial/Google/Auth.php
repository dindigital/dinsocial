<?php

namespace dinsocial\Google;

use dinsocial\SaveHandler\iSaveHandler;
use dinsocial\SocialKeys;
use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Provider\Google;
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

    $this->provider = new Google([
      'clientId'          => $socialKeys->getClientId(),
      'clientSecret'      => $socialKeys->getClientSecret(),
      'redirectUri'       => $socialKeys->getRedirectUri(),
      'accessType'        => 'offline',
    ]);

    $this->saveHandler = $saveHandler;

  }

  public function getAuthorizationUrl()
  {

    $authUrl = $this->provider->getAuthorizationUrl([
      'approval_prompt' => 'force',
      'scope' => ['https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/analytics.readonly'],
    ]);

    return $authUrl;

  }

  public function setCode($code)
  {

    try {

      $token = $this->provider->getAccessToken('authorization_code', [
        'code' => $code
      ]);

      $googleToken = array(
        'access_token' => $token->getToken(),
        'refresh_token' => $token->getRefreshToken(),
        'expires' => $token->getExpires(),
      );

      $this->saveHandler->writeToken('google', $googleToken);

    } catch (\Exception $e) {
      throw new InvalidCodeException('Código Token Inválido');
    }

  }

  public function getToken()
  {

    $savedToken = $this->saveHandler->getToken('google');

    if (!$savedToken) {
      throw new TokenNotFoundException('Token não encontrado');
    }

    $token = new AccessToken(array(
      'access_token' => $savedToken->access_token,
      'refresh_token' => $savedToken->refresh_token,
      'expires' => $savedToken->expires,
    ));

    if ( $token->hasExpired() ) {

      try {

        $grant = new RefreshToken();
        $refresh = $this->provider->getAccessToken($grant, ['refresh_token' => $token->getRefreshToken()]);

        $savedToken = array(
          'access_token' => $refresh->getToken(),
          'refresh_token' => $savedToken->refresh_token,
          'expires' => $refresh->getExpires(),
        );

        $this->saveHandler->writeToken('google', $savedToken);

      } catch (\Exception $e) {

        throw new InvalidTokenException('Token inválido');

      }

    }

    $token = $this->saveHandler->getToken('google');

    return array(
        'access_token' => $token->access_token,
        'refresh_token' => $token->refresh_token,
        'expires_in' => $token->expires
    );

  }

}