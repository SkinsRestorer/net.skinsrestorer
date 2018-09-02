/*   ___       __             __       ___      ___   ___  __    __       _______.    _______  __    __
    /   \     |  |           |  |     /   \     \  \ /  / |  |  |  |     /       |   |   ____||  |  |  |
   /  ^  \    |  |           |  |    /  ^  \     \  V  /  |  |  |  |    |   (----`   |  |__   |  |  |  |
  /  /_\  \   |  |     .--.  |  |   /  /_\  \     >   <   |  |  |  |     \   \       |   __|  |  |  |  |
 /  _____  \  |  `----.|  `--'  |  /  _____  \   /  .  \  |  `--'  | .----)   |    __|  |____ |  `--'  |
/__/     \__\ |_______| \______/  /__/     \__\ /__/ \__\  \______/  |_______/    (__)_______| \______/
*/
///////////////////////////////////////////////////////////
///////////////////// GLOBAL FUNCTIONS ////////////////////
///////////////////////////////////////////////////////////
function currectTime(){
  var time = new Date($.now());
  time = time.getHours()+"h "+time.getMinutes()+"m "+time.getSeconds()+"s ";
  return time;
}
function animateNumLong(element, number){
    var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',')
    $(element).animateNumber(
      {
        number: number,
        numberStep: comma_separator_number_step
      }
    );
}
function copyToClipboard(text, msg = "Besedilo kopirano v odložišče"){
    let $temp = $("<textarea>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();

	M.toast({html: '<i class="material-icons left">check</i> '+msg,classes: 'green darken-1'});
}
function cookieCheckTerms(){
	if ( !("terms_accept" in Cookies.get()) ){
		$('#modal-terms').modal('open');
		return false;
	} else {
		if( Cookies.get().terms_accept=="false" ){
			$('#modal-terms').modal('open');
			return false;
		} else if ( Cookies.get().terms_accept=="true" ) {
			return true;
		} else {
			return false;
		}
	}
}
function bbcode2html(bbcode = ''){
	let html = bbcode;

	$format_search =  [
	    /\n/ig,
	    /\r\n/ig,
	    /\[b\]([\n\s\S]*)?\[\/b\]/ig,
	    /\[i\]([\n\s\S]*)?\[\/i\]/ig,
	    /\[u\]([\n\s\S]*)?\[\/u\]/ig,
	    /\[s\]([\n\s\S]*)?\[\/s\]/ig,
	    /\[sub\]([\n\s\S]*)?\[\/sub\]/ig,
	    /\[sup\]([\n\s\S]*)?\[\/sup\]/ig,
	    /\[left\]([\n\s\S]*)?\[\/left\]/ig,
	    /\[center\]([\n\s\S]*)?\[\/center\]/ig,
	    /\[right\]([\n\s\S]*)?\[\/right\]/ig,
	    /\[justify\]([\n\s\S]*)?\[\/justify\]/ig,
	    /\[font=([\n\s\S]*)?\]([\n\s\S]*)?\[\/font\]/ig,
	    /\[size=([1-7])\]([\n\s\S]*)?\[\/size\]/ig,
	    /\[color=\#([a-z0-9]{6})\]([\n\s\S]*)?\[\/color\]/ig,
	    /\[ul\]([\n\s\S]*)?\[\/ul\]/ig,
		/\[ol\]([\n\s\S]*)?\[\/ol\]/ig,
	    /\[li\]([\n\s\S]*)?\[\/li\]/ig,
	    /\[img\]((https?:\/\/)?([a-zA-Z0-9\.]+\.([a-zA-Z0-9]{1,9}))((\/|\?)([a-zA-Z0-9\-\_\/\.\%]*))?)\[\/img\]/ig,
	    /\[img=([0-9]+)x([0-9]+)\]((https?:\/\/)?([a-zA-Z0-9\.]+\.([a-zA-Z0-9]{1,9}))((\/|\?)([a-zA-Z0-9\-\_\/\.\%]*))?)\[\/img\]/ig,
	    /\[youtube\]([a-zA-Z0-9]+)\[\/youtube\]/ig,
	    /\[email=([a-zA-Z0-9)\@([a-zA-Z0-9\.]+\.[a-zA-Z0-9]{1,9})\](.*)\[\/email\]/ig,
	    /\[url=((https?:\/\/)?((([a-zA-Z0-9]*\.){1,2})?[a-zA-Z0-9]*\.[a-zA-Z0-9]{1,9})((\/|\?)([^\s]*)?)?)\](.*)\[\/url\]/ig,
	    /\[text\]([\n\s\S]*)?\[\/text\]/ig,
	    /\[text\]([\n\s\S]*)?\[\/text\]/ig,
	    /\[text\]([\n\s\S]*)?\[\/text\]/ig
	];

	$format_replace = [
		'<br>',																		// break line - type 1
		'<br>',																		// break line - type 2
	    '<strong>$1</strong>',														// bold
	    '<em>$1</em>',																// italic
	    '<span style="text-decoration: underline;">$1</span>',						// underlined
	    '<span style="text-decoration: line-through;">$1</span>',					// strikethough
	    '<sub>$1</sub>',															// subscript
	    '<sup>$1</sup>',															// superscript
	    '<p style="margin-top:0;margin-bottom:0;text-align: left;">$1</p>',			// left
	    '<p style="margin-top:0;margin-bottom:0;text-align: center;">$1</p>',		// center
	    '<p style="margin-top:0;margin-bottom:0;text-align: right;">$1</p>',		// right
	    '<p style="margin-top:0;margin-bottom:0;text-align: justify;">$1</p>',		// justify
	    '<font face="$1">$2</font>',												// font
	    '<font size="$1">$2</font>',												// size
	    '<font color="$1">$2</font>',												// colour
	    '<ul class="browser-default">$1</ul>',										// ul
		'<ol class="browser-default">$1</ol>',										// ol
	    '<li>$1</li>',																// li
	    '<img src="$1">',															// img - src
	    '<img width="$1" height="$2" src="$3">',									// img - sizes && src
	    '<iframe width="560" height="315" frameborder="0" src="https://www.youtube.com/embed/$1?wmode=opaque" data-youtube-id="$1" allowfullscreen></iframe>',	// youtube video
	    '<a target="_blank" href="mailto:$1" title="$1">$2</a>',								// email
	    '<a target="_blank" href="$1" title="$1">$9</a>'										// url
	];

	for (var i=0; i<$format_search.length; i++) {
	  html = html.replace($format_search[i], $format_replace[i]);
	}
	return html;
}
