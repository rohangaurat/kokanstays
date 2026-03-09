@extends('owner.layouts.app')
@section('panel')
    <form action="{{ route('owner.roles.save', @$role->id) }}" method="post">
        @csrf
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">@lang('Name')</label>
                            <input class="form-control" name="name" type="text" value="{{ old('name', @$role->name) }}">
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Set Permissions')</h5>
                    </div>
                    <div class="card-body">
                        <div class="">

                            <div class="row gy-4">
                                @foreach ($permissionGroups as $key => $permissionGroup)
                                    <div class="col-12">
                                        <div class="permission-item">
                                            <div class="row gy-2 justify-content-center align-items-center">
                                                <div class="col-sm-3">
                                                    <span>{{ Str::replaceLast('Controller', '', $key) }}</span>
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class="d-flex flex-wrap gap-3">
                                                        @foreach ($permissionGroup as $permission)
                                                            <div class="custom-control custom-checkbox form-check-primary">
                                                                <input class="custom-control-input" id="customCheck{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->id }}">
                                                                <label class="custom-control-label" for="customCheck{{ $permission->id }}">{{ $permission->name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @can('owner.roles.save')
                <div class="col-lg-12">
                    <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                </div>
            @endcan
        </div>
    </form>
@endsection

@push('style')
    <style>
        .permission-item {
            background: #fafafa;
            border: 1px solid #f7f7f7;
            padding: 1rem;
        }
    </style>
@endpush

@push('script')
    @push('script')
        <script>
            (function($) {
                "use strict";
                @isset($permissions)
                    $('input[name="permissions[]"]').val(@json($permissions));
                @endif
            })(jQuery);
        </script>
    @endpush
@endpush
