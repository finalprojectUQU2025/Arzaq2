<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} | تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
            direction: rtl;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .login-box {
            background-color: rgba(255, 255, 255, 0.542);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .login-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f7f7f7;
            padding: 20px;
        }



        .image-section {
            flex: 1;
            background: url('{{ asset('cms/dist/img/photo_5197535986307950046_x__1_-removebg-preview.png') }}') no-repeat center center;
            background-size: cover;
        }

        .login-box h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .welcome-message {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 18px;
        }

        .form-control {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .btn-login {
            background-color: #4a90e2;
            border-color: #4a90e2;
            color: white;
            border-radius: 8px;
            padding: 10px;
            width: 100%;
        }

        .btn-login:hover {
            background-color: #357ABD;
            border-color: #357ABD;
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 10px;
            color: #999;
        }

        .forgot-password:hover {
            color: #333;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="login-section">
            <div class="login-box">
                <div class="welcome-message">مرحبًا بكم في منصة أرزاق</div>
                <h3>تسجيل الدخول إلى حسابك</h3>
                <form>
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" placeholder="أدخل بريدك الإلكتروني">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" placeholder="أدخل كلمة المرور">
                    </div>
                    <button type="button" onclick="performLogin()" class="btn btn-login">تسجيل الدخول</button>
                    <a class="forgot-password" onclick="showForgotPasswordModal('{{ $guard }}')">هل نسيت كلمة
                        المرور؟</a>
                </form>
            </div>
        </div>
        <div class="image-section"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/axios@0.27.2/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function performLogin() {
            Swal.fire({
                title: 'تحميل...',
                text: 'الرجاء الانتظار لحظة.',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            let formData = new FormData();
            formData.append('email', document.getElementById('email').value);
            formData.append('password', document.getElementById('password').value);
            axios.post('/cms/login', formData)
                .then(function(response) {
                    console.log(response);
                    Swal.close();
                    Swal.fire({
                        title: 'بنجاح!',
                        text: response.data.message,
                        icon: 'success',
                    });
                    window.location.href = '/cms/admin/';
                })
                .catch(function(error) {
                    console.log(error);
                    Swal.close();
                    Swal.fire({
                        title: 'حدث خطأ!',
                        text: error.response.data.message,
                        icon: 'error',
                        showCloseButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'حسناً'
                    });
                });
        }

        function showForgotPasswordModal(guard) {
            Swal.fire({
                title: 'هل نسيت كلمة المرور؟',
                html: `<input type="email" id="forgot_email" class="form-control" placeholder="أدخل بريدك الإلكتروني">`,
                showCancelButton: true,
                confirmButtonText: 'إرسال',
                cancelButtonText: 'إلغاء',
                preConfirm: () => {
                    const email = Swal.getPopup().querySelector('#forgot_email').value;
                    if (!email) {
                        Swal.showValidationMessage(`يرجى إدخال البريد الإلكتروني`);
                    }
                    return {
                        email: email
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'تحميل...',
                        text: 'الرجاء الانتظار لحظة.',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    let formData = new FormData();
                    formData.append('email', result.value.email);
                    formData.append('guard', guard);
                    axios.post('/cms/forgot-password', formData)
                        .then(function(response) {
                            Swal.fire('تم!', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.',
                                'success');
                        })
                        .catch(function(error) {
                            Swal.fire({
                                title: 'حدث خطأ!',
                                text: error.response.data.message,
                                icon: 'error',
                                confirmButtonText: 'حسناً'
                            });
                        });
                }
            });
        }
    </script>
</body>

</html>
