<?php

namespace  dinsocial\SaveHandler;

use dinsocial\Exceptions\TokenNotFoundException;

class FileSystem implements iSaveHandler
{

  private $path;

  public function __construct ($path)
  {
    $this->path = $path;
  }

  public function writeToken($social, $token)
  {

    $token = json_encode($token);

    $file = fopen("{$this->path}/{$social}.json", "w") or die("Unable to create token file");
    fwrite($file, $token);
    fclose($file);

  }

  public function getToken($social)
  {

    $file = "{$this->path}/{$social}.json";

    if (!is_file($file)) {
      throw new TokenNotFoundException('Token não existe');
    }

    $file = file_get_contents($file);
    $token = json_decode($file);

    return $token;

  }

}