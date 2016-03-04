<?php

use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\FacebookSDKException;
use Facebook\Entities\AccessToken;
use App\User;

if ( ! function_exists('createOrUpdateFbCustomer')){
    function createOrUpdateFbCustomer($facebook_token, $client_id)
    {

        //Your FB applion app id and secret (normally in the config file of the package) 

        $fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        //$response = $fb->get('/me?fields=id,name', '');
        $fb->setDefaultAccessToken($facebook_token);

        $oauth_client = $fb->getOAuth2Client();
        
        $token = $oauth_client->getLongLivedAccessToken($facebook_token);


        $response = $fb->get('/me?fields=id,name,picture', $token);
        $facebook_user = $response->getGraphUser();
        
        if(!($user = User::where('facebook_id', $facebook_user->getId())->first() ) ){
            $user = new User;
        }
        $user->name         = $facebook_user->getName();
        $user->facebook_id  = $facebook_user->getId();
        $user->avatar       = $facebook_user->getPicture()->getUrl();
        $user->save();

        return $user->id;
        
    }
}

if ( ! function_exists('getFolderPathForId')){
    function getFolderPathForId($id){
        if($id < 10)
        {
            return "0/";
        }
        else{
            $folders = str_split($id);
            array_pop($folders);
            return implode("/", $folders)."/";
        }
    }
}