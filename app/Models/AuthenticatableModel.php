<?php

namespace App\Models;

use App\Traits\Authenticatable;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class AuthenticatableModel extends Model
{
    use Authenticatable;
}
