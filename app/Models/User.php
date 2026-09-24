<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'license_no', 'phone', 'bio', 'avatar_path', 'notification_preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

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
            'notification_preferences' => 'array',
        ];
    }

    public function coachedPatients(): HasMany
    {
        return $this->hasMany(PatientRecord::class, 'life_coach_id');
    }

    public function coachingNotes(): HasMany
    {
        return $this->hasMany(CoachingNote::class, 'life_coach_id');
    }

    public function coachingTasks(): HasMany
    {
        return $this->hasMany(CoachingTask::class, 'life_coach_id');
    }

    public function coachingSchedules(): HasMany
    {
        return $this->hasMany(CoachingSchedule::class, 'life_coach_id');
    }

    public function coachingGoals(): HasMany
    {
        return $this->hasMany(CoachingGoal::class, 'life_coach_id');
    }
}
