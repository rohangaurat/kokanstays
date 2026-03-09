@extends('owner.layouts.app')
@section('panel')
    <div class="row ">
        <div class="col-xl-3 col-lg-4 mb-30">
            @include('owner.partials.info')
        </div>

        <div class="col-xl-9 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Profile Information')</h5>

                    <form action="{{ route('owner.profile.update') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xl-6 col-lg-12 col-md-6">
                                <div class="form-group">
                                    <x-image-uploader class="w-100" type="ownerProfile" :imagePath="getImage(getFilePath('ownerProfile') . '/' . $owner->image, getFileSize('ownerProfile'))" :required="false" />
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-12 col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" name="firstname" required type="text" value="{{ $owner->firstname }}">
                                </div>

                                <div class="form-group">
                                    <label>@lang('Last Name')</label>
                                    <input class="form-control" name="lastname" required type="text" value="{{ $owner->lastname }}">
                                </div>

                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" name="email" required type="email" value="{{ $owner->email }}">
                                </div>
                            </div>
                        </div>

                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn-outline--primary" href="{{ route('owner.password') }}"><i class="las la-key"></i>@lang('Password Setting')</a>
@endpush
