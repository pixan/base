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

class UsersController extends ApiController {

    protected $userTransformer;

    function __construct(UserTransformer $userTransformer){
        parent::__construct();
        $this->userTransformer = $userTransformer;
    }

	public function signup(Request $request){
        $rules          = array(
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required',
            'avatar'        => 'image',
            'lat'           => 'required',
            'lon'           => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return $this->respondWithValidationErrors($validator->messages());
        }
        else{
            
            $user = new User;
            $user->name     = $request->get('name');
            $user->email    = $request->get('email');
            $user->password = Hash::make($request->get('password'));

            $user->street_line_1    = $request->get('street_line_1');
            $user->street_line_2    = $request->get('street_line_2');
            $user->zip              = $request->get('zip');
            $user->region           = $request->get('region');
            $user->city             = $request->get('city');
            $user->state            = $request->get('state');
            $user->lat              = $request->get('lat');
            $user->lon              = $request->get('lon');
            $user->country          = $request->get('country');

            $user->save();

            // Set user profile picture if request
            // contains a picture
            if( $request->hasFile('avatar') ){
                $this->setProfilePicture($user, $request);
            }
            $user = User::find($user->id);
            return $this->respondCreated(["user" => $this->userTransformer->transform($user->toArray())]);
        }
    }

    public function updateProfilePicture(Request $request, $id){
        $rules          = array(
            'avatar'        => 'required|image'
        );
        $validation = Validator::make($request->all(), $rules);
        if(!$validation->passes()){
            return $this->respondWithValidationErrors($validation->messages());
        }
        else{
            if( $request->hasFile('avatar') ){
                $user = User::findOrFail($id);
                $this->setProfilePicture($user, $request);
                return $this->setMessages([ "user_id" => ["Profile Picture was successfully updated"]])->respondWithData([]);
            }        
        }
    }

    public function setProfilePicture($user, $request) {
        $destinationPath = 'uploads/avatars'; // upload path
        if($user->id > 9){
            $location_string = implode('/',str_split(substr($user->id, 0, -1)));
        }
        else{
            $location_string = '0';
        }
        $destinationPath .= '/'.$location_string;

        $avatar_file = $request->file('avatar');
        $extension = $avatar_file->getClientOriginalExtension(); // getting image extension
        $fileName = $user->id.substr(md5($user->email), 0, 10).'.'.$extension; // renameing image
        $avatar_file->move($destinationPath, $fileName); // uploading file to given path
        $user->avatar = $fileName;
        $user->save();
    }

    public function update($user_id, Request $request){
        $rules          = array(
            'name'          => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return $this->respondWithValidationErrors($validator->messages());
        }

        $user = User::findOrFail($user_id);
        $user->update($request->only('name', 'street_line_1', 'street_line_2', 'zip', 'region', 'city', 'state', 'country', 'lat', 'lon'));

        return $this->setMessages([ "user_id" => ["User profile information was successfully updated"]])->respondWithData(['user' => $this->userTransformer->transform($user->toArray())]);
    }
    public function show($user_id, Request $request){
        $rules          = array(
            'user_id'          => 'required|exists:users,id',
        );
        $validator = Validator::make([ "user_id" => $user_id ], $rules);
        if($validator->fails()){
            return $this->respondWithValidationErrors($validator->messages());
        }

        $user = User::findOrFail($user_id);
        
        return $this->respondWithData(['user' => $this->userTransformer->transform($user)]);
    }

    public function friends($user_id, Request $request){
        $rules          = array(
            'user_id'          => 'required|exists:users,id',
        );
        $validator = Validator::make([ "user_id" => $user_id ], $rules);
        if($validator->fails()){
            return $this->respondWithValidationErrors($validator->messages());
        }
        
        $user = User::findOrFail($user_id);
        $friends = $user->currentFriends;
        return $this->respondWithData(['friends' => $this->userTransformer->transformCollection($friends->toArray())]);
    }

    public function search($term){

        $users = User::where(function($query) use ($term) {
            $query->where('name', 'like', "%$term%");
            $query->orWhere('email', $term);
        })->get();

        return $this->respondWithData(['users' => $this->userTransformer->transformCollection($users->toArray())]);
    }



}