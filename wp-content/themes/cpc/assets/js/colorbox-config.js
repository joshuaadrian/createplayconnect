// JQuery Plugin for parsing the query string
(function($) {
    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'))
})(jQuery);

function openiFrameColorbox( url ) {

	if ( typeof url != "undefined" ) {

		$.colorbox({
			href          : url,
			iframe        : true, 
			transition    : "none",
			width         : "1400px",
			height        : "90%", 
			initialWidth  : "20%", 
			initialHeight : "20%", 
			left          : "5%"
		});

	} else {

		return false;

	}

}

function youTubeGetID( url ) {

  var ID = '';
  url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
  if(url[2] !== undefined) {
    ID = url[2].split(/[^0-9a-z_-]/i);
    ID = ID[0];
  } else {
    ID = url;
  }
  return ID;

}

function vimeoGetID( url ) {

	var ID = '';

	url = url.split( '/' );

	if ( url[3] !== undefined ) {
		ID = url[3];
	}

	return ID;

}

$(document).ready(function() {

	// For Desktop overlays
	if (screen.width > 768) {

		$("a[href*='youtu.be'],a[href*='youtube']").each( function() {
			var videoID = youTubeGetID( $(this).attr( 'href' ) );
			$(this).addClass('video-link').attr( 'href', "http://www.youtube.com/embed/" + videoID  + "?rel=0&amp;autoplay=1&amp;wmode=transparent");
		});

		$("a[href*='vimeo']").each( function() {
			var videoID = vimeoGetID( $(this).attr( 'href' ) );
			$(this).addClass('video-link').attr( 'href', "http://player.vimeo.com/video/" + videoID  + "?autoplay=1");
		});

		// Video Player (include video URL in a tag href)
		$("a[href*='youtu.be'],a[href*='youtube'],a[href*='vimeo']").colorbox({
			iframe: 				true, 
			transition: 		"none",
			innerWidth: 		"90%", 
			innerHeight: 		"90%",
			onComplete : function( element ) {

				$('#colorbox').addClass('video-cbox');
				var caption = $(element.el).attr('title');

				if ( caption ) {
					$('#cboxTitle').html( caption );
				} else {
					$('#cboxTitle').addClass('video-caption-hidden');
				}

			},
			onClosed : function( element ) {
				$('#cboxTitle').html('');
				$('#colorbox').removeClass('video-cbox');
				$('#cboxTitle').removeClass('video-caption-hidden');
			}
		});
		
	}

});