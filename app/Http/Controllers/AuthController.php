<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginView(Request $request)
    {
        $request->merge(['guard' => $request->guard]);
        $validator = Validator($request->all(), [
            'guard' => 'required|string|in:admin,mandub'
        ]);
        session()->put('guard', $request->input('guard'));
        if (!$validator->fails()) {
            // $setting = Setting::first();
            // $logoImage = Storage::url($setting->logo ?? '');
            $logoImage = null;
            return response()->view('cms.Auth.login', [
                'guard' => $request->input('guard'),
                'logoImage' => $logoImage,
            ]);
        } else {
            return response()->view('cms.Auth.Not-Found');
        }
    }

    public function login(Request $request)
    {
        $validator = validator([
            'email' => 'required|email',
            'password' => 'required|string|min:3',
            'remember' => 'required|boolean'
        ]);
        $guard = session()->get('guard');
        if (!$validator->fails()) {
            $crednrtials = [
                'email' => $request->input('email'),
                'password' => $request->input('password')
            ];
            if (Auth::guard($guard)->attempt($crednrtials, $request->input('remember'))) {
                $user = auth($guard)->user();
                if (!$user->blocked) {
                    return response()->json(
                        [
                            'message' => 'تم التسجيل بنجاح'
                        ],
                        Response::HTTP_OK
                    );
                } else {
                    return response()->json(
                        [
                            'message' => 'تم حظر الحساب الرجاء التواصل مع الدعم'
                        ],
                        Response::HTTP_BAD_REQUEST
                    );
                }
            } else {
                return response()->json(
                    [
                        'message' => 'فشل تسجيل الدخول، تحقق من تفاصيل تسجيل الدخول'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } else {
            return response()->json(
                ['message' => $validator->getMessageBag()->first()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function showForgotpassword($guard)
    {
        return response()->view('cms.Auth.forgot', ['guard' => $guard]);
    }


    public function sendRestLink(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required|email',
        ]);
        if (!$validator->fails()) {
            $email =  Admin::where('email', $request->get('email'))->first();
            $broker = 'admins';
            if (!is_null($email)) {
                $status = Password::broker($broker)->sendResetLink(
                    $request->only('email')
                );

                return $status === Password::RESET_LINK_SENT
                    ? response()->json(['message' => __($status)], Response::HTTP_OK)
                    : response()->json(['message' => __($status)], Response::HTTP_BAD_REQUEST);
            } else {
                return response()->json(['message' => 'لم يتم العثور على البريد الإلكتروني'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return response()->json(
                ["message" => $validator->getMessageBag()->first()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }


    public function shoewResetPassword(Request $request, $token)
    {

        return response()->view('cms.Auth.reset-password', ['token' => $token, 'email' => $request->input('email')]);
    }


    public function resetPassword(Request $request)
    {
        //
        $validator = validator($request->all(), [
            'email' => 'required|email|exists:admins,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string',

        ]);
        if (!$validator->fails()) {
            $broker = 'admins';
            $status = Password::broker($broker)->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));
                    $user->save();
                    event(new PasswordReset($user));
                }
            );
            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => __($status)], Response::HTTP_OK)
                : response()->json(['message' => __($status)], Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json(
                [
                    'message' => $validator->getMessageBag()->first()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function editPassword()
    {
        return response()->view('cms.Auth.updatePassword');
    }


    public function updatePassword(Request $request)
    {
        $guard = session('guard');
        $guard = auth($guard)->check() ? $guard : null;
        $validator = validator($request->all(), [
            'password' => 'required|current_password:' . $guard,
            'new_password' =>  ['required', 'confirmed'],
        ]);

        if (!$validator->fails()) {
            $superAdmin = $request->user();
            $superAdmin->forceFill([
                'password' => Hash::make($request->input('new_password')),
            ]);
            $isSaved = $superAdmin->save();
            if ($isSaved) {
                $superAdmin->updatePassword = true;
                $superAdmin->save();
            }
            return response()->json(
                ['message' => $isSaved ? 'تم تغيير كلمة المرور بنجاح' : 'لم يتم تغيير كلمة المرور بنجاح'],
                $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(['message' => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        }
    }


    public function logout(Request $request)
    {
        $guard = session('guard');
        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        return redirect()->route('auth.login', $guard);
    }
}
