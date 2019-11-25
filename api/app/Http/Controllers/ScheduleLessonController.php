<?php

namespace App\Http\Controllers;

use App\School;

use App\ChangeLesson;

use App\Services\ChangeMatcher;
use App\StudentLesson;
use Carbon\Carbon;


class ScheduleLessonController extends Controller
{
    private $user;
    private $school;

    public function getLessonsWithChanges($schoolId, $class, $date)
    {
        $this->user = $this->request->attributes->all()['user'];

        $date = Carbon::parse($date);

        $school = School::findOrFail($schoolId);
        $this->school = $school;

        $change = $school->changes->where('date', $date->toDateString());

        // TODO: filter by week
        $scheduleLessons = $school->schedule->lessons->where('day', $date->dayOfWeek)->where('class', $class)->values();
        $changedLessons = $change->isNotEmpty() ? $change->first()->lessons->where('class', $class)->values() : collect([]);

        $matches = $this->matchChangedLessons($scheduleLessons, $changedLessons);

        // TODO: remove -> not necessary
        $matchedChangedLessons = $changedLessons->filter(function (ChangeLesson $changedLesson, $key) use ($matches) {
            return array_key_exists($changedLesson->id, array_filter($matches));
        })->values();

        $unmatchedChangedLessons = $changedLessons->filter(function (ChangeLesson $changedLesson, $key) use ($matches) {
            return !array_key_exists($changedLesson->id, array_filter($matches));
        })->values();

        $schedule = $this->createSchedule($scheduleLessons, $matchedChangedLessons, $matches);


        return array(
            'schedule' => $schedule,
            'unmatched' => $unmatchedChangedLessons,
        );
    }


    private function matchChangedLessons($scheduleLessons, $changedLessons)
    {
        $changeMatcher = new ChangeMatcher($scheduleLessons);
        return $changeMatcher->match($changedLessons);
    }

    private function createSchedule($scheduleLessons, $matchedChangedLessons, $matches)
    {
        $scheduleLessons = $scheduleLessons->keyBy('id')->toArray();
        $matchedChangedLessons = $matchedChangedLessons->keyBy('id')->toArray();

        foreach ($scheduleLessons as $key => $lesson) {
                $scheduleLessons[$key]['change'] = null;
        }

        foreach ($matches as $changedLessonId => $scheduleLessonId) {
            //$scheduleLessons[$scheduleLessonId] = $matchedChangedLessons[$changedLessonId];
            // TODO: ErrorException in ScheduleLessonController.php line 72: xUndefined offset: 340
            $scheduleLessons[$scheduleLessonId]['change'] = $matchedChangedLessons[$changedLessonId];
        }

        $schedule = array_values($scheduleLessons);
        $groupedSchedule = [];

        foreach ($schedule as $lesson) {
            $groupedSchedule[$lesson['index']]['index'] = $lesson['index'];
            $groupedSchedule[$lesson['index']]['lessons'][] = $lesson;
        }

        return $this->setFavourites(array_values($groupedSchedule));
    }

    private function setFavourites($groupedSchedule)
    {

        $studentLessons = StudentLesson::where('student_user_id', $this->user->id)->where('school_id', $this->school->id)->get()->toArray();
        foreach ($groupedSchedule as $entryKey => $entry) {

            $groupedSchedule[$entryKey]['students_lessons_id'] = null;

            if (count($entry) > 1) {

                foreach ($entry['lessons'] as $lessonKey => $lesson) {

                    foreach ($studentLessons as $key => $studentLesson) {
                        if ($lesson['subject'] == $studentLesson['subject'] and $lesson['teacher'] == $studentLesson['teacher'] and $lesson['group'] == $studentLesson['group']) {
                            $groupedSchedule[$entryKey]['students_lessons_id'] = $studentLesson['id'];
                            $groupedSchedule[$entryKey]['lessons'][$lessonKey]['is_favourite'] = true;
                            break;
                        } else {
                            $groupedSchedule[$entryKey]['lessons'][$lessonKey]['is_favourite'] = false;
                        }
                    }
                }

            } else {
                foreach ($entry['lessons'] as $lessonKey => $lesson) {
                    $groupedSchedule[$entryKey]['lessons'][$lessonKey]['is_favourite'] = false;
                }
            }

        }
        return $groupedSchedule;
    }
}