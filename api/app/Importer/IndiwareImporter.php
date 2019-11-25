<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.05.17
 * Time: 17:38
 */

namespace App\Importer;

use Illuminate\Support\Facades\DB;

use App\Parsers\IndiwareParser;
use App\School;
use App\ScheduleLesson;
use App\Change;
use App\ChangeLesson;
use App\Schedule;

class IndiwareImporter
{
    private static $files = array(
        'lessons' => 'importLessons',
        'changes' => 'importChanges'
    );

    private $key;
    private $managerId;

    public function __construct($key, $managerId)
    {
        $this->key = $key;
        $this->managerId = $managerId;
    }

    public function import($file) {
        $type = self::identifyImport($file);
        $method = self::$files[$type];

        //TODO: pass SimpleXML ?
        $this->$method($file);
        return array(
            'fileContent' => $type
        );
    }

    private static function identifyImport($file) {

        $xml = simplexml_load_file($file);

        if ($xml) {
            switch ($xml->getName()) {
                case 'vp':
                    return 'changes';
                    //return $this->importChanges($xml);
                case 'sp':
                    return 'lessons';
                    //return $this->importTimetable($xml);
                default:
                    return false;
            }
        } else {
            return false;
        }

    }

    private function importLessons($file) {

        $data = IndiwareParser::parseTimetable($file);

        Schedule::where('school_id', $this->key)->delete();

        $scheduleSchoolId = Schedule::create([
            'school_id' => $this->key,
            'manager_user_id' => $this->managerId,
        ])->school_id;

        foreach ($data as $lesson) {
            ScheduleLesson::create([
                'schedule_school_id' => $scheduleSchoolId,
                'day' => $lesson['day'],
                'class' => $lesson['class'],
                'index' => $lesson['index'],
                'week' => $lesson['week'],
                'subject' => $lesson['subject'],
                'teacher' => $lesson['teacher'],
                'room' => $lesson['room'],
                'group' => $lesson['group'],
                'coupling' => $lesson['coupling'],
            ]);
        }

    }

    private function importChanges($file) {

        //TODO: review method

        $data = IndiwareParser::parseChanges($file);

        foreach ($data as $change) {

            /*DB::table('changes')->leftJoin('schools', 'changes.school_id', '=', 'schools.id')
                                ->where('schools.key', $this->key)->where('changes.date', '=', $change['date'])
                                ->delete();*/

            // TODO: <= for deletion
            Change::where('school_id', $this->key)->where('date', '=', $change['date'])->delete();

            $changeId = Change::create([
                'school_id' => $this->key,
                'manager_user_id' => $this->managerId,
                'date' => $change['date'],
                'week' => $change['week'],
            ])->id;

            foreach ($change['lessons'] as $changedLesson) {
                ChangeLesson::create([
                    'change_id' => $changeId,
                    'class' => $changedLesson['class'],
                    'index' => $changedLesson['index'],
                    'subject' => $changedLesson['subject'],
                    'teacher' => $changedLesson['teacher'],
                    'room' => $changedLesson['room'],
                    'info' => $changedLesson['info'],
                    'group' => $changedLesson['group'],
                ]);
            }
        }
    }
}