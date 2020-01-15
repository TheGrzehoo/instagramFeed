<?php

namespace App;

class InstaFeed
{
    private $appID = 609591429775722;
    private $appSecret = 'fea0a73531076f51a4a1ee29512f668e';
    private $redirectUrl = 'https://127.0.0.1:8000/clientCodeHandler';

    public function getMediaData(String $token)
    {
        $tokenGenerationUrl = 'https://graph.instagram.com/me/media' .
            '?fields=' . 'id,permalink,media_url' .
            '&access_token=' . $token;

        $curl = curl_init($tokenGenerationUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $generatedJSON = curl_exec($curl);
        curl_close($curl);

        $mediaData = json_decode($generatedJSON, true)['data'];

        return $mediaData;
    }

    public function generateLongLivedToken(String $shortLiveToken)
    {
        $tokenGenerationUrl = 'https://graph.instagram.com/access_token' .
            '?client_secret=' . $this->appSecret .
            '&grant_type=' . 'ig_exchange_token' .
            '&access_token=' . $shortLiveToken;

        $curl = curl_init($tokenGenerationUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $generatedJSON = curl_exec($curl);
        curl_close($curl);

        $generatedLongLivedToken = json_decode($generatedJSON, true)['access_token'];
        return $generatedLongLivedToken;
    }

    public function refreshLongLivedToken(String $oldLongLivedToken)
    {
        $tokenGenerationUrl = 'https://graph.instagram.com/refresh_access_token' .
            '?grant_type=' . 'ig_refresh_token' .
            '&access_token=' . $oldLongLivedToken;

        $curl = curl_init($tokenGenerationUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $generatedJSON = curl_exec($curl);
        curl_close($curl);

        $generatedLongLivedToken = json_decode($generatedJSON, true)['access_token'];

        return $generatedLongLivedToken;
    }

    public function generateShortLivedToken()
    {
        $tokenGenerationUrl = 'https://api.instagram.com/oauth/access_token';
        $postTokenData = [
            'client_id' => $this->appID,
            'client_secret' => $this->appSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUrl,
            'code' => $_GET['code'],
        ];

        $curl = curl_init($tokenGenerationUrl);
        curl_setopt($curl, CURLOPT_POST, true); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postTokenData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));

        $generatedJSON = curl_exec($curl);
        curl_close($curl);

        $generatedShortLivedToken = json_decode($generatedJSON, true)['access_token'];
        return $generatedShortLivedToken;
    }

    public function getShortLivedTokenUrl()
    {
        // link do strony z potwierdzeniem, które generuje kod (automatyczne przekierowanie do redirectUrl)
        $testUrl = 'https://api.instagram.com/oauth/authorize?client_id=' .
            $this->appID . '&redirect_uri=' .
            $this->redirectUrl . '&scope=user_profile,user_media&response_type=code';

        $link = '<a href="' . $testUrl . '">Link do akceptacji instagrama</a>';
        return $link;
    }
}
