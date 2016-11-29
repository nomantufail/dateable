<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\Models\UserInterests;
use App\User;
use Illuminate\Support\Facades\DB;

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

    public function countDatablesAtLocation($locationId, $userId)
    {
        return $this->getDatablesAtLocation($locationId,$userId)->count();
    }

    public function countCheckInsAtLocation($locationId)
    {
        return $this->getCheckInsAtLocation($locationId)->count();
    }

    public function getDatablesAtLocation($locationId, $userId)
    {
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        /** @var UserInterests $interests */
        $interests = $this->findById($userId)->interests;
        $usersTable = $this->getModel()->getTable();
        $query = $this->getModel()
            ->select($userFields)
            ->where($checkinsTable.".location_i",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->Where(function ($query)use ($interests, $usersTable, $userId){
                $this->QUERY_usersIamInterestedIn($query, $userId);
            });
        $results = $query->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->groupBy($userFields)
            ->get();

        return $results;
    }

    public function getCheckInsAtLocation($locationId)
    {
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        $usersTable = $this->getModel()->getTable();
        return $this->getModel()
            ->select($userFields)
            ->where($checkinsTable.".location_id",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->groupBy($userFields)
            ->get();
    }

    private function QUERY_usersIamInterestedIn($query, $userId)
    {
        $usersTable = $this->getModel()->getTable();
        $interests = $this->findById($userId)->interests;

        $query->where($usersTable.".id",'!=',$userId); /** excluding current logged in user. */

        /** Age matching */
        $query->where(DB::raw("DATEDIFF(CURDATE(), ".$this->getModel()->getTable().".birthday)/365"),'>=',$interests->age_min)
            ->Where(DB::raw("DATEDIFF(CURDATE(), ".$this->getModel()->getTable().".birthday)/365"),'<=',$interests->age_max);

        /** Gender Matching */
        if($interests->gender != 2){
            $query->where($this->getModel()->getTable().".gender",$interests->gender);
        }
        return $query;
    }

    private function QUERY_usersInterestedInMe($query, $userId)
    {
        $usersTable = $this->getModel()->getTable();
        $user = $this->findById($userId);
        $interests = $user->interests;
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();

        $query->where($usersTable.".id",'!=',$userId); /** excluding current logged in user. */

        /** Age matching */
        $query->where($interestsTable."age_min",'>=',DB::raw("DATEDIFF(CURDATE(), ".$this->getModel()->getTable().".birthday)/365"))
            ->Where(DB::raw("DATEDIFF(CURDATE(), ".$this->getModel()->getTable().".birthday)/365"),'<=',$interests->age_max);

        /** Gender Matching */
        if($interests->gender != 2){
            $query->where($this->getModel()->getTable().".gender",$interests->gender);
        }
        return $query;
    }

    public function getByIds($ids = [])
    {
        return  $this->getModel()->whereIn('id', $ids)->get();
    }

    public function findByEmail($email)
    {
        return  $this->getModel()->where('email', $email)->first();
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

    private function getUserTableFields()
    {
        $userFields = [];
        collect($this->getModel()->fields())->each(function($value, $key) use(&$userFields){
            $userFields[] = "users.".$value;
        });
        return $userFields;
    }
}