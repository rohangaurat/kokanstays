@extends('Template::layouts.master')
@section('content')
    <div class="profile-wrapper">
        <div class="profile-wrapper__top">
            <h5 class="profile-wrapper__title">{{ __($pageTitle) }}</h5>
            <p class="profile-wrapper__text">
                @lang('Keep your profile current by updating your name and address. Accurate details help us ensure a seamless booking experience and better communication for all your future stays.')
            </p>
        </div>
        <div class="profile-wrapper__inner">
            <div class="profile-wrapper__left">
                <div class="my-profile">
                    <div class="my-profile__top">
                        <form action="{{ route('user.profile.image.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="file-upload">
                                <label class="edit" for="profile-image" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="@lang('Image size:') {{ getFileSize('userProfile') }}">
                                    <i class="las la-camera"></i>
                                </label>
                                <input type="file" name="image" class="form-control form--control" id="profile-image"
                                    hidden>
                            </div>
                        </form>
                        <div class="thumb">
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, isAvatar: true) }}"
                                alt="profile image">
                        </div>
                    </div>
                    <div class="my-profile__content">
                        <h6 class="my-profile__name">{{ __($user->fullname) }}</h6>
                        <p class="my-profile__mail">{{ $user->email }}</p>
                        <div class="profile-status">
                            <p class="my-profile__info">
                                <span class="name"><i class="las la-user-tie"></i></span>
                                {{ __($user->username) }}
                            </p>
                            <p class="my-profile__info">
                                <span class="name"><i class="las la-phone"></i></span>
                                {{ __($user->mobileNumber) }}
                            </p>
                            <p class="my-profile__info">
                                <span class="name"><i class="las la-globe-americas"></i></span>
                                {{ __($user->country_name) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile-wrapper__right">
                <form method="POST" action="{{ route('user.profile.setting.submit') }}" class="profile-form">
                    @csrf
                    <div class="row">
                        <div class="col-xl-6 col-lg-12 col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('First Name')</label>
                                <input type="text" class="form--control" name="firstname"
                                    value="{{ $user->firstname ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Last Name')</label>
                                <input type="text" class="form--control" name="lastname"
                                    value="{{ $user->lastname ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Address')</label>
                                <input type="text" class="form--control" name="address"
                                    value="{{ $user->address ?? '' }}">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('State')</label>
                                <input type="text" class="form--control" name="state"
                                    value="{{ $user->state ?? '' }}">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Zip Code')</label>
                                <input type="text" class="form--control" name="zip" value="{{ $user->zip ?? '' }}">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-sm-6">
                            <div class="form-group">
                                <label class="form--label">@lang('City')</label>
                                <input type="text" class="form--control" name="city" value="{{ $user->city ?? '' }}">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button class="btn btn--base btn--lg w-100">@lang('Save Changes')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .my-profile__info {
            grid-template-columns: 0fr 1fr;
            font-size: 16px;
        }
    </style>
@endpush
