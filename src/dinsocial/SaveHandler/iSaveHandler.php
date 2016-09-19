<?php

namespace  dinsocial\SaveHandler;

interface iSaveHandler
{

  function writeToken($social, $token);
  function getToken($social);

}