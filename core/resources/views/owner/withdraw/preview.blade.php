@extends('owner.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Withdraw Via') {{ $withdraw->method->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.withdraw.submit') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="mb-2">
                            @php
                                echo $withdraw->method->description;
                            @endphp
                        </div>
                        
                        <x-viser-form identifierValue="{{ $withdraw->method->form_id }}" identifier="id" />

                        @if (authOwner()->ts)
                            <div class="form-group">
                                <label>@lang('Google Authenticator Code')</label>
                                <input class="form-control" name="authenticator_code" required type="text">
                            </div>
                        @endif
                        <div class="form-group">
                            <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
