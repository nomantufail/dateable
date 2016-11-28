<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\Models\UserInterests;
use Illuminate\Support\Facades\DB;

class UserInterestsRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(new UserInterests());
    }
}