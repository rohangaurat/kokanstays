<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ gs()->siteName($pageTitle ?? '') }}</title>

    <link href="{{ siteFavicon() }}" rel="shortcut icon" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/owner/css/vendor/bootstrap-toggle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">

    @stack('style-lib')

    <link href="{{ asset('assets/owner/css/vendor/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/owner/css/vendor/jquery-jvectormap-2.0.5.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/owner/css/app.css') }}?v=1" rel="stylesheet">
    <link href="{{ asset('assets/owner/css/custom.css') }}" rel="stylesheet">

    @stack('style')
</head>

<body>
    @yield('content')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/owner/js/vendor/bootstrap-toggle.min.js') }}"></script>
    <script src="{{ asset('assets/owner/js/vendor/jquery.slimscroll.min.js') }}"></script>

    @include('partials.notify')

    @stack('script-lib')

    <script src="{{ asset('assets/owner/js/nicEdit.js') }}"></script>

    <script src="{{ asset('assets/owner/js/vendor/select2.min.js') }}"></script>
    <script src="{{ asset('assets/owner/js/app.js') }}"></script>
    <script src="{{ asset('assets/owner/js/cu-modal.js') }}"></script>
    <script src="{{ asset('assets/owner/js/room-selection.js') }}"></script>

    {{-- LOAD NIC EDIT --}}
    <script>
        "use strict";

        var curText = "{{ __(gs()->cur_text) }}";
        var needChanged = false;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        bkLib.onDomLoaded(function() {
            $(".nicEdit").each(function(index) {
                $(this).attr("id", "nicEditor" + index);
                new nicEditor({
                    fullPanel: true
                }).panelInstance('nicEditor' + index, {
                    hasPanel: true
                });
            });
        });

        (function($) {
            $(document).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain', function() {
                $('.nicEdit-main').focus();
            });
        })(jQuery);
    </script>

    @stack('script')
</body>

</html>
