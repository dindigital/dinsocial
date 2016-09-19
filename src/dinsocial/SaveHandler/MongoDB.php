<?php

namespace  dinsocial\SaveHandler;

/**
 * Class MongoDB
 * @package dinsocial\SaveHandler
 * @todo
 */
class MongoDB implements iSaveHandler
{

  public function writeToken($social, $token)
  {
    //$token = json_encode($token);
  }

  public function getToken($social)
  {
  }

}