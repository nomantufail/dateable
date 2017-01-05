<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\Models\BlockedUser;
use App\Models\LikedUser;
use Illuminate\Support\Facades\DB;

class BlockedUsersRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(new BlockedUser());
    }

    public function removeBlockedUserByObjectId($user_id)
    {
        return DB::table('blocked_users')->where('object_id', $user_id)
            ->orWhere('subject_id', $user_id)->delete();

    }

}