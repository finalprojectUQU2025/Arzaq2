@extends('cms.parent')
@section('title', 'لوحة التحكم')
@section('main-content')
    <section class="content">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $allAlmazarie->count() }}</h3>

                        <p>عدد المزارعين</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('accounts.MazarieIndex') }}" class="small-box-footer">عرض المزيد <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $allTajir->count() }}<sup style="font-size: 20px"></sup></h3>

                        <p>عدد التجار</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('accounts.TajirIndex') }}" class="small-box-footer">عرض المزيد <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $allAdmin->count() }}</h3>

                        <p>عدد المشرفين</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ route('admins.index') }}" class="small-box-footer">عرض المزيد <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $allAuction->count() }}</h3>

                        <p>جميع المزادات</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('auctions.index') }}" class="small-box-footer">عرض المزيد <i
                        class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="brand-text font-weight-light">عرض التواصل معنا</h3>
            </div>

            <div class="card-body">
                <table class="table table-bordered  table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th class="text-center">صورة</th>
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
                                        <img class="round" src="{{ $contactUs->account->image_profile }}" alt="avatar"
                                            height="60" width="60"></span>
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
    </section>

@endsection
