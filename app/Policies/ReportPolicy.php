<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user): bool
    {
        return $user->role === User::ROLE_RESIDENT;
    }

    public function view(User $user, Report $report): bool
    {
        return $report->user_id === $user->id;
    }

    public function update(User $user, Report $report): bool
    {
        return false;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }
}
