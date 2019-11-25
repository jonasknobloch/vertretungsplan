<?php

namespace App\Http\Controllers;

use App\Manager;
use App\User;

class ManagerController extends Controller
{
    public function getManagers()
    {
        //return User::has('manager')->with('manager.schools')->get();
        return Manager::with('schools')->get();
    }

    public function getManager($id)
    {
        // TODO: use Manager model
        return User::has('manager')->with('manager')->findOrFail($id);
    }

    public function addManager()
    {
        $userId = $this->request->has('user_id') ? $this->request->input('user_id') : null;

        if ($userId) {
            Manager::create([
                'user_id' => $userId,
            ]);

            // TODO: return created model
            return $this->getManager($userId);
        } else {
            return response('Bad request.', 400);
        }
    }

    public function addSchoolToManager($userId) {
        $schoolId = $this->request->has('school_id') ? $this->request->input('school_id') : null;

        $manager = Manager::findOrFail($userId);
        $manager->schools()->attach($schoolId);

        return $manager->with('schools')->get();
    }

    public function deleteSchoolFromManager($userId, $schoolId) {
        $manager = Manager::findOrFail($userId);

        // schoolId null would fuck up the entire pivot table
        if ($schoolId) {
            $manager->schools()->detach($schoolId);
        }

        // TODO: return standard response to maintain consistency?
        return $manager->with('schools')->get();
    }

    public function deleteManager($userId)
    {
        Manager::findOrFail($userId)->delete();
        return response(['message' => $userId.' deleted.'], 200);
    }
}
