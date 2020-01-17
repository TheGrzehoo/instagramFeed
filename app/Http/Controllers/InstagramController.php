<?php

namespace App\Http\Controllers;

use App\ClientHandler;
use Illuminate\Http\Request;
use App\InstaFeed;

class InstagramController extends Controller
{
    public $instaFeed;
    function __construct()
    {
        $this->instaFeed = new InstaFeed();
    }
    public function generateToken()
    {
        return $this->instaFeed->getShortLivedTokenUrl();
    }
    public function clientCodeHandle()
    {
        return $this->instaFeed->clientCodeHandler();
    }
    public function getMedia()
    {
        return $this->instaFeed->getMedia();
    }

    public function refreshToken()
    {
        return $this->instaFeed->refreshAndStoreLongLivedToken();
    }

    public function getMediaFromFile()
    {
        return $this->instaFeed->getMediaFromFile();
    }
}
