@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('NMI')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $data->url }}" id="payment-form" method="{{ $data->method }}" role="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Card Number')</label>
                                    <div class="input-group">
                                        <input autocomplete="off" autofocus class="form-control" name="billing-cc-number" required type="tel" value="{{ old('billing-cc-number') }}" />
                                        <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Expiration Date')</label>
                                    <input autocomplete="off" class="form-control" name="billing-cc-exp" placeholder="e.g. MM/YY" required type="tel" value="{{ old('billing-cc-exp') }}" />
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label>@lang('CVC Code')</label>
                                    <input autocomplete="off" class="form-control" name="billing-cc-cvv" required type="tel" value="{{ old('billing-cc-cvv') }}" />
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
