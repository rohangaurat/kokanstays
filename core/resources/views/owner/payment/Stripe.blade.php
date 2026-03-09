@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-xl-6 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Stripe Hosted')</h5>
                </div>
                <div class="card-body">
                    <div class="card-wrapper mb-3"></div>
                    <form action="{{ $data->url }}" id="payment-form" method="{{ $data->method }}" role="form">
                        @csrf
                        <input name="track" type="hidden" value="{{ $data->track }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name on Card')</label>
                                    <div class="input-group">
                                        <input autocomplete="off" autofocus class="form-control" name="name" required type="text" value="{{ old('name') }}" />
                                        <span class="input-group-text"><i class="fa fa-font"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Card Number')</label>
                                    <div class="input-group">
                                        <input autocomplete="off" autofocus class="form-control" name="cardNumber" required type="tel" value="{{ old('cardNumber') }}" />
                                        <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Expiration Date')</label>
                                    <input autocomplete="off" class="form-control" name="cardExpiry" required type="tel" value="{{ old('cardExpiry') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('CVC Code')</label>
                                    <input autocomplete="off" class="form-control" name="cardCVC" required type="tel" value="{{ old('cardCVC') }}" />
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary h-45 w-100" type="submit"> @lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/card.js') }}"></script>

    <script>
        (function($) {
            "use strict";
            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="cardNumber"]',
                    expiryInput: 'input[name="cardExpiry"]',
                    cvcInput: 'input[name="cardCVC"]',
                    nameInput: 'input[name="name"]'
                }
            });
        })(jQuery);
    </script>
@endpush
