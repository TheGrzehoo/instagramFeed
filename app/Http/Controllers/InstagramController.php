<?php

namespace App\Http\Controllers;

use App\ClientHandler;
use Illuminate\Http\Request;
use App\InstaFeed;

class InstagramController extends Controller
{
    public function generateToken()
    {
        $instaFeed = new InstaFeed;
        return $instaFeed->getShortLivedTokenUrl();
    }
    public function clientCodeHandle()
    {
        $instaFeed = new InstaFeed;
        $shortLivedToken =  $instaFeed->generateShortLivedToken();
        $longLivedTokenJSON = $instaFeed->generateLongLivedToken($shortLivedToken);
        $instaFeed->storeLongLivedToken($longLivedTokenJSON);
        return 'long lived token generated <a href="/refreshToken">ośwież token</a>';
    }
    public function getMedia()
    {
        $instaFeed = new InstaFeed;
        $longLivedToken = $instaFeed->getLongLivedTokenFromDb();
        $mediaData = $instaFeed->getMediaData($longLivedToken);
        $instaFeed->saveMediaToFile($mediaData);
        return 'Media saved to file';
    }

    public function refreshToken()
    {
        $instaFeed = new InstaFeed;
        $msg = $instaFeed->refreshAndStoreLongLivedToken();
        return $msg;
    }

    public function getMediaFromFile()
    {
        $instaFeed = new InstaFeed;
        return $instaFeed->getMediaFromFile();
    }

    public function saveUserData()
    {
        $client = new ClientHandler;
        return $client->saveClientData();
    }
}
