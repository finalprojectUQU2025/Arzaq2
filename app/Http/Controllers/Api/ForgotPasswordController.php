<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Mail\SendCodeVerifiy;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordController extends Controller
{





    public function sendEmailCode(Request $request)
    {
        $validator = Validator($request->all(), [
            'guard' => 'required|string|in:accountApi',
            'email' => 'required|string|exists:accounts,email',
        ]);

        if (!$validator->fails()) {
            $user = $this->getUser($request, $request->input('guard'));
            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => Messages::getMessage('NO_ACCOUNT'),
                ], Response::HTTP_BAD_REQUEST);
            }
            $code = random_int(10000, 99999);
            $user->forgetPassword_code = Hash::make($code);
            $user->save();
            Mail::to($user->email)->send(new SendCodeVerifiy($user, $code));
            return response()->json([
                'status' => true,
                'message' => Messages::getMessage('FORGET_PASSWORD_SUCCESS'),
                'code' => $code,
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function checkCodeForge(Request $request)
    {
        $validator = Validator($request->all(), [
            'guard' => 'required|string|in:accountApi',
            'email' => 'required|string|exists:accounts,email',
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

            if ($user->status = 'verefy') {
                $storedCode = $user->forgetPassword_code;
                $inputCode = $request->input('code');
                if (Hash::check($inputCode, $storedCode)) {
                    $user->forgetPassword_code = null;
                    $user->save();
                    return response()->json([
                        'status' => true,
                        'message' => Messages::getMessage('PASSWORD_RESET_CODE_CORRECT'),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => Messages::getMessage('PASSWORD_RESET_CODE_ERROR'),
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


    public function resetPassword(Request $request)
    {
        //password_confirmation
        $validator = Validator($request->all(), [
            'guard' => 'required|string|in:accountApi',
            'email' => 'required|string|exists:accounts,email',
            'password' => 'required|min:6|max:12|confirmed',
        ]);

        if (!$validator->fails()) {
            $user = $this->getUser($request, $request->input('guard'));
            $user->password = Hash::make($request->input('password'));
            $isSaved = $user->save();
            return response()->json([
                'status' => $isSaved ? true : false,
                'message' => $isSaved ? Messages::getMessage('RESET_PASSWORD_SUCCESS') : Messages::getMessage('PASSWORD_SEND_FAILEDس'),

            ], $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
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
}
