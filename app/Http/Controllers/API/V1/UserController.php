<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;
    public $unauthorizedStatus = 401;

    protected $req;
    protected $validationError = [];

    protected $userData = [];

    public function __construct(Request $request)
    {
        $this->req = $request;
    }

    private function loginProcess($role)
    {
        if ($this->loginValidation()) {
            if ($this->login()) {
                $user = $this->userData['data'];
                $success['token'] = $this->userData['token'];
                $success['name'] = $user['name'];
                $success['email'] = $user['email'];

                if ($role == 'customer' && $user['role'] == 'customer') {
                    return response()->json($success, $this->successStatus);
                } elseif ($role == 'admin' && $user['role'] == 'admin') {
                    return response()->json($success, $this->successStatus);
                } else {
                    $fail['error'] = "tidak ada hak akses " . $role;
                    return response()->json($fail, $this->unauthorizedStatus);
                }
            } else {
                return response()->json(
                    ['error' => 'password atau email salah'],
                    $this->unauthorizedStatus
                );
            }
        }

        return response()->json(
            $this->validationError,
            $this->unauthorizedStatus
        );
    }

    public function loginAdmin()
    {
        return $this->loginProcess('admin');
    }

    public function loginCustomer()
    {
        return $this->loginProcess('customer');
    }

    private function loginValidation()
    {
        $validator = Validator::make($this->req->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $this->validationError = ['error' => $validator->errors()];
            return false;
        }

        return true;
    }

    private function login()
    {
        if (
            Auth::attempt([
                'email' => $this->req->email,
                'password' => $this->req->password
            ])
        ) {
            $user = Auth::user();
            $success['token'] = $user->createToken('TokenUser')->accessToken;
            $success['data'] = $user;
            $this->userData = $success;
            return true;
        }

        return false;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['role'] = 'customer';
        $user = User::create($input);
        $success['token'] = $user->createToken('TokenUser')->accessToken;
        $success['name'] = $user->name;
        return response()->json(['success' => $success], $this->successStatus);
    }
}
