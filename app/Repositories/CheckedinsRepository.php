<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\Models\CheckedIn;
use Illuminate\Support\Facades\DB;

class CheckedinsRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(new CheckedIn());
    }
}