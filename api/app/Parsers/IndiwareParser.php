<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.04.17
 * Time: 20:53
 */

namespace App\Parsers;

use DateTime;

//TODO: support all schools

class IndiwareParser
{
    private static $month = array(
        1 => 'Januar',
        2 => 'Februar',
        3 => 'März',
        4 => 'April',
        5 => 'Mai',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'August',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Dezember'
    );

    public static function parseTimetable($file)
    {
        $timetable = simplexml_load_file($file);
        $lessons = array();

        //could be improved using preg_match instead of using static node names
        $days = array(1, 2, 3, 4, 5, 6, 7);

        foreach ($timetable as $entry) {

            $class = trim($entry->kopf->titel);

            //looping trough every xml entry
            foreach ($entry->haupt->zeile as $row) {
                $index = trim($row->stunde);

                //looping trough base array
                foreach ($days as $day) {
                    $node = 'tag' . $day;

                    //fetching multiple child nodes
                    foreach ($row->$node as $key => $entry) {
                        //TODO: filter empty entries

                        $subject = trim($entry->fach);
                        $teacher = trim($entry->raum);
                        $room = trim($entry->raum);

                        if ($subject and $teacher and $room) {

                            preg_match('/([^*]+)(\*{0,2})/', trim($entry->lehrer), $matches);

                            $teacher = $matches[1];
                            $week = null;

                            switch (substr_count($matches[2], '*')) {
                                case 1:
                                    $week = 'A';
                                    break;
                                case 2:
                                    $week = 'B';
                                    break;
                                default:
                                    $week = null;
                                    break;
                            }

                            $lesson = array(
                                'day' => $day,
                                'class' => $class,
                                'index' => $index,
                                'week' => $week,
                                'subject' => $subject,
                                'teacher' => $teacher,
                                'room' => $room,
                                'group' => null,
                                'coupling' => null
                            );

                            //TODO: fix issue JG12 -> group always null
                            $group = trim($entry->gruppe);
                            $coupling = trim($entry->kopplung);

                            $lesson['group'] = $group ? $group : null;
                            $lesson['coupling'] = $coupling ? $coupling : null;

                            if ($lesson['class'] == 'JG12') {
                                //dd($entry->gruppe, $group, $lesson['group']);
                            }

                            array_push($lessons, $lesson);
                        }
                    }
                }
            }
        }

        //dd($lessons);
        return $lessons;
    }

