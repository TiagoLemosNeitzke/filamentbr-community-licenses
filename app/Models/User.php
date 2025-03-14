<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Contracts\HasTeams;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class User extends Authenticatable implements FilamentUser, HasCurrentTenantLabel, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasTeams;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->allTeams()->isNotEmpty();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return Gate::allows('view', $tenant);
    }

    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->allTeams()->contains(Team::first())) {
            return $this->allTeams();
        }

        return $this->teams;
    }

    public function getCurrentTenantLabel(): string
    {
        return 'Time';
    }
}
