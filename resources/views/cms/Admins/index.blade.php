@extends('cms.parent')
@section('title', 'عرض جميع المشرفين')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">جميع المشرفين الخاصة بالنظام</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered  table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th class="text-center">صورة</th>
                                        <th class="text-center">اسم المشرف</th>
                                        <th class="text-center">رقم الهوية</th>
                                        <th class="text-center">اسم المدينة</th>
                                        <th class="text-center">رقم الهاتف</th>
                                        <th class="text-center">حالة الحساب</th>
                                        <th class="text-center" style="width: 190px">الاعدادات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $admins)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{{ $loop->index + 1 }}.
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <span>
                                                    <img class="round" src="{{ $admins->image_profile }}" alt="avatar"
                                                        height="60" width="60"></span>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $admins->name ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $admins->id_number ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $admins->country->name ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $admins->phone ?? '' }}</td>

                                            <td class="text-center align-middle">
                                                <span class="badge {{ $admins->blocked ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $admins->blocked_sta }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group">

                                                    @if ($admins->blocked)
                                                        <a href="#"
                                                            onclick="performUnblocked('{{ $admins->id }}',this)"
                                                            class="btn btn-danger">
                                                            <i class="fas fa-lock"></i>
                                                        </a>
                                                    @else
                                                        <a href="#"
                                                            onclick="performblocked('{{ $admins->id }}',this)"
                                                            class="btn btn-success">
                                                            <i class="fas fa-lock-open"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('admins.edit', [$admins->id]) }}"
                                                        class="btn btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="#" onclick="comforme('{{ $admins->id }}',this)"
                                                        class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('script')
    <script src="{{ asset('cms/dist/js/adminlte.min.js') }}"></script>

    <script>
        function comforme(id, element) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete(id, element);
                }
            });
        }


        function performDelete(id, element) {
            axios.delete('/cms/admin/admins/' + id)
                .then(function(response) {
                    Swal.fire({
                        title: 'تم الحذف!',
                        text: response.data.message,
                        icon: 'success',
                        allowOutsideClick: false,
                        confirmButtonText: 'حسنًا',

                    });
                    element.closest('tr').remove();
                })
                .catch(function(error) {
                    Swal.fire({
                        title: 'حدث خطأ!',
                        text: error.response.data.message,
                        icon: 'error',
                        allowOutsideClick: false,
                        confirmButtonText: 'حسنًا'
                    });

                });
        }
    </script>

    <script>
        function performblocked(id) {
            Swal.fire({
                title: "هل أنت متأكد من حظر المسؤول؟?",
                text: "أنت على بعد خطوة واحدة من حظر المسؤول",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "حظر",
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
            }).then(function(result) {
                if (result.value) {
                    axios.post('/cms/admin/blockedAdmin/' + id)
                        .then(function(response) {
                            Swal.fire({
                                title: 'تم الحظر!',
                                text: response.data.message,
                                icon: 'success',
                                allowOutsideClick: false,
                                confirmButtonText: 'حسنًا',

                            });
                            window.location.href = '/cms/admin/admins';
                        })
                        .catch(function(error) {
                            Swal.fire({
                                title: 'حدث خطأ!',
                                text: error.response.data.message,
                                icon: 'error',
                                allowOutsideClick: false,
                                confirmButtonText: 'حسنًا'
                            });
                        });
                } else if (result.dismiss === "cancel") {

                }
            });
        }

        function performUnblocked(id) {
            Swal.fire({
                title: "هل أنت متأكد من إلغاء الحظر؟",
                text: "أنت على بعد خطوة واحدة من إلغاء حظر المسؤول",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: " إلغاء الحظر",
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
            }).then(function(result) {
                if (result.value) {
                    axios.post('/cms/admin/blockedAdmin/' + id)
                        .then(function(response) {
                            Swal.fire({
                                title: 'تم الحظر!',
                                text: response.data.message,
                                icon: 'success',
                                allowOutsideClick: false,
                                confirmButtonText: 'حسنًا',
                            });
                            window.location.href = '/cms/admin/admins';
                        })
                    Swal.fire({
                        title: 'حدث خطأ!',
                        text: error.response.data.message,
                        icon: 'error',
                        allowOutsideClick: false,
                        confirmButtonText: 'حسنًا'
                    });
                } else if (result.dismiss === "cancel") {

                }
            });
        }
    </script>
@endsection
