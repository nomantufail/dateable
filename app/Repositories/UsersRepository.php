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

    public function getBlockedUsersByUserId($userId)
    {
        $usersTable = $this->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getTable();
        return (new BlockedUser())
            ->select(DB::raw($usersTable.".*"))
            ->where('object_id',$userId)
            ->leftJoin($usersTable,$usersTable.'.id',$blockedUsersTable.'.subject_id')->get();
    }

    /**
     * @return User $user
     */
    public function findBlockedUser($where)
    {
        return (new BlockedUser())->where($where)->first();
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
        //['user_interests.age_min','user_interests.age_max','user_interests.gender']
        $usersTable = $this->getModel()->getTable();
        $user = $this->findById($userId);
        $interests = $user->interests;
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
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
            ->Where(function ($query)use ($user){
                $query->where(function ($query)use ($user){
                    $this->QUERY_usersInterestedInMe($query, $user);
                });
                $query->orWhere(function ($query) use ($user){
                    $this->QUERY_usersIamInterestedIn($query, $user);
                });
            })
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
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
        return DB::table('users')
            ->select(DB::raw(join(',',$userFields).",
                CASE
                    WHEN bu.object_id is Null
                        THEN
                            0
                        else
                            bu.object_id
                END as blocked_by
             "))
            ->where($checkinsTable.".location_id",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->where(function ($query)use ($user){
                $this->QUERY_usersIamInterestedIn($query, $user);
            })
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->leftJoin($blockedUsersTable." as bu", "bu.subject_id",$usersTable.".id") //ignoring blocked users
            ->groupBy(array_merge($userFields,['blocked_by']))
            ->having("blocked_by",'>',"".$user->id)
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