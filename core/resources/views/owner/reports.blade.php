@extends('owner.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ @$report->req_type }}</td>
                                        <td class="white-space-wrap text-center">{{ @$report->message }}</td>
                                        <td>
                                            <span class="badge badge--{{ @$report->status_class }}">{{ @$report->status_text }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('owner.request.report.submit')
        <div aria-hidden="true" aria-labelledby="bugModalLabel" class="modal fade" id="bugModal" role="dialog" tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bugModalLabel">@lang('Report & Request')</h5>
                        <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('owner.request.report.submit') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>@lang('Type')</label>
                                <select class="form-control" name="type" required>
                                    <option @selected(old('type') == 'bug') value="bug">@lang('Report Bug')</option>
                                    <option @selected(old('type') == 'feature') value="feature">@lang('Feature Request')</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('Message')</label>
                                <textarea class="form-control" name="message" required rows="5">{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection
@push('breadcrumb-plugins')
    @can('owner.request.report.submit')
        <button class="btn btn-sm btn-outline--primary" data-bs-target="#bugModal" data-bs-toggle="modal"><i class="las la-bug"></i> @lang('Report a bug')</button>
    @endcan
    <a class="btn btn-sm btn-outline--primary" href="https://viserlab.com/support" target="_blank"><i class="las la-headset"></i> @lang('Request for Support')</a>
@endpush
