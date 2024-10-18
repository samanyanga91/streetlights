<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Display users list
    public function index()
    {
        $users = User::all();
        return view('users', compact('users'));
    }

    // Edit user details
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // Update user details
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'role' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
        ]);

        $user->update($request->only('name', 'phone', 'role', 'ward'));

        return redirect()->route('users.index')->with('success', 'User details updated successfully.');
    }
}
