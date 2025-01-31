@extends('cms.parent')
@section('title', 'إنشاء اشعار جديدة')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">ارسال اشعار</h3>
                        </div>

                        <form id="forme_rest">
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>ارسال الاشعار الى</label>
                                            <select class="form-control" id="is_for_all" name="is_for_all"
                                                onchange="toggleUserSelect()">
                                                <option value="1">لكل الأشخاص</option>
                                                <option value="0">لشخص معين</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6" id="user_select_container" style="display: none;">
                                        <div class="form-group">
                                            <label>اختر المستخدم</label>
                                            <select class="form-control" id="account_id" name="account_id">
                                                <option value="">اختر مستخدم...</option>
                                                <!-- سيتم تعبئة المستخدمين هنا باستخدام JavaScript -->
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>عنوان الاشعار</label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                placeholder="عنوان الاشعار ...">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>تفاصيل الاشعار</label>
                                            <textarea class="form-control" rows="3" id="details" name="details" placeholder="تفاصيل الاشعار ..."></textarea>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="button" onclick="performstore()" class="btn btn-primary">ارسال</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        function toggleUserSelect() {
            var isForAll = document.getElementById('is_for_all').value;
            var userSelectContainer = document.getElementById('user_select_container');
            if (isForAll == '0') {
                userSelectContainer.style.display = 'block';
                fetchUsers();
            } else {
                userSelectContainer.style.display = 'none';
            }
        }

        function fetchUsers() {
            axios.get('/cms/admin/users') // افترض أنك ستستخدم هذا الرابط لجلب المستخدمين
                .then(function(response) {
                    let users = response.data.users;
                    let userSelect = document.getElementById('account_id');
                    userSelect.innerHTML = '<option value="">اختر مستخدم...</option>';
                    users.forEach(user => {
                        let option = document.createElement('option');
                        option.value = user.id;
                        option.text = user.name;
                        userSelect.appendChild(option);
                    });
                })
                .catch(function(error) {
                    console.error('Error fetching users:', error);
                });
        }

        function performstore() {
            Swal.fire({
                title: 'جاري التحميل...',
                text: 'يرجى الانتظار قليلاً',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formData = new FormData();
            formData.append('title', document.getElementById('title').value);
            formData.append('details', document.getElementById('details').value);
            formData.append('is_for_all', document.getElementById('is_for_all').value);
            let userId = document.getElementById('account_id').value;
            if (document.getElementById('is_for_all').value == '0' && userId) {
                formData.append('account_id', userId);
            }

            axios.post('/cms/admin/send/notifications', formData)
                .then(function(response) {
                    Swal.close();
                    Swal.fire({
                        title: 'بنجاح!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'حسنًا'
                    });
                    window.location.href = '/cms/admin/notifications';
                })
                .catch(function(error) {
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
