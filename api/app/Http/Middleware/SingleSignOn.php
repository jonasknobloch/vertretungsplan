<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Carbon\Carbon;

use App\User;
use App\UserAuthToken;

use App\Student;

use App\Http\Controllers\UserAuthTokenController;

class SingleSignOn
{
    const USER_ROLES = array(
        'guest' => [
            'keyRequired' => false,
            'hasAccessTo' => []
        ],
        'student' => [
            'keyRequired' => true,
            'hasAccessTo' => ['guest']
        ],
        'staff' => [
            'keyRequired' => true,
            'hasAccessTo' => ['guest']
        ],
        'manager' => [
            'keyRequired' => true,
            'hasAccessTo' => ['guest', 'student', 'staff']
        ],
        'admin' => [
            'keyRequired' => false,
            'hasAccessTo' => ['guest', 'student', 'staff', 'manager']
        ],
    );

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $requiredRole
     * @return mixed
     */

    public function handle(Request $request, Closure $next, $requiredRole)
    {

        $requestedKey = self::USER_ROLES[$requiredRole]['keyRequired'] ? self::getRouteParameterByKey($request, 'schoolId') : null;

        $userAuthTokenController = new UserAuthTokenController($request);
        $user = $userAuthTokenController->checkForValidAuthTokenAndReturnUserOnSuccess();

        if (!$user) {
            // starting session
            session_start();

            // checking for empty session
            if (count($_SESSION) === 0) {
                return response('Empty session.', 401);
            }

            // retrieving session data
            $ssoId = array_key_exists('ssoId', $_SESSION) ? $_SESSION['ssoId'] : null;
            $email = array_key_exists('email', $_SESSION) ? $_SESSION['email'] : null;
            $firstName = array_key_exists('firstName', $_SESSION) ? $_SESSION['firstName'] : null;
            $lastName = array_key_exists('lastName', $_SESSION) ? $_SESSION['lastName'] : null;
            $roles = array_key_exists('roles', $_SESSION) ? $_SESSION['roles'] : array();
            $schools = array_key_exists('schools', $_SESSION) ? $_SESSION['schools'] : array();

            if (!$ssoId) {
                return response('Username missing.', 401);
            }

            if (in_array('student', $roles) or in_array('staff', $roles)) {
                $user = User::find($ssoId);

                // create user if it does not exist
                if (!$user) {
                    $user = User::create([
                        'id' => $ssoId,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                    ]);

                    // add relation if user is student
                    if (in_array('student', $roles)) {

                        Student::create([
                            'user_id' => $user->id,
                        ]);

                        foreach ($schools as $school => $classes) {
                            foreach ($classes as $class) {
                                $user->student->schools()->attach($school, ['class' => $class]);
                            }
                        }
                    }
                }

                $userAuthToken = $user->authToken()->first();

                if (!$userAuthToken) {
                    $userAuthToken = $userAuthTokenController->setAuthTokenByUserId($user->id);
                }

                if ($userAuthToken) {
                    setcookie('schedule_auth_token', $userAuthToken->auth_token, Carbon::parse('first day of August this year')->timestamp);
                }

            } else {
                // creating empty user (not saved)
                $user = new User();

                $user->id = $ssoId;
                $user->firstName = $firstName;
                $user->lastName = $lastName;
                $user->email = $email;
                $user->role = 'guest';
            }
        }

        if ($user->role === 'student') {

            $classes = [];

            foreach ($user->student->schools()->get(['school_id', 'class']) as $item) {
                if (!array_key_exists($item['school_id'], $classes)) {
                    $classes[$item['school_id']] = [];
                }
                array_push($classes[$item['school_id']], $item['class']);
            }

            $user->student->classes = $classes;
            $schools = $classes; // TODO: rename $school and $class ([schoolId1 => ['class1', 'class2']])
            $user->student->load('schools');
        }

        if ($user->role === 'manager') {

            $user->manager->load('schools');
            $schools = array_flip($user->manager->schools()->get()->pluck('id')->toArray());
        }

        if ($user->role === 'admin') {

            $user->load('admin');
        }

        //check if user has the required role
        if (!($requiredRole === $user->role) and !in_array($requiredRole, self::USER_ROLES[$user->role]['hasAccessTo'])) {
            return response('Forbidden.', 403);
        }

        //check if user has access to the requested school
        if (self::USER_ROLES[$requiredRole]['keyRequired']) {
            //TODO: check managerSchoolKey
            if (!array_key_exists($requestedKey, $schools)) {
                return response('Forbidden.', 403);
            }
        }

        //TODO: $request->get() uses attributes -> post attributes are overwritten -> is this necessary?
        //TODO: school name to attributes or additional call to /schools/{key}

        //setting session data as request attributes
        $request->attributes->add(['user' => $user]);
        return $next($request);
    }

    public static function getRouteParameterByKey(Request $request, $key)
    {
        //https://github.com/laravel/lumen-framework/issues/119
        $route = $request->route();
        return is_array($route) ? $route[2][$key] : $route->parameter($key);
    }

    public static function getRouteParameters(Request $request)
    {
        return is_array($request->route()) ? $request->route()[2] : $request->route()->parameters();
    }
}
