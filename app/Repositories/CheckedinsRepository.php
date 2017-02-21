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
use Carbon\Carbon;
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
        return $this->getModel()->where('user_id',$userId)
            ->where('checked_out',null)->update(['checked_out'=>date('Y-m-d H:i:s')]);
    }

    public function checkHeart($userId)
    {
        return $this->getModel()->where('user_id',$userId)
            ->where('checked_out',null)->update([]);
    }

    public function autoCheckout()
    {
        $checkedIns = $this->getModel()->getTable();
        $now = Carbon::now();
        return $this->getModel()->select(DB::raw("TIMESTAMPDIFF(minute, $checkedIns.updated_at, '$now')"))
            ->where(DB::raw("TIMESTAMPDIFF(minute, $checkedIns.updated_at, '$now')"),'>','5')
            ->where('checked_out' , null)
            ->update(['checked_out'=>date('Y-m-d H:i:s')]);
    }

    public function deactivateCheckIn($user_id)
    {
        return DB::table('checked_ins')->where('user_id', $user_id)->delete();

    }



}