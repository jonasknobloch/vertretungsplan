<?php

namespace App\Http\Controllers;

use App\StudentLesson;

class StudentLessonController extends Controller
{
    public function addLesson($studentUserId, $schoolId) {

        $subject = $this->request->has('subject') ? $this->request->input('subject') : null;
        $teacher = $this->request->has('teacher') ? $this->request->input('teacher') : null;
        $group = $this->request->has('group') ? $this->request->input('group') : null;

        if($studentUserId and $schoolId and $subject and $teacher) {
            return StudentLesson::create([
                'student_user_id' => $studentUserId,
                'school_id' => $schoolId,
                'subject' => $subject,
                'teacher' => $teacher,
                'group' => $group
            ]);
        } else {
            return response('Bad request.', 400);
        }
    }

    public function deleteLesson($studentUserId, $schoolId, $id) {
        StudentLesson::findOrFail($id)->delete();
        return response(['message' => 'Student lesson deleted.'], 200);
    }
}