// main.js - Основной JavaScript для темы FXForTrader

jQuery(document).ready(function($) {
    'use strict';

    // Preloader
    if ($('.loader-wrap').length) {
        $('.loader-wrap').delay(1000).fadeOut(500);
    }

    // Mobile Menu
    if ($('.mobile-menu').length) {
        // Не копируем контент для мобильного меню - он уже есть в PHP
        
        // Menu Toggle Btn
        $('.mobile-nav-toggler').on('click', function() {
            $('body').addClass('mobile-menu-visible');
        });

        // Menu Toggle Btn
        $('.mobile-menu .menu-backdrop, .mobile-menu .close-btn').on('click', function() {
            $('body').removeClass('mobile-menu-visible');
        });
    }

    // Sticky Header
    if ($('.sticky-header').length) {
        // Копируем меню только если sticky header пустой
        if ($('.sticky-header .main-menu').is(':empty') || $('.sticky-header .main-menu').children().length === 0) {
            // Копируем всю структуру основного меню
            var mainMenuContent = $('.header-lower .main-menu').html();
            $('.sticky-header .main-menu').html(mainMenuContent);
            
            // Удаляем второй ul (кнопки авторизации) из sticky header
            $('.sticky-header .main-menu ul.navigation').eq(1).remove();
        }
        
        $(window).on('scroll', function() {
            var scroll = $(window).scrollTop();
            if (scroll >= 100) {
                $('.sticky-header').addClass('fixed-header animated slideInDown');
            } else {
                $('.sticky-header').removeClass('fixed-header animated slideInDown');
            }
        });
    }

    // Owl Carousel
    if ($('.owl-carousel').length) {
        $('.banner-carousel').owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            smartSpeed: 500,
            autoplay: 4000,
            autoplayTimeout: 4000,
            navText: ['<span class="icon-16"></span>', '<span class="icon-16"></span>'],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                800: {
                    items: 1
                },
                1024: {
                    items: 1
                }
            }
        });
    }

    // Scroll to Top
    if ($('.scroll-to-top').length) {
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 200) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });
        
        $('.scroll-to-top').on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600);
            return false;
        });
    }

    // Nice Select
    if ($('select').length) {
        $('select').niceSelect();
    }

    // Accordion
    if ($('.accordion-box').length) {
        $(".accordion-box").on('click', '.acc-btn', function() {
            var outerBox = $(this).parents('.accordion');
            var target = $(this).parents('.accordion');

            if ($(this).hasClass('active') !== true) {
                $('.accordion .acc-btn').removeClass('active');
            }

            if ($(this).next('.acc-content').is(':visible')) {
                return false;
            } else {
                $(this).addClass('active');
                $('.accordion').removeClass('active-block');
                $('.accordion .acc-content').slideUp(300);
                target.addClass('active-block');
                $(this).next('.acc-content').slideDown(300);
            }
        });
    }

    // WOW Animation
    if ($('.wow').length) {
        var wow = new WOW({
            boxClass: 'wow',
            animateClass: 'animated',
            offset: 0,
            mobile: false,
            live: true
        });
        wow.init();
    }

    // Parallax Scene
    if ($('.parallax-scene').length) {
        $('.parallax-scene').parallax();
    }

    // Counter
    if ($('.count-box').length) {
        $('.count-box').appear(function() {
            var $t = $(this),
                n = $t.find(".count-text").attr("data-stop"),
                r = parseInt($t.find(".count-text").attr("data-speed"), 10);

            if (!$t.hasClass("counted")) {
                $t.addClass("counted");
                $({
                    countNum: $t.find(".count-text").text()
                }).animate({
                    countNum: n
                }, {
                    duration: r,
                    easing: "linear",
                    step: function() {
                        $t.find(".count-text").text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $t.find(".count-text").text(this.countNum);
                    }
                });
            }
        }, {
            accY: 0
        });
    }

    // Tabs Box
    if ($('.tabs-box').length) {
        $('.tabs-box .tab-buttons .tab-btn').on('click', function(e) {
            e.preventDefault();
            var target = $($(this).attr('data-tab'));

            if ($(target).is(':visible')) {
                return false;
            } else {
                target.parents('.tabs-box').find('.tab-buttons').find('.tab-btn').removeClass('active-btn');
                $(this).addClass('active-btn');
                target.parents('.tabs-box').find('.tabs-content').find('.tab').fadeOut(0);
                target.parents('.tabs-box').find('.tabs-content').find('.tab').removeClass('active-tab');
                $(target).fadeIn(300);
                $(target).addClass('active-tab');
            }
        });
    }

    // Product Price Info System
    const productInfo = {
        'soft_example': {
            'prices': {
                'mini': '950 руб - 1 месяц',
                'full': '1950 руб - 3 месяца',
                'max': '2950 руб - 6 месяцев'
            },
            'date-end': {
                'mini': calculateEndDate(30),
                'full': calculateEndDate(90),
                'max': calculateEndDate(180)
            }
        },
        'learning_example': {
            'prices': {
                'mini': '950 руб - 1 месяц',
                'full': '1950 руб - 3 месяца',
                'max': '2950 руб - 6 месяцев'
            },
            'date-end': {
                'mini': calculateEndDate(30),
                'full': calculateEndDate(90),
                'max': calculateEndDate(180)
            }
        }
    };

    // Calculate end date
    function calculateEndDate(days) {
        const date = new Date();
        date.setDate(date.getDate() + days);
        return date.toLocaleDateString('ru-RU');
    }

    // Initialize prices (for product pages)
    function initPrices() {
        const prices = document.querySelector('[data-prices]');
        if (!prices) return;
        
        const pricesItems = prices.querySelectorAll('[data-price]');
        if (!pricesItems || pricesItems.length === 0) return;
        
        pricesItems.forEach(price => {
            price.onclick = () => {
                pricesItems.forEach(p => {
                    p.classList.remove('active');
                });
                price.classList.add('active');
                
                const dateEndNode = document.querySelector('[data-date-end]');
                if (dateEndNode) {
                    const dataInfo = document.querySelector('[data-info]');
                    if (dataInfo) {
                        const info = dataInfo.getAttribute('data-info');
                        const priceType = price.getAttribute('data-price');
                        if (productInfo[info] && productInfo[info]['date-end'][priceType]) {
                            dateEndNode.textContent = productInfo[info]['date-end'][priceType];
                        }
                    }
                }
                prices.setAttribute('data-prices', price.getAttribute('data-price'));
            };
        });
        
        // Click first price if exists
        if (prices.hasAttribute('data-prices')) {
            const firstPrice = document.querySelector(`[data-price="${prices.getAttribute('data-prices')}"]`);
            if (firstPrice) {
                firstPrice.click();
            }
        }
    }

    // Initialize prices on page load (only for product pages)
    if (document.querySelector('[data-prices]')) {
        initPrices();
    }
});

