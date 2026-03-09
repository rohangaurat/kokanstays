@extends('Template::layouts.frontend')
@section('content')
    <section class="py-80">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="cookie-data">
                        <h4 class="mb-60 text-center">{{ __($pageTitle) }}</h4>

                        @php
                            echo $cookie->data_values->description;
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .cookie-data {
            border: 1px solid hsl(var(--border-color));
            border-radius: 10px;
            padding: 32px;
        }

        .cookie-data h5 {
            margin-bottom: 16px
        }

        .cookie-data p {
            font-size: 16px;
        }

        .cookie-data ul {
            padding-left: 20px;
            margin-top: 12px;
            margin-left: 12px;
        }

        .cookie-data ul li {
            list-style: disc;
            font-size: 16px;
        }

        .cookie-data ul li:not(:last-child) {
            margin-bottom: 12px;
        }
    </style>
@endpush
