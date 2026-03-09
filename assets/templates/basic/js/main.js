(function ($) {
    ("use strict");

    // ============== Header Hide Click On Body Js Start ========
    $(".header-button").on("click", function () {
        $(".body-overlay").toggleClass("show");
    });
    $(".body-overlay").on("click", function () {
        $(".header-button").trigger("click");
        $(this).removeClass("show");
    });
    // =============== Header Hide Click On Body Js End =========

    // tooltip js 
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    // tooltip js end

    // ==========================================
    //      Start Document Ready function
    // ==========================================
    $(document).ready(function () {
        // ========================== Header Hide Scroll Bar Js Start =====================
        $(".navbar-toggler.header-button").on("click", function () {
            $("body").toggleClass("scroll-hide-sm");
        });
        $(".body-overlay").on("click", function () {
            $("body").removeClass("scroll-hide-sm");
        });
        // ========================== Header Hide Scroll Bar Js End =====================

        // ========================== Small Device Header Menu On Click Dropdown menu collapse Stop Js Start =====================
        $(".dropdown-item").on("click", function () {
            $(this).closest(".dropdown-menu").addClass("d-block");
        });

        // ========================== Small Device Header Menu On Click Dropdown menu collapse Stop Js End =====================

        // ========================== Add Attribute For Bg Image Js Start =====================
        $(".bg-img").css("background", function () {
            var bg = "url(" + $(this).data("background-image") + ")";
            return bg;
        });
        // ========================== Add Attribute For Bg Image Js End =====================

        // qty js start here 
        var buttonPlus = $(".qty-btn-plus");
        var buttonMinus = $(".qty-btn-minus")
        var incrementPlus = buttonPlus.click(function () {
            var $n = $(this)
                .parent(".qty-container")
                .find(".input-qty");
            $n.val(Number($n.val()) + 1);
        });
        var incrementMinus = buttonMinus.click(function () {
            var $n = $(this)
                .parent(".qty-container")
                .find(".input-qty");
            var amount = Number($n.val());
            if (amount > 0) {
                $n.val(amount - 1);
            }
        });

        /*==================== custom dropdown select js ====================*/
        $('.custom--dropdown > .custom--dropdown__selected').on('click', function () {
            $(this).parent().toggleClass('open');
        });

        $('.custom--dropdown > .dropdown-list > .dropdown-list__item').on('click', function () {
            $('.custom--dropdown > .dropdown-list > .dropdown-list__item').removeClass('selected');
            $(this).addClass('selected').parent().parent().removeClass('open').children('.custom--dropdown__selected').html($(this).html());
        });

        $(document).on('keyup', function (evt) {
            if ((evt.keyCode || evt.which) === 27) {
                $('.custom--dropdown').removeClass('open');
            }
        });

        $(document).on('click', function (evt) {
            if ($(evt.target).closest(".custom--dropdown > .custom--dropdown__selected").length === 0) {
                $('.custom--dropdown').removeClass('open');
            }
        });
        /*=============== custom dropdown select js end =================*/

        // delete btn js start 
        $('.delete-btn').on('click', function () {
            $(this).closest('.card-box').addClass('d-none')
        })
        // delete btn js end 

        // remove btn js start 
        $('.remove-btn').on('click', function () {
            $(this).closest('.booking-card').addClass('d-none')
        })
        // remove btn js end 

        // ========================== add active class to ul>li top Active current page Js Start =====================
        function dynamicActiveMenuClass(selector) {
            let fileName = window.location.pathname.split("/").reverse()[0];
            selector.find("li").each(function () {
                let anchor = $(this).find("a");
                if ($(anchor).attr("href") == fileName) {
                    $(this).addClass("active");
                }
            });
            // if any li has active element add class
            selector.children("li").each(function () {
                if ($(this).find(".active").length) {
                    $(this).addClass("active");
                }
            });
            // if no file name return
            if ("" == fileName) {
                selector.find("li").eq(0).addClass("active");
            }
        }

        if ($("ul.sidebar-menu-list").length) {
            dynamicActiveMenuClass($("ul.sidebar-menu-list"));
        }
        // ========================== add active class to ul>li top Active current page Js End =====================
        // select2 js 

        $(document).ready(function () {
            $('.select2').select2({
                templateResult: function (option) {
                    if (!option.id) {
                        return option.text;
                    }
                    var imgUrl = $(option.element).data('img');
                    if (imgUrl) {
                        return $(`<span><img src="${imgUrl}"> ${option.text}</span>`);
                    }
                    return option.text;
                },
                templateSelection: function (option) {
                    if (!option.id) {
                        return option.text;
                    }
                    var imgUrl = $(option.element).data('img');
                    if (imgUrl) {
                        return $(`<span><img src="${imgUrl}"> ${option.text}</span>`);
                    }
                    return option.text;
                }
            });
        });

        // ========================= Range Slider Ui Js Start =====================
        // $("#slider-range").slider({
        //     range: true,
        //     min: 0,
        //     max: 500,
        //     values: [0, 320],
        //     slide: function (event, ui) {
        //         $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
        //     }
        // });

        // $("#amount").val("$" + $("#slider-range").slider("values", 0) +
        //     " - $" + $("#slider-range").slider("values", 1));

        // ========================= Range Slider Ui Js end =====================

        // ================== Password Show Hide Js Start ==========
        $(".toggle-password").on('click', function () {
            const input = $(this).siblings('input');
            $(this).toggleClass(" fa-eye");

            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
        // =============== Password Show Hide Js End =================

        // sidebar js 
        $('.profile-filter').on('click', function () {
            $(".sidebar-menu").addClass('show-sidebar');
            $(".sidebar-overlay").addClass('show');
        });

        $('.sidebar-menu__close, .sidebar-overlay').on('click', function () {
            $(".sidebar-menu").removeClass('show-sidebar');
            $(".sidebar-overlay").removeClass('show');
        });

        /*===================== action btn js start here =====================*/
        $('.action-btn__icon').on('click', function (event) {
            event.stopPropagation(); // Prevent click from bubbling to document
            $('.action-dropdown').not($(this).parent().find('.action-dropdown')).removeClass('show');
            $(this).parent().find('.action-dropdown').toggleClass('show');
        });

        $(document).on('click', function () {
            $('.action-dropdown').removeClass('show');
        });
        /*===================== action btn js end here =====================*/

        //========================18. hotel details slider js=========================
        // ==========slider js end========

        // Smooth scroll on click
        $('.content-block-link').on('click', function (e) {
            e.preventDefault();
            var target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 200
                }, 600);
            }
        });

        // ScrollSpy on scroll
        $(window).on('scroll', function () {
            var scrollPos = $(document).scrollTop() + 201;

            $('.widget_component-wrapper').each(function () {
                var id = $(this).attr('id');
                var offsetTop = $(this).offset().top;

                if (scrollPos >= offsetTop) {
                    $('.content-block-link').removeClass('active');
                    $('.content-block-link[href="#' + id + '"]').addClass('active');
                }
            });
        });

        // Trigger once on load
        $(window).trigger('scroll');

        // ================== Sidebar Menu Js Start ===============
        // Sidebar Dropdown Menu Start
        $(".has-dropdown > a").click(function () {
            $(".sidebar-submenu").slideUp(200);
            if ($(this).parent().hasClass("active")) {
                $(".has-dropdown").removeClass("active");
                $(this).parent().removeClass("active");
            } else {
                $(".has-dropdown").removeClass("active");
                $(this).next(".sidebar-submenu").slideDown(200);
                $(this).parent().addClass("active");
            }
        });
        // Sidebar Dropdown Menu End

        // Sidebar Icon & Overlay js
        $(".filter-icon").on("click", function () {
            $(".hotel-sidebar").addClass("show-hotel-sidebar");
            $(".sidebar-overlay").addClass("show");
        });

        $(".sidebar-filter__close, .sidebar-overlay").on("click", function () {
            $(".hotel-sidebar").removeClass("show-hotel-sidebar");
            $(".sidebar-overlay").removeClass("show");
        });
        // Sidebar Icon & Overlay js
        // ===================== Sidebar Menu Js End =================

        // ==================== Dashboard User Profile Dropdown Start ==================
        $(".user-info__button").on("click", function () {
            $(".user-info-dropdown").toggleClass("show");
        });

        $(".user-info__button").attr("tabindex", -1).focus();

        $(".user-info__button").on("focusout", function () {
            $(".user-info-dropdown").removeClass("show");
        });
        // ==================== Dashboard User Profile Dropdown End ==================
    });
    // ==========================================
    //      End Document Ready function
    // ==========================================

    // ========================= Preloader Js Start =====================
    $(window).on("load", function () {
        $(".preloader").fadeOut();
    });
    // ========================= Preloader Js End=====================

    // // ========================= Header Sticky Js Start ==============
    $(window).on("scroll", function () {
        if ($(window).scrollTop() >= 100) {
            $(".header").addClass("fixed-header");
        } else {
            $(".header").removeClass("fixed-header");
        }
    });
    // // ========================= Header Sticky Js End===================

    // //============================ Scroll To Top Icon Js Start =========
    var btn = $(".scroll-top");
    $(window).scroll(function () {
        if ($(window).scrollTop() > 300) {
            btn.addClass("show");
        } else {
            btn.removeClass("show");
        }
    });

    btn.on("click", function (e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "300");
    });

    // hotel sidebar js 
    $(document).ready(function () {
        $('.filter-block__list').each(function () {
            var $block = $(this);
            var $checkboxes = $block.find('.filter-block__item');
            var $loadMoreButton = $block.find('.load-more-button');
            var itemsToShow = 5;

            function toggleCheckboxesVisibility() {
                $checkboxes.hide().slice(0, itemsToShow).show();
            }

            $loadMoreButton.on('click', function () {
                itemsToShow = (itemsToShow === 5) ? $checkboxes.length : 5;
                $loadMoreButton.text((itemsToShow === 5) ? 'Load More' : 'Show Less');
                toggleCheckboxesVisibility();
            });

            toggleCheckboxesVisibility();
            $loadMoreButton.toggle($checkboxes.length > itemsToShow);
        });
    });
    // hotel sidebar js 
})(jQuery);
