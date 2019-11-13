/**
 * Created by lion on 2018/8/16.
 */



$(document).ready(function () {

  // Full height of sidebar
  function fix_height() {
    var heightWithoutNavbar = $("body > #wrapper").height() - 61;
    $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");

    var navbarHeigh = $('nav.navbar-default').height();
    var wrapperHeigh = $('#page-wrapper').height();

    if (navbarHeigh > wrapperHeigh) {
      $('#page-wrapper').css("min-height", navbarHeigh + "px");
    }

    if (navbarHeigh < wrapperHeigh) {
      $('#page-wrapper').css("min-height", $(window).height() + "px");
    }

    if ($('body').hasClass('fixed-nav')) {
      if (navbarHeigh > wrapperHeigh) {
        $('#page-wrapper').css("min-height", navbarHeigh + "px");
      } else {
        $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
      }
    }

  }

  fix_height();

// Minimalize menu
  $('.navbar-minimalize').on('click', function () {
    $("body").toggleClass("mini-navbar");
    SmoothlyMenu();

  });

  // Move right sidebar top after scroll
  $(window).scroll(function () {
    if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) {
      $('#right-sidebar').addClass('sidebar-top');
    } else {
      $('#right-sidebar').removeClass('sidebar-top');
    }
  });

  $(window).bind("load resize scroll", function () {
    if (!$("body").hasClass('body-small')) {
      fix_height();
    }
  });

  $("[data-toggle=popover]")
    .popover();


});
