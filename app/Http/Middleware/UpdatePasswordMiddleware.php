<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdatePasswordMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = session()->get('guard', 'admin');  // الحصول على الـ guard من الجلسة أو تعيينه كـ 'admin'
        // تحقق من حالة تسجيل الدخول للمستخدم وإذا كانت قيمة 'updatePassword' هي false
        if (auth($guard)->check() && auth($guard)->user()->updatePassword == false) {
            // إذا كانت قيمة 'updatePassword' هي false، يتم توجيه المستخدم إلى صفحة تغيير كلمة المرور
            return response()->view('cms.Auth.updatePassword');
        }

        return $next($request);  // إذا لم تكن هناك حاجة لإعادة التوجيه، استمر في تنفيذ الطلب
    }
}
