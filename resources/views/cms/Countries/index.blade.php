@extends('cms.parent')
@section('title', 'عرض جميع المدن')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">جميع المدن الخاصة بالنظام</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered  table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        {{-- <th class="text-center">صورة</th> --}}
                                        <th class="text-center">اسم المدينة</th>
                                        <th class="text-center">حالة المدينة</th>
                                        <th class="text-center" style="width: 190px">الاعدادات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $countries)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{{ $loop->index + 1 }}.
                                            </td>
                                            {{-- <td style="text-align: center; vertical-align: middle;">
                                                <span>
                                                    <img class="round" src="{{ $countries->image_profile }}" alt="avatar"
                                                        height="60" width="60"></span>
                                            </td> --}}
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $countries->name ?? '' }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge {{ $countries->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $countries->active_key }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('countries.edit', [$countries->id]) }}"
                                                        class="btn btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" onclick="comforme('{{ $countries->id }}',this)"
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
            axios.delete('/cms/admin/countries/' + id)
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

@endsection
