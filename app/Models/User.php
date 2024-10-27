<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Filters\V1\QueryFilter;
use App\Traits\V1\Authorization;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Authorization, CanResetPassword;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');
    }

    public function assignRoles($roles): void
    {
        if (empty($roles)) {
            return;
        }

        $roles = is_iterable($roles) ? $roles : [$roles];
        $this->roles()->sync($roles);
    }

    public function hasRole($roleName): bool
    {
        // Ensure that roleName is an array for consistent handling
        $roleNames = is_array($roleName) ? $roleName : [$roleName];

        // Check if any of the roles exist for the user
        $roleCount = $this->roles()->whereIn('name', $roleNames)->count();

        return $roleCount === count($roleNames);
    }

    public function can($abilities, $arguments = []): bool
    {
        // If $abilities is an iterable, we check all abilities
        if (is_iterable($abilities)) {
            foreach ($abilities as $ability) {
                if ($this->checkUserAbility($ability)) {
                    return true;
                }
            }
            return false; // Return false if none of the abilities match
        }

        // If $abilities is a string, we check just one ability
        return $this->checkUserAbility($abilities);
    }

    private function checkUserAbility($ability)
    {
        if($this->checkAbility($ability)){
            return true;
        }

        return $this->roles->contains(function ($role) use ($ability) {
            return $role->hasPermission($ability);
        });
    }

    public function scopeFilter(Builder $query, QueryFilter $filter): void
    {
        $filter->apply($query);
    }
}
