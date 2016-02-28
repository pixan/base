<?php
namespace Pixan\Base;
class UserTransformer extends Transformer{
	
    public function transform($tag){
    	$transformation = [
			'id' => $tag['id'],
			'name' => $tag['name'],
			'email' => $tag['email'],
			'avatar' => $tag['avatar']
		];

		if(isset($tag->currentFriends)){
			$transformation['friends'] = $tag['currentFriends'];
		}

    	return $transformation;
    }

}