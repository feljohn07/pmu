<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'staff_position',
        'profile'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function getFilamentAvatarUrl(): ?string
    {
        // Assuming 'profile_photo_path' is the attribute storing the avatar's path
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;
    }

    /**
     * The projects that the user belongs to.
     */
    public function projects(): BelongsToMany
    {
        // If you followed conventions, Laravel automatically figures out
        // the table name ('project_user') and the foreign keys ('user_id', 'project_id').
        $relationship = $this->belongsToMany(Project::class);

        // If you added timestamps to the pivot table:
        // $relationship->withTimestamps();

        // If you added extra pivot columns (like 'role'):
        // $relationship->withPivot('role');

        return $relationship;
    }
}
