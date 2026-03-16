<?php
/*
 * This file is part of the KitaMatch app.
 *
 * (c) Sven Giegerich <sven.giegerich@mailbox.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 /*
 |--------------------------------------------------------------------------
 | Register Controller
 |--------------------------------------------------------------------------
 */

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Http\Requests\ReCaptchataRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

/**
* This controller handles the registration of new users as well as their
* validation and creation.
*/
class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
        return Validator::make($data, [
        //   'g-recaptcha-response'=>'required|recaptcha',
          'email' => 'required|string|max:255|unique:users',
          'password' => 'required|string|min:6|confirmed'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'account_type' => $data['accountType'],
            'password' => Hash::make($data['password'])
        ]);

        if ($data['accountType'] == 1) {
            //account-type is guardian
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add(['uid' => $user->id]);
            app('App\Http\Controllers\GuardianController')->storeByUser($request);
        } else if ($data['accountType'] == 4) {
            //account-type is provider
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add(['uid' => $user->id]);
            app('App\Http\Controllers\ProviderController')->storeByUser($request);
        } else if ($data['accountType'] == 2 || $data['accountType'] == 3) {
            //account-type private or public
            //2: public, 3: private
            $request = new Request();
            $request->setMethod('POST');
            if ($data['accountType'] == 2) {
                $p_kind = 1;
                $coordination = 1;
            } else if ($data['accountType'] == 3) {
                $p_kind = 2;
                $coordination = 0;
            }
            $request->request->add(['uid' => $user->id,
                                   'coordination' => $coordination,
                                   'p_kind' => $p_kind]);

            app('App\Http\Controllers\ProgramController')->storeByUser($request);
        } else {
            //error
        }

        return $user;
    }

    /**
    * Stores the created user, after test of ReCaptacha by request.
    *
    * @param  App\Http\Requests\ReCaptchataRequest $request request
    * @return \App\User
    */
    // public function store(ReCaptchataRequest $request) {
    //     $user = User::create([
    //         'email' => $request->email,
    //         'account_type' => $request->account_type,
    //         'password' => Hash::make($request->password)
    //     ]);
    //     return $user;
    // }

    /**
    * Stores a user created by provider.
    *
    * @param Illuminate\Http\Request $request request
    * @return \App\User
    */
    public function storeByProvider(Request $request) {
        $user = User::create([
            'email' => $request->email,
            'account_type' => $request->account_type,
            'password' => Hash::make($request->password)
        ]);
        return $user;
    }

    /**
    * A user friendly, strong password generator PHP function. Adopted from Tyler Hall, see https://gist.github.com/tylerhall/521810.
    *
    * @param string $length
    * @return string $password
    */
    public function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds') {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }
}
