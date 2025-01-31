@extends('cms.parent')
@section('title', 'عرض جميع التجار')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">جميع التجار الخاصة بالنظام</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered  table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th class="text-center">صورة</th>
                                        <th class="text-center">اسم المستخدم</th>
                                        <th class="text-center">رقم الهاتف</th>
                                        <th class="text-center">نوع المستخدم</th>
                                        <th class="text-center">البريد الالكتروني</th>
                                        <th class="text-center">حالة الحساب</th>
                                        <th class="text-center" style="width: 190px">الاعدادات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $accounts)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{{ $loop->index + 1 }}.
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <span>
                                                    <img class="round" src="{{ $accounts->image_profile }}" alt="avatar"
                                                        height="60" width="60"></span>
                                            </td>

                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $accounts->name ?? '' }}</td>

                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $accounts->phone ?? '' }}</td>

                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $accounts->typeKey ?? '' }}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $accounts->email ?? '' }}</td>

                                            <td class="text-center align-middle">
                                                <span
                                                    class="badge
                                                        @if ($accounts->status === 'verefy') bg-success
                                                        @elseif($accounts->status === 'unVerefy') bg-warning
                                                        @elseif($accounts->status === 'blocked') bg-danger
                                                        @else bg-secondary @endif">
                                                    {{ $accounts->blocked_sta }}
                                                </span>
                                            </td>


                                            <td class="text-center">
                                                <div class="btn-group">

                                                    @if ($accounts->status == 'blocked')
                                                        <a href="#"
                                                            onclick="performUnblocked('{{ $accounts->id }}',this)"
                                                            class="btn btn-danger">
                                                            <i class="fas fa-lock"></i>
                                                        </a>
                                                    @else
                                                        <a href="#"
                                                            onclick="performblocked('{{ $accounts->id }}',this)"
                                                            class="btn btn-success">
                                                            <i class="fas fa-lock-open"></i>
                                                        </a>
                                                    @endif

                                                    <a href="#" onclick="comforme('{{ $accounts->id }}',this)"
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
            axios.delete('/cms/admin/accounts/' + id)
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
                title: "هل أنت متأكد من حظر المستخدم؟?",
                text: "أنت على بعد خطوة واحدة من حظر المستخدم",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "حظر",
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
            }).then(function(result) {
                if (result.value) {
                    axios.post('/cms/admin/blockedAccounts/' + id)
                        .then(function(response) {
                            window.location.href = '/cms/admin/accounts';
                        })
                } else if (result.dismiss === "cancel") {

                }
            });
        }

        function performUnblocked(id) {
            Swal.fire({
                title: "هل أنت متأكد من إلغاء الحظر؟",
                text: "أنت على بعد خطوة واحدة من إلغاء حظر المستخدم",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: " إلغاء الحظر",
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
            }).then(function(result) {
                if (result.value) {
                    axios.post('/cms/admin/blockedAccounts/' + id)
                        .then(function(response) {
                            window.location.href = '/cms/admin/accounts';
                        })
                } else if (result.dismiss === "cancel") {

                }
            });
        }
    </script>
@endsection