    public static function parseChanges($file)
    {
        //TODO: implement additions
        //TODO: implement exams

        $changes = simplexml_load_file($file);
        $map = self::mapChangesXMLStructure($changes);

        //dd($map);
        //TODO: multiple plans in one file -> utilize xml structure map

        $entries = array();
        $output = array();

        foreach ($changes->kopf as $head) {

            preg_match('/(?:.*), (?P<day>\d{1,2})\. (?P<month>.*) (?P<year>\d{4})\s?(?:\((?P<week>A|B)-Woche\))?(?:.*)/', trim($head->titel), $matches);

            /**
             * ex.: XXX, 24. Oktober 2016 (A-Woche)
             * day => 24
             * month => Oktober
             * year => 2016
             * week => A (optional)
             */

            //TODO: A/B week, format output
            //TODO: var name "matches" sucks
            $monthNumeric = array_flip(self::$month)[$matches['month']];

            $week = isset($matches['week']) ? $matches['week'] : null;

            $date = DateTime::createFromFormat('d m Y', $matches['day'].' '.$monthNumeric.' '.$matches['year'])->format('Y-m-d');

            $changedClasses = trim($head->kopfinfo->aenderungk) ? explode(', ', trim($head->kopfinfo->aenderungk)) : array();
            $absentClasses = trim($head->kopfinfo->abwesendk) ? explode(', ', trim($head->kopfinfo->abwesendk)) : array();

            $changedTeachers = trim($head->kopfinfo->aenderungl) ? explode(', ', trim($head->kopfinfo->aenderungl)) : array();
            $absentTeachers = trim($head->kopfinfo->abwesendl) ? explode(', ', trim($head->kopfinfo->abwesendl)) : array();

            $changedRooms = trim($head->kopfinfo->abwesendr) ? explode(', ', trim($head->kopfinfo->abwesendr)) : array();
            $absentRooms = trim($head->kopfinfo->abwesendr) ? explode(', ', trim($head->kopfinfo->abwesendr)) : array();

            //TODO: check fo empty additions
            $additions[$date] = array_filter(array(
                'absentClasses' => $absentClasses,
                'changedClasses' => $changedClasses,
                'changedTeachers' => $changedTeachers,
                'absentTeachers' => $absentTeachers,
                'changedRooms' => $changedRooms,
                'absentRooms' => $absentRooms
            ));

            //$output[$date] = array();

            array_push($entries, array(
                'date' => $date,
                'week' => $week,
                'classes' => array_merge($changedClasses, $absentClasses)
            ));
        }

        //dd($entries);

        foreach ($entries as $key => $info) {

            $content = array();

            foreach ($changes->haupt[$key] as $entry) {

                $temp = array(
                    'class' => trim($entry->klasse),
                    'index' => trim($entry->stunde),
                    'subject' => trim($entry->fach),
                    'teacher' => trim($entry->lehrer) ? trim($entry->lehrer) : null,
                    'room' => trim($entry->raum) ? trim($entry->raum) : null,
                    'info' => trim($entry->info) ? trim($entry->info) : null,
                    'group' => null
                );

                //array search output could be 0
                if (array_search($temp['class'], $info['classes']) !== false) {
                    //class found in changed classes -> everything fine
                    array_push($content, $temp);
                } elseif (preg_match('/^(?P<class>[\S]+)\/\s(?P<group>[\S]+)$/', $temp['class'], $classMatches)) {
                    //class not found in changed classes -> year/course combination

                    /**
                     * ex.: 11/ en1
                     * class => 11
                     * group => en1
                     */

                    if (array_search($classMatches[1], $info['classes']) !== false) {
                        $temp['class'] = $classMatches['class'];
                        $temp['group'] = $classMatches['group'];

                        array_push($content, $temp);
                    } else {
                        //TODO: throw error
                        dd(array('yc', $temp));
                    }
                } elseif (preg_match('/(([^,]+),)+([^,]+)/', $temp['class'])) {
                    //class not found in changed classes -> multiple classes in same row
                    //ex.: 6/1,6/2,6/3,6/4,6/5,6/6

                    $classes = explode(',', $temp['class']);

                    //c&p code -> restructure code
                    //TODO: case -> multiple year/course combinations
                    foreach ($classes as $class) {
                        if (array_search($class, $info['classes']) !== false) {
                            $temp['class'] = $class;
                            array_push($content, $temp);
                        } else {
                            //TODO: throw error
                            dd(array('mc', $temp));
                        }
                    }
                } else {
                    //TODO: throw error
                    dd('fu', $temp, $info['classes'], array_search($temp['class'], $info['classes']));
                }
            }

            //$output[$info['date']] = $content;

            array_push($output, array('date' => $info['date'], 'week' => $info['week'], 'lessons' => $content));
        }

        //dd($output);

        return $output;
    }

    private static function mapChangesXMLStructure($xml) {
        $temp = array();
        $temp2 = array();
        $temp3 = array();

        foreach ($xml->children() as $line) {
            array_push($temp, $line->getName());
        }

        foreach ($temp as $key => $value) {
            $temp2 = array_keys($temp, 'kopf');
        }

        foreach ($temp2 as $key => $temp2Key) {
            $offset = $temp2Key;
            $length = isset($temp2[$key+1]) ? $temp2[$key+1] - $temp2Key : count($temp) - $offset;

            array_push($temp3, array_slice($temp, $offset, $length));
        }

        return $temp3;
    }
}