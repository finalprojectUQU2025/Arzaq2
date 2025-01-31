@extends('cms.parent')
@section('title', 'عرض الإشعارات')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">جميع الإشعارات</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th class="text-center">اسم المستخدم</th>
                                        <th class="text-center">العنوان</th>
                                        <th class="text-center">التفاصيل</th>
                                        <th class="text-center">تاريخ الإنشاء</th>
                                        <th class="text-center">هل تم قراءتها؟</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifications as $notification)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{{ $loop->index + 1 }}.
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $notification->account->name ?? 'لكل المستخدمين' }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $notification->title ?? '' }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $notification->details ?? '' }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {{ $notification->created_at ? $notification->created_at->format('h:i A - d/m/Y') : '' }}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if ($notification->is_for_all)
                                                    <span>أرسل إلى جميع المستخدمين</span>
                                                @else
                                                    {{ $notification->is_read ? 'تمت القراءة' : 'غير مقروءة' }}
                                                @endif
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
