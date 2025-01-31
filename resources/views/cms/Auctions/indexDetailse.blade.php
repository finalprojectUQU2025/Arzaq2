@extends('cms.parent')
@section('title', 'عرض تفاصيل مزاد' . ' ' . $auction->name ?? '')
@section('main-content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">عدد العروض القدمة لهذا المزاد</span>
                                    <span class="info-box-number text-center text-muted mb-0">{{ $offers->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">اعلى سعر وصل اليه المزاد</span>
                                    <span
                                        class="info-box-number text-center text-muted mb-0">{{ $offer_amount->offer_amount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h4>تفاصيل المزاد</h4>
                            <hr>

                            @foreach ($offers as $offer)
                                <div class="post">
                                    <div class="user-block">
                                        <img class="img-circle img-bordered-sm" src="{{ $offer->account->image_profile }}"
                                            alt="user image">
                                        <span class="username">
                                            {{ $offer->account->name ?? '' }}.
                                        </span>
                                        <span class="description">
                                            {{ \Carbon\Carbon::parse($offer->create_at)->format('g:i A') }}
                                            اليوم</span>
                                    </div>
                                    <!-- /.user-block -->
                                    {{-- <p>
                                        Lorem ipsum represents a long-held tradition for designers,
                                        typographers and the like. Some people hate it and argue for
                                        its demise, but others ignore.
                                    </p> --}}

                                    <p>
                                        <a class="link-black text-sm"><i class="fas fa-link mr-1"></i>
                                            {{ $offer->offer_amount ?? '' }} ريال سعودي</a>
                                    </p>
                                </div>
                            @endforeach




                            {{-- <div class="post clearfix">
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm" src="../../dist/img/user7-128x128.jpg"
                                        alt="User Image">
                                    <span class="username">
                                        <a href="#">Sarah Ross</a>
                                    </span>
                                    <span class="description">Sent you a message - 3 days ago</span>
                                </div>
                                <!-- /.user-block -->
                                <p>
                                    Lorem ipsum represents a long-held tradition for designers,
                                    typographers and the like. Some people hate it and argue for
                                    its demise, but others ignore.
                                </p>
                                <p>
                                    <a href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> Demo File
                                        2</a>
                                </p>
                            </div>

                            <div class="post">
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm" src="../../dist/img/user1-128x128.jpg"
                                        alt="user image">
                                    <span class="username">
                                        <a href="#">Jonathan Burke Jr.</a>
                                    </span>
                                    <span class="description">Shared publicly - 5 days ago</span>
                                </div>
                                <!-- /.user-block -->
                                <p>
                                    Lorem ipsum represents a long-held tradition for designers,
                                    typographers and the like. Some people hate it and argue for
                                    its demise, but others ignore.
                                </p>

                                <p>
                                    <a href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> Demo File
                                        1 v1</a>
                                </p>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                    <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{ $auction->name ?? '' }}</h3>
                    <br>
                    <div class="text-muted">
                        <p class="text-sm">مقدم المزاد
                            <b class="d-block">{{ $auction->account->name ?? '' }}</b>
                        </p>
                        <p class="text-sm">فائز المزاد
                            <b class="d-block">{{ $offer_amount->account->name ?? '' }}</b>
                        </p>
                    </div>

                    {{-- <h5 class="mt-5 text-muted">Project files</h5>
                    <ul class="list-unstyled">
                        <li>
                            <a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i>
                                Functional-requirements.docx</a>
                        </li>
                        <li>
                            <a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-pdf"></i>
                                UAT.pdf</a>
                        </li>
                        <li>
                            <a href="" class="btn-link text-secondary"><i class="far fa-fw fa-envelope"></i>
                                Email-from-flatbal.mln</a>
                        </li>
                        <li>
                            <a href="" class="btn-link text-secondary"><i class="far fa-fw fa-image "></i>
                                Logo.png</a>
                        </li>
                        <li>
                            <a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i>
                                Contract-10_12_2014.docx</a>
                        </li>
                    </ul>
                    <div class="text-center mt-5 mb-3">
                        <a href="#" class="btn btn-sm btn-primary">Add files</a>
                        <a href="#" class="btn btn-sm btn-warning">Report contact</a> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
    </div>
@endsection
@section('script')
    <script src="{{ asset('cms/dist/js/adminlte.min.js') }}"></script>


@endsection
