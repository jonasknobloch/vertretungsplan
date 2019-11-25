<?php

namespace App\Http\Controllers;

use App\School;

class SchoolController extends Controller
{
    public function getSchools() {
        return School::all();
    }

    public function getSchool($id) {
        return School::findOrFail($id);
    }

    public function addSchool() {

        // request->input !== request->get!!

        $id = $this->request->has('id') ? $this->request->input('id') : null;
        $name = $this->request->has('name') ? $this->request->input('name') : null;

        // TODO: check if id already in use

        if ($id and $name) {
            $school = new School();
            $school->id = $id;
            $school->name = $name;
            $school->save();

            return $school;
        } else {
            return response('Bad request.', 400);
        }
    }

    public function editSchool($id) {
        $school = School::findOrFail($id);

        $name = $this->request->has('name') ? $this->request->input('name') : null;

        if ($name) {
            if ($name) $school->name = $name;

            $school->save();
        } else {
            return response('Bad request.', 400);
        }

        return $school;
    }

    public function deleteSchool($id) {
        School::findOrFail($id)->delete();
        return response($id.' deleted.', 200);
    }
}