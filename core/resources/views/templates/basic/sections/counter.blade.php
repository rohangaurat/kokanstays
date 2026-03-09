@php $frontendCounterElements = getContent('counter.element'); @endphp
@if (!blank($frontendCounterElements))
    <div class="counter-up-section py-80">
        <div class="container">
            <div class="row">
                <div class="counterup-item ">
                    @foreach ($frontendCounterElements ?? [] as $frontendCounterElement)
                        <div class="counterup-item__content">
                            <div class="d-flex align-items-center counterup-wrapper">
                                <span class="counterup-item__icon">
                                    @php echo $frontendCounterElement->data_values->icon ?? ''; @endphp
                                </span>
                                <div class="content">
                                    <div class="counterup-item__number">
                                        <h3 class="counterup-item__title mb-0">
                                            <span class="odometer"
                                                data-odometer-final="{{ $frontendCounterElement->data_values->value ?? 0 }}">0</span>{{ $frontendCounterElement->data_values->quantity ?? '' }}
                                        </h3>
                                    </div>
                                    <span class="counterup-item__text mb-0">
                                        {{ $frontendCounterElement->data_values->title ?? '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

@push('script')
    <script>
        (function($) {
            "use strict";

            $(".counterup-item").each(function() {
                $(this).isInViewport(function(status) {
                    if (status === "entered") {
                        let length = document.querySelectorAll(".odometer").length;
                        for (var i = 0; i < length; i++) {
                            var el = document.querySelectorAll(".odometer")[i];
                            el.innerHTML = el.getAttribute("data-odometer-final");
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
