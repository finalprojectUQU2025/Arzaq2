@extends('cms.parent')
@section('title', 'عرض جميع المزادات')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="brand-text font-weight-light">جميع المزادات الخاصة بالنظام</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered  table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th class="text-center">صورة المزاد</th>
                                        <th class="text-center">الرقم التسلسلي</th>
                                        <th class="text-center">اسم المزاد</th>
                                        <th class="text-center">الكمية</th>
                                        <th class="text-center">سعر البداية</th>
                                        <th class="text-center">حالة المزاد</th>
                                        <th class="text-center">نوع المزاد</th>
                                        <th class="text-center">منشئ المزاد</th>
                                        <th class="text-center">تاريخ انتهاء المزاد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $admins)
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td>{{ $loop->index + 1 }}.
                                            </td>
                                            <td>
                                                <span>
                                                    <img class="round" src="{{ url('storage', $admins->image) }}"
                                                        alt="avatar" height="60" width="60"></span>
                                            </td>
                                            <td>
                                                <a
                                                    @if ($admins->status == 'done') href="{{ route('auctionsDetails.index', $admins->id) }}" @endif>
                                                    {{ $admins->serial_number ?? '' }}
                                                </a>
                                            </td>

                                            <td>
                                                {{ $admins->name ?? '' }}</td>

                                            <td>
                                                {{ $admins->quantity ?? '' }}</td>

                                            <td>
                                                {{ $admins->starting_price ?? '' }}</td>

                                            <td>
                                                {{ $admins->status ?? '' }}
                                            </td>

                                            <td>
                                                {{ $admins->product->name ?? '' }}
                                            </td>

                                            <td>
                                                {{ $admins->account->name ?? '' }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($admins->end_time)->format('d-m-Y g:i A') ?? '' }}
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
