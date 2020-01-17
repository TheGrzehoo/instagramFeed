<?php

namespace App\InstaFeed;

use Exception;
use App\InstaData;
use App\Client;

class InstaFeed
{  
    private $appSecret;
    private $appID;
    private $redirectUrl;
    function __construct()
    {
        $clientData = Client::first();
        $this->appID = $clientData->appID;
        $this->appSecret = $clientData->appSecret;
        $this->redirectUrl = app('config')->get('instagram')['redirectUrl'];
    }

    public function clientCodeHandler()
    {
        $shortLivedToken =  $this->generateShortLivedToken();
        $longLivedTokenJSON = $this->generateLongLivedToken($shortLivedToken);
        $this->storeLongLivedToken($longLivedTokenJSON);
        return redirect('/');
    }
    
    public function getShortLivedTokenUrl()
    {
        
        // link do strony z potwierdzeniem, które generuje kod (automatyczne przekierowanie do redirectUrl)
        $tokenUrl = 'https://api.instagram.com/oauth/authorize?client_id=' .
            $this->appID . '&redirect_uri=' .
            $this->redirectUrl . '&scope=user_profile,user_media&response_type=code';

        return $tokenUrl;
    }

    public function getMedia()
    {
        $longLivedToken = $this->getLongLivedTokenFromDb();
        $mediaData = $this->getMediaData($longLivedToken);
        $this->saveMediaToFile($mediaData);
    }

    public function refreshAndStoreLongLivedToken()
    {
        $oldToken = $this->getLongLivedTokenFromDb();
        InstaData::where('longLivedToken', $oldToken)
        ->update(['isExpired' => 1]);

        $tokenGenerationUrl = 'https://graph.instagram.com/refresh_access_token' .
            '?grant_type=' . 'ig_refresh_token' .
            '&access_token=' . $oldToken;

        $curl = curl_init($tokenGenerationUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $generatedJSON = curl_exec($curl);
        curl_close($curl);
        $generatedLongLivedTokenJSON = json_decode($generatedJSON, true);
        $tokenStoreMsg = $this->storeLongLivedToken($generatedLongLivedTokenJSON);

        return redirect('/');
    }

    public function getMediaFromFile()
    {
        return file_get_contents("media.json");
    }

    private function saveMediaToFile($media){
        $fp = fopen('media.json', 'w');
        fwrite($fp, json_encode($media));
        fclose($fp);
    }

    private function getMediaData(String $token)
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

    private function generateLongLivedToken(String $shortLiveToken)
    {
        $tokenGenerationUrl = 'https://graph.instagram.com/access_token' .
            '?client_secret=' . $this->appSecret .
            '&grant_type=' . 'ig_exchange_token' .
            '&access_token=' . $shortLiveToken;

        $curl = curl_init($tokenGenerationUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $generatedJSON = curl_exec($curl);
        curl_close($curl);

        $generatedLongLivedToken = json_decode($generatedJSON, true);
        return $generatedLongLivedToken;
    }

    private function generateShortLivedToken()
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


    private function storeLongLivedToken($tokenJSON)
    {
        try {
            $tokenToStore = new InstaData();
            $tokenToStore->longLivedToken = $tokenJSON['access_token'];

            $currentTime = time();
            $expireTime = $currentTime + $tokenJSON['expires_in'];

            $tokenToStore->tokenExpire = $expireTime;
            $tokenToStore->isExpired = false;

            $tokenSaved = $tokenToStore->save();

            $returnMessage = $tokenSaved ? 'token saved properly' : 'token saving failed';
        } catch (Exception $e) {
            $returnMessage = $e;
        }

        return $returnMessage;
    }

    private function getLongLivedTokenFromDb()
    {
        $tokenToStore = InstaData::where('isExpired', 0);
        return $tokenToStore->get()[0]->longLivedToken;
    }
}
