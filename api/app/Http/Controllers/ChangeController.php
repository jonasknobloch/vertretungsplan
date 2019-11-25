<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 20.07.17
 * Time: 11:27
 */

namespace App\Http\Controllers;

use App\School;

use Carbon\Carbon;

class ChangeController extends Controller {
    public function showChanges($schoolId) {
        return School::findOrFail($schoolId)->changes;
    }

    public function getChanges($date){
        $school = \Auth::user();

        $date = Carbon::parse($date);

        $change = $school->changes->where('date', $date->toDateString());
        $changedLessons = $change->isNotEmpty() ? $change->first()->lessons->values() : collect([]);

        return $changedLessons;
    }

    public function deleteChangeByDate($schoolId, $date) {
        School::findOrFail($schoolId)->changes->where('date', $date)->first()->delete();
        return response($date.' deleted.', 200);
    }
}