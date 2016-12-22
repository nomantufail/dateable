<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\User;

class UsersRepository extends Repository
{
    public function __construct()
    {
        $this->setModel(new User());
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function store($user)
    {
        $user->save();
        return $user;
    }

    public function findByEmail($email)
    {
        return  $this->getModel()->where('email', $email)->first();
    }

    public function getByIds($ids = [])
    {
        return  $this->getModel()->whereIn('id', $ids)->get();
    }

    public function findByFbId($fbid)
    {
        return $this->getModel()->where('fb_id',$fbid)->first();
    }

    public function findByToken($token)
    {
        return  $this->getModel()->where('access_token', $token)->first();
    }

    public function franchises()
    {
        return $this->getModel()->where('role', 2)->with('info')->get();
    }
    public function customers()
    {
        return $this->getModel()->where('role', 3)->get();
    }
    public function admins()
    {
        return $this->getModel()->where('role', 1)->get();
    }
}