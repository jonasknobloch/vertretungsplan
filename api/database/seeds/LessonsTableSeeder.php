<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.04.17
 * Time: 23:41
 */

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\ScheduleLesson;
use App\School;

class LessonsTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ScheduleLesson::create([
            'day' => 1,
            'class' => '5c',
            'index' => 1,
            'subject' => 'Ge',
            'teacher' => 'Gr',
            'room' => '109',
            'week' => 'A',
            'school_id' => School::all()->random()->id
        ]);
    }
}