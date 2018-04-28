<?php

namespace App\Policies;

use App\User;
use App\Breakdown;
use App\Authorization;
use Illuminate\Auth\Access\HandlesAuthorization;

class BreakdownPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the breakdown.
     *
     * @param  \App\User  $user
     * @param  \App\Breakdown  $breakdown
     * @return mixed
     */
    public function view(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'Breakdown')
                ->where('user_id', $user->id)
                ->where('view', 1)->count();
    }

    /**
     * Determine whether the user can create breakdowns.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'Breakdown')
                ->where('user_id', $user->id)
                ->where('create', 1)->count();
    }

    /**
     * Determine whether the user can update the breakdown.
     *
     * @param  \App\User  $user
     * @param  \App\Breakdown  $breakdown
     * @return mixed
     */
    public function update(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'Breakdown')
                ->where('user_id', $user->id)
                ->where('update', 1)->count();
    }

    /**
     * Determine whether the user can delete the breakdown.
     *
     * @param  \App\User  $user
     * @param  \App\Breakdown  $breakdown
     * @return mixed
     */
    public function delete(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'Breakdown')
                ->where('user_id', $user->id)
                ->where('delete', 1)->count();
    }

    public function export(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'Breakdown')
                ->where('user_id', $user->id)
                ->where('export', 1)->count();
    }

    public function import(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'Breakdown')
                ->where('user_id', $user->id)
                ->where('import', 1)->count();
    }

    public function createOrUpdate(User $user)
    {
        return $this->create($user) || $this->update($user);
    }

    public function updateOrDelete(User $user)
    {
        return $this->update($user) || $this->delete($user);
    }
}
