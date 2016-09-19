<?php

namespace dinsocial;

class SocialKeys
{

  protected $clientId;
  protected $clientSecret;
  protected $redirectUri;

  public function setClientId ($clientId)
  {
    $this->clientId = $clientId;
  }

  public function setClientSecret ($clientSecret)
  {
    $this->clientSecret = $clientSecret;
  }

  public function setRedirectUri ($redirectUri)
  {
    $this->redirectUri = $redirectUri;
  }

  public function getClientId ()
  {
    return $this->clientId;
  }

  public function getClientSecret ()
  {
    return $this->clientSecret;
  }

  public function getRedirectUri ()
  {
    return $this->redirectUri;
  }

}