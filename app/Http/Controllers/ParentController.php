<?php

namespace App\Http\Controllers;

use App\Http\Requests\FooRequest;

class ParentController extends Controller
{
    public function foo(FooRequest $request)
    {
        return 'hello';
    }
}
