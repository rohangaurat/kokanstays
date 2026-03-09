@extends('owner.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-header-bg">
                    <h5 class="card-title">{{ __($pageTitle) }}</h5>
                </div>
                <div class="card-body  ">
                    <form action="{{ route('owner.deposit.manual.update') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p class="text-center mt-2">@lang('You have requested') <b class="text--success">{{ showAmount($data['amount']) }}</b> , @lang('Please pay')
                                    <b class="text--success">{{ showAmount($data['final_amount'], currencyFormat:false) . ' ' . $data['method_currency'] }} </b> @lang('for successful payment')
                                </p>
                                <h4 class="text-center mb-4">@lang('Please follow the instruction below')</h4>

                                <p class="my-4 text-center">@php echo  $data->gateway->description @endphp</p>

                            </div>

                            <x-viser-form identifierValue="{{ $gateway->form_id }}" identifier="id" />

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn--primary h-45 w-100" type="submit">@lang('Pay Now')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
