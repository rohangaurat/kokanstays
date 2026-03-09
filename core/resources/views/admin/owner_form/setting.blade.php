@extends('admin.layouts.app')
@section('panel')
    <div class="row ">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg--primary d-flex justify-content-between">
                    <h5 class="text-white">@lang('Form Fields')</h5>
                    <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        @csrf
                        <x-generated-form :form=$form />
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-form-generator-modal />
@endsection
