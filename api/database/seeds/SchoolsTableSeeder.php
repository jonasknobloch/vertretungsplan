<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.04.17
 * Time: 23:41
 */

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\School;

class SchoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*School::create([
            'key' => 4340154,
            'name' => 'Gymnasium Klotzsche'
        ]);

        School::create([
            'key' => 4340451,
            'name' => 'Gymnasium Bühlau'
        ]);

        School::create([
            'key' => 4340469,
            'name' => 'Gymnasium Bürgerwiese'
        ]);*/

        factory(App\School::class, 3)->create()->each(function ($school) {
            $school->save();
        });
    }
}