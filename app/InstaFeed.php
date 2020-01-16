<?php

namespace App;

use Exception;
use App\InstaData;
use App\Client;

class InstaFeed
{
    private $appID = 609591429775722;
    private $appSecret = 'fea0a73531076f51a4a1ee29512f668e';
    private $redirectUrl = 'https://127.0.0.1:8000/clientCodeHandler';

    public function saveMediaToFile($media){
        $fp = fopen('media.json', 'w');
        fwrite($fp, json_encode($media));
        fclose($fp);
    }

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
        $clientData = Client::find(1);
        $appSecret = $clientData->appSecret;
        $tokenGenerationUrl = 'https://graph.instagram.com/access_token' .
            '?client_secret=' . $appSecret .
            '&grant_type=' . 'ig_exchange_token' .
            '&access_token=' . $shortLiveToken;

        $curl = curl_init($tokenGenerationUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $generatedJSON = curl_exec($curl);
        curl_close($curl);

        $generatedLongLivedToken = json_decode($generatedJSON, true);
        return $generatedLongLivedToken;
    }

    public function generateShortLivedToken()
    {
        $clientData = Client::find(1);
        $appID = $clientData->appID;
        $appSecret = $clientData->appSecret;

        $tokenGenerationUrl = 'https://api.instagram.com/oauth/access_token';
        $postTokenData = [
            'client_id' => $appID,
            'client_secret' => $appSecret,
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
        $clientData = Client::find(1);
        $appID = $clientData->appID;
        // link do strony z potwierdzeniem, które generuje kod (automatyczne przekierowanie do redirectUrl)
        $testUrl = 'https://api.instagram.com/oauth/authorize?client_id=' .
            $appID . '&redirect_uri=' .
            $this->redirectUrl . '&scope=user_profile,user_media&response_type=code';

        $link = '<a href="' . $testUrl . '">Link do akceptacji instagrama</a>';
        return $link;
    }

    public function storeLongLivedToken($tokenJSON)
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

    public function getLongLivedTokenFromDb()
    {
        $tokenToStore = InstaData::where('isExpired', 0);
        return $tokenToStore->get()[0]->longLivedToken;
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

        return 'token '.$tokenStoreMsg;

        // $tokenStored = InstaData::where('isExpired', 0)->get()[0]->longLivedToken;
        
        // return 'New token has been generated: ' . $tokenStored;
    }

    public function getMediaFromFile()
    {
        return file_get_contents("media.json");
    }
}
