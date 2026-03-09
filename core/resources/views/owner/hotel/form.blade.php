@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-xxl-8 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link @if ($step == 1) active @endif"
                                href="{{ route('owner.hotel.setting.index') . '?step=1' }}">
                                <i class="las la-cogs"></i>
                                @lang('Basic')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($step == 2) active @endif" @disabled($setting->complete_step < 2)
                                href="{{ $setting->complete_step < 2 ? '#' : route('owner.hotel.setting.index') . '?step=2' }}">
                                <i class="las la-photo-video"></i>
                                @lang('Images')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($step == 3) active @endif" @disabled($setting->complete_step < 3)
                                href="{{ $setting->complete_step < 3 ? '#' : route('owner.hotel.setting.index') . '?step=3' }}">
                                <i class="las la-coffee"></i>
                                @lang('Facility')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($step == 4) active @endif" @disabled($setting->complete_step < 4)
                                href="{{ $setting->complete_step < 4 ? '#' : route('owner.hotel.setting.index') . '?step=4' }}">
                                <i class="las la-check-square"></i>
                                @lang('Policy')
                            </a>
                        </li>
                    </ul>
                    <form action="{{ route('owner.hotel.setting.update', $setting->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="step" value="{{ $step }}">
                        @include("owner.hotel.step.$step")
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            color: #212529c9;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .nav-tabs .nav-link {
            color: #212529c9;
        }

        @media(max-width: 380px) {
            .nav-link {
                padding: 8px 7px;
                font-size: 12px;
            }
        }

        .image--uploader .mt-2 {
            text-align: center !important;
        }

        .image-uploader .upload-text span {
            font-size: 14px !important;
            color: #6c757d !important;
        }

        .image-uploader .upload-text i {
            margin-bottom: 0px !important;
        }
    </style>
@endpush
