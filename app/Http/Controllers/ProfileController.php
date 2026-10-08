<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profile user yang sedang login.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->loadMissing('employee.division');

        return view('profile.edit', [
            'user' => $user,
            'employee' => $user->employee,
        ]);
    }

    /**
     * Identitas akun dikelola HR / Administrator Sistem.
     */
    public function update(Request $request): RedirectResponse
    {
        return Redirect::route('profile.edit')
            ->with('status', 'identity-managed-by-hr');
    }

    /**
     * Penghapusan akun dikelola administrator sistem dan tidak dapat dilakukan sendiri.
     */
    public function destroy(Request $request): RedirectResponse
    {
        return Redirect::route('profile.edit')
            ->with('status', 'Penghapusan akun dikelola administrator sistem dan tidak dapat dilakukan sendiri.');
    }
}
