<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin', [
            'users' => User::withCount('records')->withMax('records', 'score')->orderBy('id')->get(),
            'records' => Record::with('user')->latest()->limit(20)->get(),
        ]);
    }

    public function toggleRole(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Нельзя менять свою роль.');
        }

        $user->update(['role' => $user->isAdmin() ? 'player' : 'admin']);

        return back()->with('status', 'Роль обновлена.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Нельзя удалить себя.');
        }

        $user->delete();

        return back()->with('status', 'Пользователь удалён.');
    }
}
