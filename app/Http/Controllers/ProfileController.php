<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

class ProfileController extends Controller
{
    /**
     * Show admin profile
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $admin = Auth::guard('admin')->user();
        $roles = collect(Admin::ROLES)->map(fn ($name, $id) => (object) ['id' => $id, 'name' => $name, 'slug' => $id])->values()->all();

        return view('admin.profile', compact('admin', 'roles'));
    }

    /**
     * Update admin profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin->update($request->only(['name', 'phone']));

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Change admin password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Update password
        $admin->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
