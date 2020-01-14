<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InstaFeed;

class InstagramController extends Controller
{
    public function generatedToken()
    {
        $instaFeed = new InstaFeed;
        return $instaFeed->getShortLivedTokenUrl();
    }
}
