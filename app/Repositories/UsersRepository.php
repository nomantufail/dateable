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
use App\Models\LikedUser;
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
        return (new BlockedUser())->where($where)->first();
    }

/*    public function countDatablesAtLocation($locationId, $userId)
    {
        return collect($this->getDatablesAtLocation($locationId,$userId))->count();
    }

    public function countCheckInsAtLocation($locationId, $userId)
    {
        return collect($this->getCheckInsAtLocation($locationId, $userId))->count();
    }*/

    public function getMatches($locationId, $userId)
    {
        $user = $this->findById($userId);
        $interests = $user->interests;
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        $myAge = Carbon::createFromFormat('Y-m-d',$user->birthday)->diff(Carbon::now())->days/365;

        /** @var UserInterests $interests */
        $usersTable = $this->getModel()->getTable();
        $users = [];
        $this->getModel()
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
            ->where($usersTable.".id",'!=',$user->id) /** excluding current logged in user. */
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
            ->groupBy(array_merge($userFields,['interested_in_me','i_am_interested_in', 'he_like_me', 'i_like_him']))
            ->get()->each(function($user) use($userId, &$users){
                $user->he_like_me = 0;
                $user->i_like_him = 0;

                $he_blocked_me = 0;
                $user->blockedUsers->each(function($blockedUser) use($userId, &$he_blocked_me){
                    if($blockedUser->subject_id == $userId)
                        $he_blocked_me = 1;
                });
                $i_blocked_him = 0;
                $user->blockedBy->each(function($blockingUser) use($userId, &$i_blocked_him){
                    if($blockingUser->object_id == $userId)
                        $i_blocked_him = 1;
                });
                if(!$he_blocked_me && !$i_blocked_him){
                    $user->likedUsers->each(function($likedUser) use($userId,&$user){
                        if($likedUser->subject_id == $userId)
                            $user->he_like_me = 1;
                    });
                    $user->likedBy->each(function($likingUser) use($userId,&$user){
                        if($likingUser->object_id == $userId)
                            $user->i_like_him = 1;
                    });

                    $users[] = $user;
                }
            });
        return $users;
    }

    public function getDatablesAtLocation($checkins)
    {
        $datables = [];
        collect($checkins)->each(function($checkedInUser)use($datables){
            if($checkedInUser->i_am_interested_in){
                $datables[] = $checkedInUser;
            }
        });
        return $datables;
    }

    public function getDatablesAtLocation_UNDER_DEV($locationId, $userId)
    {
        $user = $this->findById($userId);
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();
        /** @var UserInterests $interests */
        $usersTable = $this->getModel()->getTable();
        $users = [];
        $this->getModel()
            ->select(DB::raw(join(',',$userFields)))
            ->where($checkinsTable.".location_id",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->where($usersTable.".id",'!=',$user->id) /** excluding current logged in user. */
            ->where(function ($query)use ($user){
                $this->QUERY_usersIamInterestedIn($query, $user);
            })
            ->where($usersTable.".active",1)
            ->with('blockedUsers','blockedBy')
            ->with('likedUsers','likedBy')
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->groupBy(array_merge($userFields,[]))
            ->get()->each(function($user) use($userId, &$users){
                $user->he_like_me = 0;
                $user->i_like_him = 0;

                $he_blocked_me = 0;
                $user->blockedUsers->each(function($blockedUser) use($userId, &$he_blocked_me){
                    if($blockedUser->subject_id == $userId)
                        $he_blocked_me = 1;
                });
                $i_blocked_him = 0;
                $user->blockedBy->each(function($blockingUser) use($userId, &$i_blocked_him){
                    if($blockingUser->object_id == $userId)
                        $i_blocked_him = 1;
                });
                if(!$he_blocked_me && !$i_blocked_him){
                    $user->likedUsers->each(function($likedUser) use($userId,&$user){
                        if($likedUser->subject_id == $userId)
                            $user->he_like_me = 1;
                    });
                    $user->likedBy->each(function($likingUser) use($userId,&$user){
                        if($likingUser->object_id == $userId)
                            $user->i_like_him = 1;
                    });

                    $users[] = $user;
                }
            });
        return $users;
    }

    public function getCheckInsAtLocation($locationId, $userId)
    {
        $usersTable = $this->getModel()->getTable();
        $user = $this->findById($userId);
        $interests = $user->interests;
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();
        $likedUsersTable = (new LikedUsersRepository())->getModel()->getTable();
        $myAge = Carbon::createFromFormat('Y-m-d',$user->birthday)->diff(Carbon::now())->days/365;

        /** @var UserInterests $interests */
        $usersTable = $this->getModel()->getTable();
        $users = [];
        $this->getModel()
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
            ->where($usersTable.".id",'!=',$user->id) /** excluding current logged in user. */
            ->where($checkinsTable.".location_id",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->where($usersTable.".active",1)
            ->with('blockedUsers','blockedBy')
            ->with('likedUsers','likedBy')
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->groupBy(array_merge($userFields,['interested_in_me','i_am_interested_in']))
            ->get()->each(function($user) use($userId, &$users){
                $user->he_like_me = 0;
                $user->i_like_him = 0;

                $he_blocked_me = 0;
                $user->blockedUsers->each(function($blockedUser) use($userId, &$he_blocked_me){
                    if($blockedUser->subject_id == $userId)
                        $he_blocked_me = 1;
                });
                $i_blocked_him = 0;
                $user->blockedBy->each(function($blockingUser) use($userId, &$i_blocked_him){
                    if($blockingUser->object_id == $userId){
                        $i_blocked_him = 1;
                    }
                });
                if(!$he_blocked_me && !$i_blocked_him){
                    $user->likedUsers->each(function($likedUser) use($userId,&$user){
                        if($likedUser->subject_id == $userId)
                            $user->he_like_me = 1;
                    });
                    $user->likedBy->each(function($likingUser) use($userId,&$user){
                        if($likingUser->object_id == $userId)
                            $user->i_like_him = 1;
                    });

                    $users[] = $user;
                }
            });
        return $users;
    }
    public function getCheckInsAtLocation_depricated($locationId, $userId)
    {
        $usersTable = $this->getModel()->getTable();
        $user = $this->findById($userId);
        $interests = $user->interests;
        $userFields = $this->getUserTableFields();
        $interestsTable = (new UserInterestsRepository())->getModel()->getTable();
        $checkinsTable = (new CheckedinsRepository())->getModel()->getTable();
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();
        $likedUsersTable = (new LikedUsersRepository())->getModel()->getTable();
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
                END as interested_in_me,
                CASE
                    WHEN users_who_liked_me.subject_id = ".$userId."
                        THEN
                            true
                        else
                            false
                END as he_like_me,
                CASE
                    WHEN users_liked_by_me.object_id = ".$userId."
                        THEN
                            true
                        else
                            false
                END as i_like_him
            "))
            ->where($usersTable.".id",'!=',$user->id) /** excluding current logged in user. */
            ->where($checkinsTable.".location_id",$locationId)
            ->where($checkinsTable.".checked_out",null)
            ->where(function($query)use($blockedUsersTable,$userId){
                $this->QUERY_ignoreBlockedUsers($query,$userId);
            })
            ->where($usersTable.".active",1)
            ->leftJoin($interestsTable, $usersTable.".id",$interestsTable.".user_id")
            ->leftJoin($checkinsTable, $usersTable.".id",$checkinsTable.".user_id")
            ->leftJoin($blockedUsersTable." as users_blocked_by_me", "users_blocked_by_me.subject_id",$usersTable.".id") //ignoring users blocked by me
            ->leftJoin($blockedUsersTable." as users_who_blocked_me", "users_who_blocked_me.object_id",$usersTable.".id") //ignoring users who blocked me
            ->leftJoin($likedUsersTable." as users_liked_by_me", "users_liked_by_me.subject_id",$usersTable.".id") //ignoring users blocked by me
            ->leftJoin($likedUsersTable." as users_who_liked_me", "users_who_liked_me.object_id",$usersTable.".id") //ignoring users who blocked me
            ->groupBy(array_merge($userFields,['interested_in_me','i_am_interested_in', 'he_like_me', 'i_like_him']))
            ->get();
    }

    private function QUERY_ignoreBlockedUsers($query, $userId)
    {
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();
        $query->where(function($query)use($blockedUsersTable,$userId){
            $query->where("users_blocked_by_me.subject_id","!=",$userId);
            $query->orWhere("users_blocked_by_me.object_id",null);
        });
        $query->where(function($query)use($blockedUsersTable,$userId){
            $query->where("users_who_blocked_me.object_id","!=",$userId);
            $query->orWhere("users_who_blocked_me.object_id",null);
        });
        return $query;
    }

    private function QUERY_usersIamInterestedIn($query, User $user)
    {
        $usersTable = $this->getModel()->getTable();
        $interests = $user->interests;
        $blockedUsersTable = (new BlockedUser())->getModel()->getTable();

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
        return $this->getModel()->where('fb_id',$fbid)->first();
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