@php
    $frontendMissionVisionGoalContent = getContent('mission_vision_goal.content', true);
    $frontendMissionContent = getContent('mission.content', true);
    $frontendMissionElements = getContent('mission.element', orderById: true);
    $frontendVisionContent = getContent('vision.content', true);
    $frontendVisionElements = getContent('vision.element');
    $frontendGoalContent = getContent('goal.content', true);
    $frontendGoalElements = getContent('goal.element');
@endphp
<div class="our-goal-section section-bg">
    <div class="container">
        <div class="row">
            <div class="col-xl-5 col-lg-6">
                <div class="goal-content py-80">
                    <h2 class="goal-content__title">
                        {{ __($frontendMissionVisionGoalContent?->data_values?->heading ?? '') }}
                    </h2>
                    <div class="goal-content__tab">
                        <ul class="nav nav-pills custom--tab tab-two" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-vision-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-vision" type="button" role="tab"
                                        aria-controls="pills-vision" aria-selected="true">
                                    @lang('Our Vision')
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-mission-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-mission" type="button" role="tab"
                                        aria-controls="pills-mission" aria-selected="false">
                                    @lang('Our Mission')
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-goal-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-goal" type="button" role="tab"
                                        aria-controls="pills-goal" aria-selected="false">
                                    @lang('Our Goal')
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-vision" role="tabpanel"
                                 aria-labelledby="pills-vision-tab" tabindex="0">
                                <div class="our-goal">
                                    <p class="our-goal__text">
                                        {{ __($frontendVisionContent?->data_values?->description ?? '') }}
                                    </p>
                                    <ul class="service-list">
                                        @foreach ($frontendVisionElements ?? [] as $frontendVisionElement)
                                            <li class="service-list__item">
                                                <span class="service-list__icon">
                                                    @php echo $frontendVisionContent?->data_values?->icon ?? ''; @endphp
                                                </span>
                                                {{ __($frontendVisionElement->data_values->point ?? '') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-mission" role="tabpanel"
                                 aria-labelledby="pills-mission-tab" tabindex="0">
                                <div class="our-goal">
                                    <p class="our-goal__text">
                                        {{ __($frontendMissionContent?->data_values?->description ?? '') }}
                                    </p>
                                    <ul class="service-list">
                                        @foreach ($frontendMissionElements ?? [] as $frontendMissionElement)
                                            <li class="service-list__item">
                                                <span class="service-list__icon">
                                                    @php echo $frontendMissionContent?->data_values?->icon ?? ''; @endphp
                                                </span>
                                                {{ __($frontendMissionElement->data_values->point ?? '') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-goal" role="tabpanel" aria-labelledby="pills-goal-tab"
                                 tabindex="0">
                                <div class="our-goal">
                                    <p class="our-goal__text">
                                        {{ __($frontendGoalContent?->data_values?->description ?? '') }}
                                    </p>
                                    <ul class="service-list">
                                        @foreach ($frontendGoalElements ?? [] as $frontendGoalElement)
                                            <li class="service-list__item">
                                                <span class="service-list__icon">
                                                    @php echo $frontendGoalContent?->data_values?->icon ?? ''; @endphp
                                                </span>
                                                {{ __($frontendGoalElement->data_values->point ?? '') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7 col-lg-6 ">
                <div class="goal-thumb-wrapper">
                    <div class="our-goal-thumb">
                        <img src="{{ frontendImage('mission_vision_goal', $frontendMissionVisionGoalContent?->data_values?->image ?? null, '955x615') }}"
                             alt="mission vision image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
    <style>
        .our-goal-section .goal-thumb-wrapper::after {
            -webkit-mask-image: url({{ asset(activeTemplate(true) . 'images/shapes/g-1.png') }});
            mask-image: url({{ asset(activeTemplate(true) . 'images/shapes/g-1.png') }});
        }
    </style>
@endpush
