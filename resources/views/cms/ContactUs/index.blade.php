@extends('cms.parent')
@section('title', 'عرض تواصل معنا')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">جميع تواصل معنا الخاصة بالنظام</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered  table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th class="text-center">صورة المستخدم</th>
                                        <th class="text-center">اسم المستخدم</th>
                                        <th class="text-center">العنوان</th>
                                        <th class="text-center">التفاصيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $contactUs)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{{ $loop->index + 1 }}.
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <span>
                                                    <img class="round" src="{{ $contactUs->account->image_profile }}"
                                                        alt="avatar" height="60" width="60"></span>
                                            </td>

                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $contactUs->account->name ?? '' }}
                                            </td>

                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $contactUs->title ?? '' }}
                                            </td>


                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $contactUs->details ?? '' }}
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
@endsection
