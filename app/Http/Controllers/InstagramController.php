<?php

namespace App\Http\Controllers;

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
        $longLivedToken = $instaFeed->generateLongLivedToken($shortLivedToken);
        $mediaData = $instaFeed->getMediaData($longLivedToken);
        return $mediaData;
    }
}
