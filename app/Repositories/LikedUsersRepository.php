<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\Models\LikedUser;
use Illuminate\Support\Facades\DB;

class LikedUsersRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(new LikedUser());
    }

    public function removeLikesByObjectId($user_id)
    {
        return $this->getModel()
            ->where('object_id', $user_id)->delete();
    }




}