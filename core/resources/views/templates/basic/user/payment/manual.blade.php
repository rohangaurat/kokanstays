@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="profile-wrapper">
                <h5 class="profile-wrapper__title mb-3">{{ __($pageTitle) }}</h5>
                <form action="{{ route('user.deposit.manual.update') }}" method="POST" class="disableSubmission"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert--base">
                                <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting')
                                    <b>{{ showAmount($data['amount']) }}</b> @lang('to deposit.') @lang('Please pay')
                                    <b>{{ showAmount($data['final_amount'], currencyFormat: false) . ' ' . $data['method_currency'] }}
                                    </b> @lang('for successful payment.')
                                </p>
                            </div>
                            <div class="mb-3">@php echo  $data->gateway->description @endphp</div>
                        </div>
                        <div class="col-12">
                            @php
                                $labelClass = '--label';
                                $formCheckClass = 'form--check';
                                $formRadioClass = 'form--radio';
                            @endphp
                            <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}"
                                labelClass="{{ $labelClass }}" formCheckClass="{{ $formCheckClass }}"
                                formRadioClass="{{ $formRadioClass }}" />
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--base btn--lg w-100">@lang('Pay Now')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
