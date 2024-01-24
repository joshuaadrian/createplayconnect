$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

jQuery(document).ready(function($) {

  if ( $('.content-block-news-slides-wrapper').length > 0 ) {

    $('.content-block-news-slides-wrapper').each( function( index ) {

      var curTab      = $(this);
      var curTabCopy  = curTab.find('.content-block-tabbed-copy');
      var curTabs     = curTab.find('.content-block-news-slide');
      var curTabsPag  = curTab.find('.content-block-news-pagination li');
      var curTabLinks = curTabsPag.find('a');

      if ( $(window).width() > 768 ) {

        // curTab.css('width', ( 100 * curTabs.length ) + '%');
        // curTabs.css('width', ( 100 / curTabs.length ) + '%');

      }

      // var facultyID = curTabCopy.first().find('.faculty').attr('id');

      // if ( $('html').hasClass('touch') && facultyID !== undefined ) {

      //   window.mySwipe = Swipe( document.getElementById( facultyID ), {
      //     callback: function(e, pos) {

      //       $(bullets).removeClass('faculty-pag-active');
      //       $(bullets).eq($(pos).data('index')).addClass('faculty-pag-active');

      //     }
      //   }),
      //   bullets = document.getElementById( facultyID + '-pagination' ).getElementsByTagName('a');

      // } 

      $( curTabLinks ).on( 'click', function( event ) {

        event.preventDefault();
        var thisParent = $(this).parent();

        if ( !thisParent.hasClass('is-active') ) {

          var thisParentIndex = curTabsPag.index( thisParent );
          console.log('INDEX => ' + thisParentIndex);

          curTabsPag.removeClass('is-active');
          thisParent.addClass('is-active');
          curTabs.removeClass('is-active').eq( thisParentIndex ).addClass('is-active');
          var newsSliderID = curTab.parent().attr('id');

          if ( $('html').hasClass('touch') && newsSliderID ) {

            window.mySwipe = Swipe( document.getElementById( newsSliderID ), {
              callback: function(e, pos) {

                $(bullets).removeClass('is-active');
                $(bullets).eq($(pos).data('index')).addClass('is-active');

              }
            }),
            bullets = document.getElementById( newsSliderID + '-pagination' ).getElementsByTagName('a');

          }

        }

      });

    });

  }

  //jQuery(".page-header-content-inner h1").fitText(0.9);
  jQuery(".page-header-content-inner h1").lettering().fitText(0.9);
  jQuery(".page-header-content-inner h2").lettering('lines');

  jQuery(".about-copy-1 div").fitText(1.1);
  jQuery(".about-copy-2 div").fitText(2.2);
  jQuery(".about-copy-3 div").fitText(1.8);
 console.log($('.about-image').length);

  for (var i = $('.about-image').length - 1; i >= 0; i--) {
    console.log(i);
    $('.about-image').eq( i ).addClass('in');
  };

  //collapsedMenu();

});

function collapsedMenu() {

  var menuBtn = $('.branding-touch a');

  if ( menuBtn.length > 0 ) {

    menuBtn.on('click', function( event ) {

      event.preventDefault();

      $('#header .navbar').toggleClass('is-open');
      var newText = $('#header .navbar').hasClass('is-open') ? 'Close' : 'Menu';
      menuBtn.children('.text').text( newText );

    });

  }

}

function nameValidated( name ) {
  return name.search( /^[a-zA-Z][a-zA-Z\-'\s]{0,60}$/ ) != -1 ? true : false; 
}

function emailValidated( email ) {
  return email.search( /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/ ) != -1 ? true : false; 
}

function phoneValidated( phone ) {
  return phone.search( /(\W|^)[(]{0,1}\d{3}[)]{0,1}[\s-]{0,1}\d{3}[\s-]{0,1}\d{4}(\W|$)/ ) != -1 ? true : false; 
}

function programValidated( program ) {
  return program != '' ? true : false;
}