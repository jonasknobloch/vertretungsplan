<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.07.17
 * Time: 14:00
 */

namespace App\Http\Controllers;

class DateController extends Controller
{
    public function getSchoolDatesByRange()
    {

        // TODO: school holidays
        // TODO: use carbon

        $range = $this->request->has('range') ?  intval($this->request->get('range')) : 3;

        setlocale(LC_ALL, 'de_DE.utf8');

        $dates = array();
        $rangeCount = 0;

        $today = date('Y-m-d');

        while (count($dates) < $range) {

            $date = date('Y-m-d', strtotime($today . '+' . $rangeCount . 'day'));
            $dateString = strftime('%e. %B %Y', strtotime($date));
            $dayName = strftime('%A', strtotime($date));

            $weekDay = date('w', strtotime($date));

            if ($weekDay != 0 and $weekDay != 6) {
                array_push($dates,
                    array(
                        'date' => $date,
                        'dateString' => $dateString,
                        'weekDay' => $dayName
                    )
                );
            }

            $rangeCount++;
        }

        return $dates;

    }
}