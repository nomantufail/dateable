<?php
/**
 * Created by PhpStorm.
 * user: nomantufail
 * Date: 10/10/2016
 * Time: 10:13 AM
 */

namespace App\Repositories;

use App\Events\UserRegistered;
use App\Models\BlockedUser;
use App\Models\UserInterests;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

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
        Event::fire(new UserRegistered($user));
        return $user;
    }

    public function blockUser(BlockedUser $blockedUserModel)
    {
        $blockedUserModel->save();
        return $blockedUserModel;
    }

    public function unblockUser($where)
    {
        return (new BlockedUser())->where($where)->delete();
    }

    public function updateInterestsWhere($where, $data)
    {
        return (new UserInterests())->where($where)->update($data);
    }

    public function updateWhere($where, $data)
    {
        return $this->getModel()->where($where)->update($data);
    }

    public function getBlockedUsersByUserId($userId)
    {
        $usersTable = $this->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getTable();
        return (new BlockedUser())
            ->select(DB::raw($usersTable.".*"))
            ->where('object_id',$userId)
            ->where($usersTable.".active",1)
            ->leftJoin($usersTable,$usersTable.'.id',$blockedUsersTable.'.subject_id')
            ->get();
    }

    /**
     * @return User $user
     */
    public function findBlockedUser($where)
    {
        $usersTable = $this->getModel()->getTable();
        return (new BlockedUser())->where($where)
            ->where($usersTable.".active",1)
            ->first();
    }

    public function countDatablesAtLocation($locationId, $userId)
    {
        return $this->getDatablesAtLocation($locationId,$userId)->count();
    }

    public function countCheckInsAtLocation($locationId)
    {
        return $this->getCheckInsAtLocation($locationId)->count();
    }

    public function getMatches($locationId, $userId)
    {
        $usersTable = $this->getModel()->getTable();
        $user = $this->findById($userId);
        $interests = $user->interests;
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();
        $myAge = Carbon::createFromFormat('Y-m-d',$user->birthday)->diff(Carbon::now())->days/365;

        /** @var UserInterests $interests */
        $usersTable = $this->getModel()->getTable();
        return $this->getModel()
            //finding who is interested in whom?
            ->select(DB::raw(" users.*,
                CASE
                    WHEN DATEDIFF(CURDATE(), ".$usersTable.".birthday)/365 >= ".$interests->age_min." AND DATEDIFF(CURDATE(), ".$usersTable.".birthday)/365 <= ".$interests->age_max.(($interests->gender != 2)?" AND ".$usersTable.".gender = ".$interests->gender:"")."
                        THEN
                            true
                        else
                            false
                END as i_am_interested_in ,
                CASE
                    WHEN ".$interestsTable.".age_min <= ".$myAge." AND ".$interestsTable.".age_max >= ".$myAge.(($interests->gender != 2)?" AND ".$interestsTable.".gender = ".$user->gender:"")."
                        THEN
                            true
                        else
                            false
                END as interested_in_me
            "))
            ->where($checkinsTable.".location_id",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->where(function($query)use($blockedUsersTable,$userId){
                $query->where(function($query)use($blockedUsersTable,$userId){
                    $query->where("users_blocked_by_me.object_id","!=",$userId);
                    $query->orWhere("users_blocked_by_me.object_id",null);
                });
                $query->where(function($query)use($blockedUsersTable,$userId){
                    $query->where("users_who_blocked_me.subject_id","!=",$userId);
                    $query->orWhere("users_who_blocked_me.object_id",null);
                });
            })
            ->Where(function ($query)use ($user){
                $query->where(function ($query)use ($user){
                    $this->QUERY_usersInterestedInMe($query, $user);
                });
                $query->orWhere(function ($query) use ($user){
                    $this->QUERY_usersIamInterestedIn($query, $user);
                });
            })
            ->where($usersTable.".active",1)
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->leftJoin($blockedUsersTable." as users_blocked_by_me", "users_blocked_by_me.subject_id",$usersTable.".id") //ignoring users blocked by me
            ->leftJoin($blockedUsersTable." as users_who_blocked_me", "users_who_blocked_me.object_id",$usersTable.".id") //ignoring users who blocked me
            ->groupBy(array_merge($userFields,['interested_in_me','i_am_interested_in']))
            ->get();
    }

    public function getDatablesAtLocation($locationId, $userId)
    {
        $user = $this->findById($userId);
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();
        /** @var UserInterests $interests */
        $usersTable = $this->getModel()->getTable();
        return $this->getModel()
            ->select(DB::raw(join(',',$userFields)))
            ->where($checkinsTable.".location_id",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->where(function ($query)use ($user){
                $this->QUERY_usersIamInterestedIn($query, $user);
            })
            ->where(function($query)use($blockedUsersTable,$userId){
                $query->where(function($query)use($blockedUsersTable,$userId){
                    $query->where("users_blocked_by_me.object_id","!=",$userId);
                    $query->orWhere("users_blocked_by_me.object_id",null);
                });
                $query->where(function($query)use($blockedUsersTable,$userId){
                    $query->where("users_who_blocked_me.subject_id","!=",$userId);
                    $query->orWhere("users_who_blocked_me.object_id",null);
                });
            })
            ->where($usersTable.".active",1)
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->leftJoin($blockedUsersTable." as users_blocked_by_me", "users_blocked_by_me.subject_id",$usersTable.".id") //ignoring users blocked by me
            ->leftJoin($blockedUsersTable." as users_who_blocked_me", "users_who_blocked_me.object_id",$usersTable.".id") //ignoring users who blocked me
            ->groupBy(array_merge($userFields,[]))
            ->get();
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
            ->where($usersTable.".active",1)
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->groupBy($userFields)
            ->get();
    }

    private function QUERY_usersIamInterestedIn($query, User $user)
    {
        $usersTable = $this->getModel()->getTable();
        $interests = $user->interests;
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();

        $query->where($usersTable.".id",'!=',$user->id); /** excluding current logged in user. */
        /** Age matching */
        $query->where(DB::raw("DATEDIFF(CURDATE(), ".$this->getModel()->getTable().".birthday)/365"),'>=',$interests->age_min)
            ->Where(DB::raw("DATEDIFF(CURDATE(), ".$this->getModel()->getTable().".birthday)/365"),'<=',$interests->age_max);

        /** Gender Matching */
        if($interests->gender != 2){
            $query->where($usersTable.".gender",$interests->gender);
        }
        return $query;
    }

    private function QUERY_usersInterestedInMe($query, User $user)
    {
        $usersTable = $this->getModel()->getTable();
        $interests = $user->interests;
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();
        $myAge = Carbon::createFromFormat('Y-m-d',$user->birthday)->diff(Carbon::now())->days/365;

        $query->where($usersTable.".id",'!=',$user->id); /** excluding current logged in user. */

        /** Age matching */
        $query->where($interestsTable.".age_min",'<=',$myAge)
            ->where($interestsTable.".age_max",'>=',$myAge);

        /** Gender Matching */
        if($interests->gender != 2){
            $query->where($interestsTable.".gender",$user->gender);
        }
        return $query;
    }

    public function getByIds($ids = [])
    {
        $usersTable = $this->getModel()->getTable();
        return  $this->getModel()
            ->whereIn('id', $ids)
            ->where($usersTable.".active",1)
            ->get();
    }

    public function findByEmail($email)
    {
        $usersTable = $this->getModel()->getTable();
        return  $this->getModel()->where('email', $email)
            ->where($usersTable.".active",1)
            ->first();
    }

    public function findByFbId($fbid)
    {
        $usersTable = $this->getModel()->getTable();
        return $this->getModel()->where('fb_id',$fbid)
            ->where($usersTable.".active",1)
            ->first();
    }

    public function findByToken($token)
    {
        $usersTable = $this->getModel()->getTable();
        return  $this->getModel()->where('access_token', $token)
            ->where($usersTable.".active",1)
            ->first();
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