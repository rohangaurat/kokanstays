@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="profile-wrapper">
                <h5 class="profile-wrapper__title mb-3">@lang('Razorpay')</h5>
                <ul class="list-group text-center">
                    <li class="list-group-item d-flex justify-content-between">
                        @lang('You have to pay '):
                        <strong>
                            {{ showAmount($deposit->final_amount, currencyFormat: false) }}
                            {{ __($deposit->method_currency) }}
                        </strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        @lang('You will get '):
                        <strong>{{ showAmount($deposit->amount) }}</strong>
                    </li>
                </ul>
                <form action="{{ $data->url }}" method="{{ $data->method }}">
                    <input type="hidden" custom="{{ $data->custom }}" name="hidden">
                    <script src="{{ $data->checkout_js }}"
                        @foreach ($data->val as $key => $value)
                    data-{{ $key }}="{{ $value }}" @endforeach>
                    </script>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('input[type="submit"]').addClass("mt-4 btn btn--base btn--lg w-100");
        })(jQuery);
    </script>
@endpush
