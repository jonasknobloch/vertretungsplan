<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// cors middleware
$router->group(['middleware' => 'cors'], function () use ($router) {

    $router->options('/{any:.*}', function (){
        return response('', 200);
    });

    // get lumen version
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    // token auth test
    $router->group(['prefix' => 'mulm'], function ($router) {
        $router->group(['middleware' => 'auth'], function () use ($router) {
            $router->get('foo', function () {
                return 'bar';
            });
        });
    });

    // api version prefix
    $router->group(['prefix' => 'v1'], function () use ($router) {

        //TODO: remove sso:guest
        //public routes -> no key required
        $router->group(['middleware' => 'sso:guest'], function () use ($router) {
            $router->get('dates', 'DateController@getSchoolDatesByRange');
        });

        //pupil routes -> key required
        $router->group(['middleware' => 'sso:student'], function () use ($router) {

            $router->get('schedules/{schoolId:\d{7}}/lessons/{class}/changes/{date:\d{4}-\d{2}-\d{2}}', 'ScheduleLessonController@getLessonsWithChanges');

            $router->post('students/{studentUserId}/schools/{schoolId:\d{7}}/lessons', 'StudentLessonController@addLesson');
            $router->delete('students/{studentUserId}/schools/{schoolId:\d{7}}/lessons/{id:\d+}', 'StudentLessonController@deleteLesson');
        });

        //manager routes -> key required
        $router->group(['middleware' => 'sso:manager'], function () use ($router) {

            $router->get('schedules/{schoolId:\d{7}}', 'ScheduleController@getSchedule');
            $router->delete('schedules/{schoolId:\d{7}}', 'ScheduleController@deleteSchedule');

            $router->get('lessons/{schoolId:\d{7}}/available', 'LessonController@getAvailabilityByKey');
            $router->get('lessons/{schoolId:\d{7}}/info', 'LessonController@getInfoByKey');

            $router->get('changes/{schoolId:\d{7}}', 'ChangeController@showChanges');
            $router->delete('changes/{schoolId:\d{7}}/{date:\d{4}-\d{2}-\d{2}}', 'ChangeController@deleteChangeByDate');

            $router->post('uploads/{schoolId:\d{7}}', 'UploadController@uploadFile');
        });

        $router->group(['middleware' => 'auth'], function () use ($router) {
            $router->get('changes/{date:\d{4}-\d{2}-\d{2}}', 'ChangeController@getChanges');
        });

        //admin routes -> no key required
        $router->group(['middleware' => 'sso:admin'], function () use ($router) {

            // schools routes
            $router->get('schools', 'SchoolController@getSchools');
            $router->post('schools', 'SchoolController@addSchool');
            $router->get('schools/{id:\d+}', 'SchoolController@getSchool');
            $router->put('schools/{id:\d+}', 'SchoolController@editSchool');
            $router->delete('schools/{id:\d+}', 'SchoolController@deleteSchool');

            // users routes
            $router->get('users', 'UserController@getUsers');
            $router->post('users', 'UserController@addUser');
            $router->get('users/{id}', 'UserController@getUser');
            $router->put('users/{id}', 'UserController@editUser');
            $router->delete('users/{id}', 'UserController@deleteUser');

            // admin routes
            $router->get('/admins', 'AdminController@getAdmins');
            $router->post('/admins', 'AdminController@addAdmin');
            $router->get('/admins/{userId}', 'AdminController@getAdmin');
            $router->delete('/admins/{userId}', 'AdminController@deleteAdmin');

            // manager routes
            $router->get('managers', 'ManagerController@getManagers');
            $router->post('managers', 'ManagerController@addManager');
            $router->get('managers/{userId}', 'ManagerController@getManager');
            $router->delete('managers/{userId}', 'ManagerController@deleteManager');
            $router->post('managers/{userId}/schools', 'ManagerController@addSchoolToManager');
            $router->delete('managers/{userId}/schools/{schoolId:\d{7}}', 'ManagerController@deleteSchoolFromManager');
        });

        //experimental
        $router->group(['middleware' => 'sso:guest'], function () use ($router) {
            $router->get('foo/{key:\d{7}}', 'ExampleController@foo');
            $router->get('bar/{key:\d{7}}', 'ExampleController@bar');

            $router->delete('delete', 'ExampleController@delete');

            $router->get('status', function (\Illuminate\Http\Request $request) {
                return $request->attributes->all();
            });
        });
    });
});