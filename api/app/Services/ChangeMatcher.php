<?php

/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 06.05.17
 * Time: 15:37
 */

namespace App\Services;

use App\ScheduleLesson;
use App\ChangeLesson;
use Illuminate\Support\Collection;

class ChangeMatcher
{
    //TODO: week priority?
    private $priorities = array('index', 'subject', 'group', 'teacher');

    private $lessons;
    private $changedLessons;

    private $matches = array();

    public function __construct(Collection $lessons)
    {
        $this->lessons = $lessons;
    }

    public function match(Collection $changedLessons) {
        $this->changedLessons = $changedLessons;

        foreach ($this->changedLessons as $changedLesson) {
            $this->matches[$changedLesson->id] = $this->matchChangedLesson($changedLesson);
        }

        return array_filter($this->matches);
    }

    private function matchChangedLesson(ChangeLesson $changedLesson) {

        //https://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
        //https://php.net/manual/en/language.oop5.cloning.php
        $possibleMatches = clone $this->lessons;

        foreach ($this->priorities as $priority) {

            $possibleMatches = $this->matchChangedLessonByPriority($changedLesson, $priority, $possibleMatches);

            if ($possibleMatches->count() === 1) {
                return $possibleMatches->first()->id;
            } else if ($possibleMatches->count() === 0) {
                // matching not possible
                return null;
            }
        }

        //TODO: utilize changed lesson info

        return $possibleMatches->pluck('id')->toArray();
    }

    private function matchChangedLessonByPriority(ChangeLesson $changedLesson, $priority, $possibleMatches) {

        $possibleMatches->each(function ($lesson, $key) use ($changedLesson, $priority, $possibleMatches) {
            if ($lesson->$priority !== $changedLesson->$priority) {
                $possibleMatches->forget($key);
            }
        });

        return $possibleMatches;
    }

    private function utilizeChangedLessonInfo(ChangeLesson $changedLesson) {
        //TODO: regex over info string
        return $changedLesson;
    }
}