<?php

namespace App\Models\Pivots;

use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectUserAsTeamMember extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     *  Reference 1: https://stackoverflow.com/questions/71439425/cant-get-id-from-created-eloquent-model-extending-pivot-in-laravel
     *  Reference 2: https://laravel.com/docs/9.x/eloquent-relationships#custom-pivot-models-and-incrementing-ids
     *
     * @var bool
     */
    public $incrementing = true;

    protected $casts = [
        'permissions' => 'array'
    ];

    const VISIBLE_COLUMNS = [
        'id', 'permissions'
    ];

    public function getPermissionsAttribute($permissions)
    {
        $permissions = json_decode($permissions ?? '[]');

        return collect($permissions)->contains('*')
            //  Get every permission available except the "*" permission
            ? collect(Project::PERMISSIONS)->filter(fn($permission) => $permission !== '*')->all()
            //  Get only the specified permissions
            : $permissions;
    }
}
