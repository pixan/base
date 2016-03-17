<?php
namespace Pixan\Base;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Response;
use Pagination;
use Config;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiController extends Controller
{

	protected $statusCode = 200;

	public $response;
	public function __construct(){
		$this->response = new \stdClass();
		$this->response->status 	= 'error';
		$this->response->code 		= 500;
		$this->response->messages 	= array();
		$this->response->data 		= new \stdClass();
	}
	public function getStatusCode(){
		return $this->statusCode;
	}

	public function setStatusCode($statusCode){
		$this->statusCode = $statusCode;
		$this->response->code = $statusCode;
		if(2 == (int)floor($this->response->code / 100)){
			$this->response->status = 'ok';
		}
		else{
			$this->response->status = 'error';	
		}
		return $this;
	}

	public function setMessages($messages){
		$this->response->messages = $messages;
		return $this;
	}

	public function respondWithValidationErrors($errors){
		return $this->respondWithErrors($errors, SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
	}

	public function respondWithErrors($errors, $statusCode = SymfonyResponse::HTTP_BAD_REQUEST){
		$this->setStatusCode($statusCode);
		$this->response->messages = $errors;
		return $this->respond();
	}

	public function respondCreated($object){
		$this->response->data = $object;
		return $this->setStatusCode(SymfonyResponse::HTTP_CREATED)->respond();
	}

	public function respondNotFound($messages = ["id" => ["Entity does not exist"]]){
		$this->response->messages = $messages;
		return $this->setStatusCode(SymfonyResponse::HTTP_NOT_FOUND)->respond();
	}

	public function respondWithData($data){
		$this->response->data = $data;

		return $this->setStatusCode(SymfonyResponse::HTTP_OK)->respond();
	}

	public function respond($headers =[]){
		return Response::json($this->response, $this->getStatusCode(), $headers);
	}

}
