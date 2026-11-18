<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'avatar', 'password', 'email_verified_at', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }
    public static function getUsersExceptUser(user $exceptUser): array
    {
            $userId = $exceptUser->id;
            $query = User::select(['users.*',"messages.message as last_message" ,'messages.create_at as last_message_date'])
            ->where('users.id','!=',$userId)
                ->when(!$exceptUser->is_admin,function ($query){
                    $query->whereNull('users.blocked_at');
                })
                ->leftJoin('conversations',function ($join ) use ($userId){

                })
            ;

    }

}
