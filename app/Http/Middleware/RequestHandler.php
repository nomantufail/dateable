<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Requests\Request;
use Closure;
use App\Http\Response;

class RequestHandler
{
    private $response;
    private $validationMessages = [];
    public function __construct()
    {
        $this->response = new Response();
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $customRequest = "")
    {
        $requestClass = "Requests\\".$customRequest;
        /** @var Request $customRequest */
        $customRequest = new $requestClass();
        if(!$customRequest->authorize())
            return $this->response->respondOwnershipConstraintViolation();
        if(!$this->validate($customRequest))
            return $this->response->respondValidationFails($this->validationMessages);

        return $next($request);
    }

    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), $request->rules(), $request->messages());
        if($validator->fails()){
            $this->validationMessages = $validator->errors();
            return false;
        }
        return true;
    }
}
