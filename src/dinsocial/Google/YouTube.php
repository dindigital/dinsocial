<?php

namespace dinsocial\Google;

use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Google_Service_YouTube_Video;
use Google_Service_Exception;
use Exception;

class YouTube
{

    /**
     * @TODO
     */

    private $youtube;
    
    public function __construct(Google_Client $client)
    {
        $this->youtube = new Google_Service_YouTube($client);
    }

    public function insert($file, $title, $description, $tags = array(), $privacy = "public")
    {
        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($title);
        $snippet->setDescription($description);
        if (count($tags)) {
            $snippet->setTags($tags['tags']);
        }
        $status = new Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = $privacy;
        $video = new Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);
        if (!is_file($file)) {
            throw new Exception('Problema com o caminho do arquivo');
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file);
        try {
            $obj = $this->youtube->videos->insert(
                "status,snippet", $video, array(
                    "data" => file_get_contents($file),
                    "mimeType" => $mime_type,
                    'uploadType' => 'multipart'
                )
            );
            return $obj->id;
        } catch (Google_Service_Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $this->youtube->videos->delete($id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}