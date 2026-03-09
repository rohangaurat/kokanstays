@extends('owner.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/owner/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">
                                    @lang('Welcome to') <strong>{{ __(gs()->site_name) }}</strong>
                                </h3>
                                <p class="text-white">{{ __($pageTitle) }}</p>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('owner.send.form.data', session('OWNER_ID') ?? 0) }}" method="POST"
                                    class="cmn-form mt-30 login-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <x-viser-form identifier="act" identifierValue="owner_form" />
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn cmn-btn mt-2 w-100">@lang('Send Request')</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="text-end text-white mt-3">
                                    <a href="{{ route('owner.login') }}" class="text-white mt-4">
                                        <i class="las la-sign-in-alt" aria-hidden="true"></i>
                                        @lang('Login your account')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            "use strict";

            $.each($('.select2'), function() {
                $(this)
                    .select2({
                        dropdownParent: $(this).parent('.form-group');
                    });
            });
        });
    </script>
@endpush

@push('style')
    <style>
        input {
            color-scheme: dark;
        }

        #license_picture {
            padding: 0
        }

        #license_picture::-webkit-file-upload-button {
            margin-top: 5px;
            margin-left: 5px;
            height: calc(100% - 10px);
            border-radius: 3px;
        }

        .form-group pre {
            color: #fff;
        }

        .form-control {
            background-color: transparent !important;
        }

        .select2-container--default .select2-selection--single {
            background: #1e157d;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
            font-size: 0.875rem;
        }

        .select2-dropdown {
            background: #1e1297;
            color: #fff;
        }

        .select2-container--default .select2-results__option--disabled,
        .select2-results__options li {
            color: #cfcece;
        }

        .select2-results__option--selectable {
            color: #eee
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        .select2-container--open .select2-selection {
            border: 1px solid #3D2BFB !important;
            box-shadow: 0 3px 9px rgba(50, 50, 9, 0.05), 3px 4px 8px rgba(115, 103, 240, 0.1);
        }

        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            line-height: 50px;
        }

        .select2-search__field {
            color: #fff;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 5px;
        }

        .input-group-text {
            background-color: #3d2bfb;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: #fff;
        }

        .login-form .input-group-text+.form-control {
            border-left: none;
        }

        .select2-results__option.select2-results__option--selected {
            background-color: #3d2bfb;
        }

        .select2-dropdown {
            margin-left: 1px;
        }

        .select2-container {
            z-index: var(--index) !important;
        }
    </style>
@endpush
