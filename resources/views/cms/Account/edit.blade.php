@extends('cms.parent')
@section('title', 'تعديل بيانات المشرفين ')
@section('style')
    <style>
        .custom-file-upload {
            display: flex;
            align-items: center;
            border: 2px solid #495057;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            position: relative;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-label {
            flex-grow: 1;
            text-align: left;
            padding: 5px;
            color: #495057;
        }

        .file-input:hover+.file-label,
        .file-input:focus+.file-label {
            color: #495057;
        }

        .file-input:active+.file-label {
            color: #495057;
        }
    </style>

@endsection
@section('main-content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">تعديل بيانات {{ $admin->name ?? '' }}</h3>
                        </div>

                        <form id="forme_rest">
                            <div class="card-body">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>إسم المشرف</label>
                                                <input type="text" class="form-control"id="name" name="name"
                                                    placeholder="إسم المشرف ..." value="{{ $admin->name ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>البريد الالكتروني</label>
                                                <input type="email" class="form-control"id="email" name="email"
                                                    placeholder="البريد الالكتروني ..." value="{{ $admin->email ?? '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>رقم الهاتف</label>
                                                <input type="number" class="form-control"id="phone" name="phone"
                                                    placeholder="رقم الهاتف ..." value="{{ $admin->phone ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>رقم الهوية</label>
                                                <input type="number" class="form-control"id="id_number" name="id_number"
                                                    placeholder="رقم الهوية ..." value="{{ $admin->id_number ?? '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>الدولة</label>
                                                <select class="form-control country" style="width: 100%;" id="country_id">
                                                    <option>اختر مما يلي</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}" @selected($country->id == $admin->country_id)>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="fileInput" style="display: block; margin-bottom: 10px;">اختر
                                                    ملفًا لتحميله</label>
                                                <div class="custom-file-upload">
                                                    <input type="file" id="fileInput" name="fileInput"
                                                        class="file-input">
                                                    <span class="file-label" style="text-align: right;"
                                                        id="fileName">{{ $admin->image ?? '' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" onclick="performstore()" class="btn btn-primary">تعديل</button>
                                </div>

                        </form>
                    </div>

                </div>
            </div>
    </section>

@endsection
@section('script')
    <script>
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileName');

        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
                fileNameDisplay.textContent = "لم يتم اختيار أي ملف";
            }
        });
    </script>
    <script>
        $(function() {
            bsCustomFileInput.init();
        });

        function performstore() {
            // عرض شاشة التحميل
            Swal.fire({
                title: 'جاري التحميل...',
                text: 'يرجى الانتظار قليلاً',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // إظهار شاشة التحميل
                }
            });

            let formData = new FormData();
            id = {{ $admin->id }}
            formData.append('_method', 'PUT');
            formData.append('name', document.getElementById('name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('phone', document.getElementById('phone').value);
            formData.append('id_number', document.getElementById('id_number').value);
            formData.append('country_id', document.getElementById('country_id').value);
            if (document.getElementById('fileInput').files[0] != undefined) {
                formData.append('image', document.getElementById('fileInput').files[0]);
            }
            axios.post('/cms/admin/admins/' + id, formData)
                .then(function(response) {
                    console.log(response);
                    Swal.close();
                    Swal.fire({
                        title: 'بنجاح!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'حسنًا'
                    });
                    // document.getElementById('forme_rest').reset();
                    window.location.href = '/cms/admin/admins';

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
