<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users with search and filter capabilities.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $status = $request->query('status');

        $query = User::with(['groupeurProfile'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role && $role !== 'tous') {
            $query->where('role', $role);
        }

        if ($status && $status !== 'tous') {
            $query->where('status', $status);
        }

        $users = $query->paginate(15)->withQueryString();

        $counts = [
            'total' => User::count(),
            'expediteurs' => User::where('role', 'expediteur')->count(),
            'groupeurs' => User::where('role', 'groupeur')->count(),
            'suspendus' => User::where('status', 'suspendu')->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'selectedRole' => $role ?? 'tous',
            'selectedStatus' => $status ?? 'tous',
            'counts' => $counts,
        ]);
    }

    /**
     * Toggle the active/suspended status of a user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas suspendre votre propre compte administrateur.');
        }

        $newStatus = $user->status === 'actif' ? 'suspendu' : 'actif';
        $user->status = $newStatus;
        $user->save();

        $message = $newStatus === 'suspendu'
            ? "Le compte de {$user->name} a été suspendu. L'utilisateur ne pourra plus se connecter."
            : "Le compte de {$user->name} a été réactivé.";

        return back()->with('success', $message);
    }
}
