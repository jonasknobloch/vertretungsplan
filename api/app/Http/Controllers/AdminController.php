<?php

namespace App\Http\Controllers;

use App\Admin;
use App\User;

class AdminController extends Controller
{
    public function getAdmins()
    {
        //return User::has('admin')->with('admin')->get();

        return Admin::all();
    }

    public function getAdmin($id)
    {
        return User::has('admin')->with('admin')->findOrFail($id);
    }

    public function addAdmin()
    {
        $userId = $this->request->has('user_id') ? $this->request->input('user_id') : null;

        if ($userId) {
            Admin::create([
                'user_id' => $userId,
            ]);

            // TODO: do ist like manager
            return $this->getAdmin($userId);
        } else {
            return response('Bad request.', 400);
        }

    }

    public function deleteAdmin($userId)
    {
        Admin::findOrFail($userId)->delete();
        return response(['message' => $userId.' deleted.'], 200);
    }
}
