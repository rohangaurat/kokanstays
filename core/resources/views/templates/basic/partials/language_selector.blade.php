@if (gs('multi_language'))
    @php
        $languages = App\Models\Language::where('code', '!=', config('app.locale'))->get();
        $selectedLanguage = App\Models\Language::where('code', config('app.locale'))->first();
    @endphp
    <div class="custom--dropdown">
        <div class="custom--dropdown__selected dropdown-list__item">
            <div class="icon">
                <img src="{{ getImage(getFilePath('language') . '/' . $selectedLanguage->image ?? null, getFileSize('language')) }}"
                     alt="country-flag">
            </div>
            <span class="text">{{ __($selectedLanguage->name) }}</span>
        </div>
        @if (!blank($languages))
            <ul class="dropdown-list">
                @foreach ($languages as $language)
                    <li class="dropdown-list__item ">
                        <a href="{{ route('lang', $language->code) }}" class="thumb">
                            <img src="{{ getImage(getFilePath('language') . '/' . $language->image ?? null, getFileSize('language')) }}"
                                 alt="country-flag">
                            <span class="text">{{ __($language->name) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
