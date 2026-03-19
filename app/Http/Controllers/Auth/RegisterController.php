<?php

namespace App\Http\Controllers\Auth;

use Validator;
use App\Models\Agent;
use Exception;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserProfiles;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

/**
 * Class RegisterController
 * @package %%NAMESPACE%%\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $settings = Setting::allConfigsKeyValue();
        return view('admin.auth.register', compact('settings'));
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'username' => 'unique:users',
                'email'    => 'unique:users',
                'password' => 'min:8',
                'password_confirmation' => 'same:password'
            ],
            [
                'email.unique' => 'Email đã tồn tại!',
                'username.unique' => 'Tên tài khoản đã tồn tại!',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự!',
                'password_confirmation.same' => 'Giá trị xác nhận mật khẩu không khớp!'
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        try {
            $fields = [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ];
            if (config('auth.providers.users.field', 'email') === 'username' && isset($data['username'])) {
                $fields['username'] = $data['username'];
            }
            //data agent
            $agentFields = [
                'name' => $data['name'],
                'phone' => $data['username'],
                'email' => $data['email']
            ];

            //prepare
            $role = $data['role'];

            $profile = $data['profile'];
            if (!empty($profile['birthday'])) {
                $profile['birthday'] = Carbon::createFromFormat(config('settings.format.date'), $profile['birthday'])->format('Y-m-d');
            }
            //nếu đăng ký đại lý thì active = false, cần admin tự active
            if ($role === config('settings.roles.agent_admin')) {
                $fields['active'] = config("settings.inactive");
                //create agent
                $agent = Agent::create($agentFields);
                if ($agent) {
                    $fields['agent_id'] = $agent->id;
                }
            }
            //add user
            $user = User::create($fields);
            //add profile
            $user->profile()->save(new UserProfiles($profile));
            //role
            $user->assignRole($role);

            return $user;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
