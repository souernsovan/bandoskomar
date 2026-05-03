<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->latest()
            ->get()
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => Str::headline($user->role ?? 'user'),
                'status' => Str::headline($user->status ?? 'active'),
                'last_active' => optional($user->updated_at)->diffForHumans() ?? 'Never',
                'avatar' => $user->avatar,
                'initials' => $this->initials($user->name),
            ]);

        return view('admin.users', compact('users'));
    }

    private function initials(string $name): string
    {
        return Str::of($name)
            ->explode(' ')
            ->filter()
            ->map(fn (string $part) => Str::substr($part, 0, 1))
            ->take(2)
            ->implode('');
    }
}
