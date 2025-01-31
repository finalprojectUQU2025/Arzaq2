@extends('cms.parent')
@section('title', 'تعديل بيانات الصنف ')
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
                            <h3 class="brand-text font-weight-light">تعديل صنف {{ $product->name ?? '' }}</h3>
                        </div>

                        <form id="forme_rest">
                            <div class="card-body">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>إسم الصنف</label>
                                                <input type="text" class="form-control"id="name" name="name"
                                                    placeholder="إسم الصنف ..." value="{{ $product->name ?? '' }}">
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
                                                        id="fileName">{{ $product->image ?? '' }}</span>
                                                </div>
                                            </div>
                                        </div>



                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="active" id="active"
                                            @checked($product->active ?? '')>
                                        <label class="custom-control-label" for="active">فعال</label>
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
            id = {{ $product->id }}
            formData.append('_method', 'PUT');
            formData.append('name', document.getElementById('name').value);
            formData.append('active', document.getElementById('active').checked ? 1 : 0);
            if (document.getElementById('fileInput').files[0] != undefined) {
                formData.append('image', document.getElementById('fileInput').files[0]);
            }


            axios.post('/cms/admin/products/' + id, formData)
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
                    window.location.href = '/cms/admin/products';

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
