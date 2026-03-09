@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="profile-wrapper">
                <h5 class="profile-wrapper__title mb-3">@lang('Payment Preview')</h5>
                <div class="card-body-deposit text-center">
                    <h4 class="my-2">
                        @lang('PLEASE SEND EXACTLY') <span class="text--success"> {{ $data->amount }}</span>
                        {{ __($data->currency) }}
                    </h4>
                    <h5 class="mb-2">@lang('TO') <span class="text--success"> {{ $data->sendto }}</span></h5>
                    <img src="{{ $data->img }}" alt="Image">
                    <h4 class="text-white bold my-4">@lang('SCAN TO SEND')</h4>
                </div>
            </div>
        </div>
    </div>
@endsection
