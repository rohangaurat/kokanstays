@extends('Template::layouts.master')
@section('content')
    <div class="profile-wrapper">
        <h5 class="profile-wrapper__title">@lang('Change Password')</h5>
        <p class="profile-wrapper__text mb-4">
            @lang('Update your password regularly to keep your account secure and your travel details protected.')
        </p>
        <form method="POST" action="{{ route('user.change.password.submit') }}" class="profile-form">
            @csrf
            <div class="row">
                <div class="col-12 form-group">
                    <label class="form--label">@lang('Current Password')</label>
                    <div class="position-relative">
                        <input type="password" class="form-control form--control" name="current_password" required
                               autocomplete="current-password">
                        <span class="password-show-hide fa-solid fa-eye-slash toggle-password" id="#password"></span>
                    </div>
                </div>
                <div class="col-12 form-group">
                    <label class="form--label">@lang('Password')</label>
                    <div class="position-relative">
                        <input type="password"
                               class="form-control form--control @if (gs('secure_password')) secure-password @endif"
                               name="password" required autocomplete="current-password">
                        <span class="password-show-hide fa-solid fa-eye-slash toggle-password" id="#password"></span>
                    </div>
                </div>
                <div class="col-12 form-group">
                    <label class="form--label">@lang('Confirm Password')</label>
                    <div class="position-relative">
                        <input type="password" class="form-control form--control" name="password_confirmation" required
                               autocomplete="current-password">
                        <span class="password-show-hide fa-solid toggle-password fa-eye-slash"
                              id="#password_confirmation"></span>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button class="btn btn--base btn--lg w-100">@lang('Save Changes')</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
