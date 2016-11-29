<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\UserInterests;
use App\Repositories\UserInterestsRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddDefaultSettings
{
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $userInterests = new UserInterests();
        $userInterests->gender = ($event->user->gender == 1)?0:1;
        $userInterests->age_max = 30;
        $userInterests->age_min = 18;
        (new UserInterestsRepository())->store($userInterests);
    }
}
