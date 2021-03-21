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

        $url = trim($_GET['url']);
        $doc->loadHTML($this->do($url));

        $metas = $doc->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:video' || $meta->getAttribute('property') == 'og:image') {
                $mediaUrl = $meta->getAttribute('content');
            }
        }

        if ($mediaUrl) {
          $mediaUrlName = substr($mediaUrl, 0, strpos($mediaUrl, '?'));

          header('Content-Description: File Transfer');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename="' . $mediaUrlName . '"');
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          header('Content-Length: ' . filesize($mediaUrl));
          readfile($mediaUrl);

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
