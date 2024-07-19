<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function storeUniqueImage($image, $path)
    {
        $ImageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs($path, $ImageName, 'public');
    }
}
