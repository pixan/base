<?php
namespace Pixan\Base;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Pixan\Base\UserTransformer;
use App\User;
use Hash;
use Validator;
use Response;
use Pagination;
use Config;
use LucaDegasperi\OAuth2Server\Authorizer;

class FriendsController extends ApiController {

    protected $userTransformer;
    protected $authorizer;

    function __construct(UserTransformer $userTransformer, Authorizer $authorizer){
        parent::__construct();
        $this->userTransformer = $userTransformer;
        $this->authorizer = $authorizer;
    }

	public function request(Request $request){
        
        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,id'
        ]);
        if($validator->fails())
            return $this->respondWithValidationErrors($validator->errors());
        
        $user_id    = $this->authorizer->getResourceOwnerId();
        $friend_id  = $request->get('friend_id');

        $user       = User::findOrFail($user_id);
        $friend     = User::findOrFail($friend_id);

        // Friend request already sent?
        if($user->currentFriends->contains($friend))
            return $this->respondWithErrors([
                'friend_id' => [
                    'User is already friends with the requested user'
                ]
            ]);
        if($user->friends->contains($friend))
            return $this->respondWithErrors([
                'friend_id' => [
                    'Friend request already sent'
                ]
            ]);

        $user->befriend($friend);
        return $this->setMessages([ "friend_request" => ["Friend request was successfully sent"]])->respondWithData([]);
    }

    public function accept(Request $request){

        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,id'
        ]);
        if($validator->fails())
            return $this->respondWithValidationErrors($validator->errors());

        $user_id    = $this->authorizer->getResourceOwnerId();
        $friend_id  = $request->get('friend_id');

        $user       = User::findOrFail($user_id);
        $friend     = User::findOrFail($friend_id);

        if($user->currentFriends->contains($friend))
            return $this->respondWithErrors([
                'friend_id' => [
                    'Friend request was already accepted'
                ]
            ]);
        $user->approve($friend);
        return $this->setMessages([ "friend_request" => ["Friend request was accepted"]])->respondWithData([]);
    }

    public function block(Request $request){

        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,id'
        ]);
        if($validator->fails())
            return $this->respondWithValidationErrors($validator->errors());
        $user_id    = $this->authorizer->getResourceOwnerId();
        $friend_id  = $request->get('friend_id');

        $user       = User::findOrFail($user_id);
        $friend     = User::findOrFail($friend_id);
        $user->block($friend);

    }

}