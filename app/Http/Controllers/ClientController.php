<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InstaFeed\ClientHandler;

class ClientController extends Controller
{
    public function updateClient(Request $request)
    {
        return ClientHandler::updateClient($request);
    }
    public function isClientSaved()
    {
        return ClientHandler::isClientSaved();
    }
}
