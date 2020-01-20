<?php

namespace App\InstaFeed;

use Exception;
use App\Client;
use App\InstaData;
use Illuminate\Support\Facades\Log;

class ClientHandler
{
  public static function updateClient($request)
  {
    $appID = $request['appID'];
    $appSecret = $request['appSecret'];

    try {
      if (count(Client::all())) {
        $client = Client::first();
      } else {
        $client = new Client();
      }
      $client->appID = $appID;
      $client->appSecret = $appSecret;
      $error = $client->save();
      InstaData::whereNotNull('id')->delete();
      $message = [
        'error' => $error,
        'message' => $error ? 'Client updated successfully' : 'Database error occured. Try again'
      ];
    } catch (Exception $e) {
      $message = 'Database error occured. Try again.' . $e;
    }

    return $message;
  }

  public static function isClientSaved(){
    if (count(Client::all())) {
      return 'true';
    } else {
      return 'false';
    }
  }
}
