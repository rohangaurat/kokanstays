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
                                    <th>@lang('Title')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Rating')</th>
                                    @can('owner.review.details')
                                        <th>@lang('Action')</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews ?? [] as $review)
                                    <tr>
                                        <td>{{ __($review->title) }}</td>
                                        <td>{{ $review->user->fullname }}</td>
                                        <td class="text-center"><x-rating-star :rating="$review->rating" /></td>
                                        @can('owner.review.details')
                                            <td>
                                                <div class="button--group">
                                                    <a href="{{ route('owner.review.details', $review->id) }}"
                                                        class="btn btn-sm btn-outline--primary">
                                                        <i class="las la-desktop"></i>@lang('Details')
                                                    </a>
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .rating-list {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            justify-content: center;
        }

        .rating-list__item {
            padding: 0 2px;
            color: #ff9f43;
            font-size: 14px;
            line-height: 1;
        }
    </style>
@endpush
