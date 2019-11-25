<?php

namespace App\Http\Controllers;

use App\Schedule;


class ScheduleController extends Controller
{
    public function getSchedule($id)
    {
        return Schedule::findOrFail($id);
    }

    // TODO: check attributes -> school id over route?
    public function addSchedule($schoolId, $managerId) {
        return Schedule::create([
            'school_id' => $schoolId,
            'manager_user_id' => $managerId,
        ])->school_id;
    }

    public function deleteSchedule($id) {
        Schedule::findOrFail($id)->delete();
        return response(['message' => 'Schedule deleted.'], 200);
    }
}