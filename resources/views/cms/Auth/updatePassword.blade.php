@extends('cms.parent')
@section('title', 'لوحة التحكم')
@section('main_name', 'إنشاءات')
@section('small_page_name', 'إنشاء مستخدم')
@section('small_page_admin', 'إنشاء مدير')
@section('style')

@endsection
@section('main-content')

    <!-- المحتوى الرئيسي -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- العمود الأيسر -->
                <div class="col-md-12">
                    <!-- رسالة التحذير -->
                    <div class="alert alert-warning" role="alert">
                        يجب عليك تغيير كلمة المرور الخاصة بك لتتمكن من الاستمرار.
                    </div>
                    <!-- عناصر النموذج العامة -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">تغيير كلمة المرور</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- بداية النموذج -->

                        <form>
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="current_password">كلمة المرور الحالية</label>
                                    <input type="password" class="form-control" id="current_password"
                                        placeholder="كلمة المرور الحالية">
                                </div>

                                <div class="form-group">
                                    <label for="new_password">كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control " id="new_password"
                                        placeholder="كلمة المرور الجديدة">
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation">تأكيد كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control" id="new_password_confirmation"
                                        placeholder="تأكيد كلمة المرور الجديدة">
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="button" onclick="performupdatepassword()"
                                    class="btn btn-primary">تحديث</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
    </section>
@endsection
@section('script')
    <script>
        function performupdatepassword() {
            axios.post('/cms/admin/update-password', {
                    password: document.getElementById('current_password').value,
                    new_password: document.getElementById('new_password').value,
                    new_password_confirmation: document.getElementById('new_password_confirmation').value,
                })
                .then(function(response) {
                    console.log(response);
                    Swal.close();
                    Swal.fire({
                        title: 'بنجاح!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'حسنًا'
                    });
                    window.location.href = '/cms/admin';
                })
                .catch(function(error) {
                    console.log(error);
                    Swal.close();
                    Swal.fire({
                        title: 'حدث خطأ!',
                        text: error.response.data.message,
                        icon: 'error',
                        confirmButtonText: 'حسنًا'
                    });
                });
        }
    </script>
@endsection
