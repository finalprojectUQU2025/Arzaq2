<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator($request->all(), [
            'guard' => 'required|string|in:accountApi',
            'email' => 'required|string|exists:accounts,email',
            'password' => 'required|string',
        ]);

        if (!$validator->fails()) {
            $guard = $request->input('guard');
            $user = $this->getUser($request, $guard);
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => Messages::getMessage('NO_ACCOUNT'),
                ], Response::HTTP_BAD_REQUEST);
            }
            if ($user->status == "verefy") {

                if (Hash::check($request->input('password'), $user->password)) {
                    return $this->generateToken($request, $user);
                } else {
                    return response()->json(
                        ['message' => Messages::getMessage('ERROR_PASSWORD'),],
                        Response::HTTP_BAD_REQUEST,
                    );
                }
            } else {
                $message = Messages::getMessage('NO_ACCOUNT');
                if ($user->status == "blocked") {
                    $message = Messages::getMessage('BLOCK_ACCOUNT');
                }
                if ($user->status == "unVerefy") {
                    $message = Messages::getMessage('NOT_VERIFIED');
                }
                return response()->json([
                    'status' => false,
                    'message' => $message,
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->getMessageBag()->first(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }


    public function checkCode(Request $request)
    {
        $validator = Validator($request->all(), [
            'guard' => 'required|string|in:accountApi',
            'email' => 'required|string',
            'code' => 'required|string'
        ]);

        if (!$validator->fails()) {
            $user = $this->getUser($request, $request->input('guard'));
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => Messages::getMessage('NO_ACCOUNT'),
                ], Response::HTTP_BAD_REQUEST);
            }
            if ($user->status = 'unVerefy') {
                $storedCode = $user->verification_code;
                $inputCode = $request->input('code');
                if (Hash::check($inputCode, $storedCode)) {
                    $user->status = 'verefy';
                    $user->verification_code = null;
                    $user->save();
                    return $this->generateToken($request, $user, 'check');
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => Messages::getMessage('FAILD_CHECKED'),
                    ], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => Messages::getMessage('FAILD_CHECKED'),
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function reSendVerifiyCode(Request $request)
    {
        $validator = Validator($request->all(), [
            'guard' => 'required|string|in:accountApi',
            'email' => 'required|string',
        ]);

        if (!$validator->fails()) {
            $user = $this->getUser($request, $request->input('guard'));
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => Messages::getMessage('NO_ACCOUNT'),
                ], Response::HTTP_BAD_REQUEST);
            }
            // $code = random_int(10000, 99999);
            $code = 55555;
            $user->verification_code = Hash::make($code);
            $user->save();
            // Mail::to($user->email)->send(new SendCodeVerifiy($user, $code));
            return response()->json([
                'status' => true,
                'message' => Messages::getMessage('AUTH_CODE_SENT'),
                'code' => $code,
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    private function getUser(Request $request, $guard)
    {
        $user = null;
        if ($guard === 'accountApi') {
            $user = Account::where('email', $request->get('email'))->first();
        }
        return $user;
    }


    private function generateToken(Request $request, $user, $type = 'login')
    {
        try {
            $guard = $request->input('guard');
            $token = $user->createToken($guard);
            $user->setAttribute('token', $token->accessToken);
            return response()->json([
                'status' => true,
                'message' => Messages::getMessage($type == 'login' ? 'LOGGED_IN_SUCCESSFULLY' : 'SUCCESS_CHECKED'),
                'data' => new AccountResource($user),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            $message = Messages::getMessage($type == "login" ? 'LOGIN_IN_FAILED' : 'FAILD_CHECKED');
            return response()->json([
                'status' => false,
                'message' => $message,
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function logout(Request $request)
    {
        $guard = $request->input('guard');
        $token = auth($guard)->user()->token();
        $isRevoked = $token->revoke();
        $user = auth($guard)->user();
        return response()->json([
            'status' => $isRevoked,
            'message' => $isRevoked ?
                Messages::getMessage('LOGGED_OUT_SUCCESSFULLY')
                : Messages::getMessage('LOGGED_OUT_FAILED'),
        ], $isRevoked ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