// Window Load Function
jQuery(window).on('load', function() {
    // Isotope
    if (jQuery('.sortable-masonry').length) {
        var winDow = jQuery(window);
        var jQuerycontainer = jQuery('.sortable-masonry .items-container');
        var jQueryfilter = jQuery('.filter-btns');
        jQuerycontainer.isotope({
            filter: '*',
            masonry: {
                columnWidth: '.masonry-item'
            },
            animationOptions: {
                duration: 500,
                easing: 'linear'
            }
        });

        jQueryfilter.find('li').on('click', function() {
            var selector = jQuery(this).attr('data-filter');
            try {
                jQuerycontainer.isotope({
                    filter: selector,
                    animationOptions: {
                        duration: 500,
                        easing: 'linear',
                        queue: false
                    }
                });
            } catch (err) {}
            return false;
        });

        winDow.on('resize', function() {
            var selector = jQueryfilter.find('li.active').attr('data-filter');
            jQuerycontainer.isotope({
                filter: selector,
                animationOptions: {
                    duration: 500,
                    easing: 'linear',
                    queue: false
                }
            });
        });

        var filterItemA = jQuery('.filter-btns li');
        filterItemA.on('click', function() {
            var jQuerythis = jQuery(this);
            if (!jQuerythis.hasClass('active')) {
                filterItemA.removeClass('active');
                jQuerythis.addClass('active');
            }
        });
    }
});