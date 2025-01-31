@component('mail::message')
<div dir="rtl">
# مرحباً {{ $name }}
يسعدنا انضمامك إلينا ونتمنى لك التوفيق والنجاح.<br><br>
@component('mail::panel')
<div style="text-align: right;">
    لتتمكن من الوصول إلى لوحة التحكم الخاصة بـ {{ $name }}، إليك بيانات حسابك:
    <br><br>
    - البريد الإلكتروني: {{ $email }}<br>
    - كلمة المرور: {{ $password }}
</div>
@endcomponent
@component('mail::button', ['url' => config('app.url') . '/login', 'dir' => 'rtl'])
    لوحة إدارة النظام
@endcomponent
شكراً لك،<br>
{{ config('app.name') }}
</div>
@endcomponent
