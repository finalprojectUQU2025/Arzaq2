@extends('cms.parent')
@section('title', 'تعديل بيانات ؤ ')
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
                            <h3 class="brand-text font-weight-light">تعديل شرط {{ $condition->name ?? '' }}</h3>
                        </div>

                        <form id="forme_rest">
                            <div class="card-body">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>عنوان الشرط</label>
                                                <input type="text" class="form-control"id="title" name="title"
                                                    placeholder="عنوان الشرط ..." value="{{ $condition->title ?? '' }}">
                                            </div>

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="active"
                                                    id="active" @checked($condition->active ?? '')>
                                                <label class="custom-control-label" for="active">فعال</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>تفاصيل الشرط</label>
                                                <textarea class="form-control" rows="3" id="sub_title" name="sub_title" placeholder="تفاصيل الشرط ...">{{ $condition->sub_title ?? '' }}</textarea>
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
            id = {{ $condition->id }}
            formData.append('_method', 'PUT');
            formData.append('title', document.getElementById('title').value);
            formData.append('sub_title', document.getElementById('sub_title').value);
            formData.append('active', document.getElementById('active').checked ? 1 : 0);

            axios.post('/cms/admin/conditions/' + id, formData)
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
                    window.location.href = '/cms/admin/conditions';

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
