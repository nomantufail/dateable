<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\Models\CheckedIn;
use App\User;
use Illuminate\Support\Facades\DB;

class CheckedinsRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(new CheckedIn());
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function checkoutPreviousCheckIns($userId)
    {
        return $this->getModel()->where('user_id',$userId)->update(['checked_out'=>date('Y-m-d H:i:s')]);
    }


}