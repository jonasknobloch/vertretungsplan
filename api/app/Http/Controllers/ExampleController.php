<?php

namespace App\Http\Controllers;

class ExampleController extends Controller
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }

    public function delete() {
        // JSON.stringify([1, 2, 3]); -> $keys -> [1, 2, 3]
        $keys = $this->request->all();
        return $keys;
    }
}
