<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggle(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        if (request()->ajax()) {
            return response()->json([
                'message'   => 'User status updated.',
                'is_active' => $user->is_active
            ]);
        }

        return back()->with('success', 'User status updated.');
    }
}
