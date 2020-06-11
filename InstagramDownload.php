<?php

class InstagramDownload
{

    public $result;
    private $curl;

    public function __construct()
    {
        $this->curl = curl_init();

        $doc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML($this->do($_GET['url']));

        $metas = $doc->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:video' || $meta->getAttribute('property') == 'og:image') {
                $media_url = $meta->getAttribute('content');
            }
        }

        if ($media_url) {
            $media_url = substr($media_url, 0, strpos( $media_url, '?'));
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $media_url . "\"");
            readfile($media_url);
        } else {
            echo 'Media not found';
        }
    }

    private function do(string $url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $this->result = curl_exec($this->curl);
        return $this->result;
    }

    public function __destruct()
    {
        $this->curl = curl_close($this->curl);
    }

}

(new InstagramDownload());