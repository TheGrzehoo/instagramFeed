<?php

namespace App;

class InstaFeed
{
    private $appID = 609591429775722;
    private $appSecret = 'fea0a73531076f51a4a1ee29512f668e';
    private $redirectUrl = 'https://127.0.0.1:8000/tokenHandler';

    public function getMediaData()
    {
    }

    public function getMediaIDs()
    {
    }

    public function getLongLivedToken(String $shortLiveToken)
    {

    }

    public function getShortLivedTokenUrl()
    {
        $testUrl = 'https://api.instagram.com/oauth/authorize
            ?client_id='.$this->appID.'
            &redirect_uri='.$this->redirectUrl.'
            &scope=user_profile,user_media
            &response_type=code';
        return $testUrl;
    }
}
