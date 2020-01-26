<?php

namespace App\InstaFeed;

use Exception;
use App\InstaData;
use App\Client;
use Illuminate\Support\Facades\Storage;

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

        // walidacja AppID prawdopodobnie niemożliwa, przekierowanie zawsze na forceLogin
        // $curl = curl_init($tokenUrl);
        // curl_setopt($curl, CURLOPT_HEADER, true);
        // curl_setopt($curl, CURLOPT_NOBODY, true);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // $response = curl_exec($curl);
        // $status = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        // curl_close($curl);
        // return [
        //     'error' => true,
        //     'data' => 'invalid client data',
        //     'status' => $status
        // ];

        return [
            'error' => false,
            'data' => $tokenUrl,
        ];
    }

    public function getMedia()
    {
        $longLivedToken = $this->getLongLivedTokenFromDb();
        if ($longLivedToken != false) {
            $mediaData = $this->getMediaData($longLivedToken);
            return [
                'error' => false,
                'message' => $this->saveMediaToFile($mediaData),
            ];
        } else {
            return [
                'error' => true,
                'message' => 'Token not found',
            ];
        }
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
        return Storage::get("media.json");
    }

    private function saveMediaToFile($media)
    {
        Storage::put('media.json', json_encode($media));
        return 'Media saved properly';
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
        try {
            $tokenToStore = InstaData::where('isExpired', 0);
            return $tokenToStore->get()[0]->longLivedToken;
        } catch (Exception $e) {
            return false;
        }
    }
}
