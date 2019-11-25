<?php

namespace App\Http\Controllers;

use App\User;
use App\UserAuthToken;

class UserController extends Controller
{
    public function getUsers()
    {
        $relations = $this->request->has('relations') ?  explode(',', $this->request->get('relations')) : [];
        return User::with($relations)->get();
    }

    public function getUser($id) {
        return User::findOrFail($id);
    }

    public function addUser()
    {
        // TODO: use input
        $id = $this->request->has('id') ? $this->request->get('id') : null;
        $firstName = $this->request->has('firstName') ? $this->request->get('firstName') : null;
        $lastName = $this->request->has('lastName') ? $this->request->get('lastName') : null;
        $email = $this->request->has('email') ? $this->request->get('email') : null;

        if ($id and $firstName and $lastName and $email) {
            $user = new User();
            $user->id = $id;
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $email;
            $user->save();

            return $user;
        } else {
            return response('Bad request.', 400);
        }
    }

    public function editUser($id)
    {
        // TODO: use input
        $firstName = $this->request->has('firstName') ? $this->request->get('firstName') : null;
        $lastName = $this->request->has('lastName') ? $this->request->get('lastName') : null;
        $email = $this->request->has('email') ? $this->request->get('email') : null;

        if ($id and $firstName and $lastName and $email) {
            $user = User::findOrFail($id);
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $email;
            $user->save();

            return $user;
        } else {
            return response('Bad request.', 400);
        }
    }

    public function deleteUser($id) {
        User::findOrFail($id)->delete();
        return response(['message' => $id.' deleted.'], 200);
    }
}