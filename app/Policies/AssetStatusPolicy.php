<?php

namespace App\Policies;

use App\User;
use App\AssetStatus;
use App\Authorization;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetStatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the assetStatus.
     *
     * @param  \App\User  $user
     * @param  \App\AssetStatus  $assetStatus
     * @return mixed
     */
    public function view(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'AssetStatus')
                ->where('user_id', $user->id)
                ->where('view', 1)->count();
    }

    /**
     * Determine whether the user can create assetStatuss.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'AssetStatus')
                ->where('user_id', $user->id)
                ->where('create', 1)->count();
    }

    /**
     * Determine whether the user can update the assetStatus.
     *
     * @param  \App\User  $user
     * @param  \App\AssetStatus  $assetStatus
     * @return mixed
     */
    public function update(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'AssetStatus')
                ->where('user_id', $user->id)
                ->where('update', 1)->count();
    }

    /**
     * Determine whether the user can delete the assetStatus.
     *
     * @param  \App\User  $user
     * @param  \App\AssetStatus  $assetStatus
     * @return mixed
     */
    public function delete(User $user)
    {
        if ($user->super_admin) {
            return true;
        }

        return Authorization::where('controller', 'AssetStatus')
                ->where('user_id', $user->id)
                ->where('delete', 1)->count();
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
