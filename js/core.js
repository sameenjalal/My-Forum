function input_focus(id)
{
	$("#l"+id).addClass('lcurrent');
}

function input_blur(id)
{
	$("#l"+id).removeClass('lcurrent');
}

function sendContact()
{
	// check for email
	var email = $("#email").val();
	var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
	if(!filter.test(email))	
	{
		$("#email-error").slideDown(500);
		$("#email").focus();
		return false;
	}
	else
		$("#email-error").slideUp(500);
	
	// check for message
	var msg = $("#message").val();
	if(msg.length == 0)
	{
		$("#message-error").slideDown(500);
		$("#message").focus();
		return false;
	}
	else
		$("#message-error").slideUp(500);
		
	// check for captcha
	var captcha = $("#captcha").val();
	if(captcha != captcha_c)
	{
		$("#captcha-error").slideDown(500);
		$("#captcha").focus();
		return false;
	}
	else
		$("#captcha-error").slideUp(500);	
	
	var data = $("#contact_form > form").serialize();

	$.ajax({
		type: "POST",
		url: "sendContact.php",
		data: data,
		cache: false,
		success: function(msg){
		}
	});
	
	$("#contact_form").fadeOut(1000, function() {
		$("#message_sent").slideDown(500);
	});
	
	
	return false;
}

var captcha_a = Math.ceil(Math.random() * 10);
var captcha_b = Math.ceil(Math.random() * 10);       
var captcha_c = captcha_a + captcha_b;
function generate_captcha(id)
{
	var id = (id) ? id : 'lcaptcha';
	$("#"+id).html(captcha_a + " + " + captcha_b + " = ");
}

var jGalleryTimer = 0;
var jGalleryFirstStart = true;
var jGallery_action = false;
function jGallery(id, visible, timeInterval, transitionInterval)
{
	var visible = (visible) ? visible : 1;
	var timeInterval = (timeInterval) ? timeInterval : 5000;
	var transitionInterval = (transitionInterval) ? transitionInterval : 200;
	var w = (w) ? w : $("."+id+"-gallery-div :first").width();
	var cnt = $("#gallery-"+id+"-holder > div").size();
	
	if(jGalleryTimer)
	{
		clearInterval(jGalleryTimer);
		jGalleryTimer = 0;
	}
	
	if(!jGalleryFirstStart)
	{
		if(!jGallery_move(id, cnt, -1, w, visible, transitionInterval))
			jGallery_restart(id, cnt, transitionInterval);
	}
	
	jGalleryFirstStart = false;
	
	jGalleryTimer = setInterval(function(){ jGallery(id, visible, timeInterval, transitionInterval); }, timeInterval);
}

function jGallery_move(id, cnt, dir, w, visible, transitionInterval)
{
	if(jGallery_action)
		return false;
		
	var curr = document.getElementById("gallery-"+id+"-holder").style.left;
	curr = parseFloat(curr);

	if(isNaN(curr))
		curr = 0;
	if(dir > 0)
	{
		if(curr >= 0)
			return false;
	}
	else
	{
		if(curr + cnt * w - visible * w <= 0)
			return false;
	}

	jGallery_action = true;
	var offset = w;

	if(dir < 0)
		dir = "-";
	else
		dir = "+";
		
	$("#gallery-"+id+"-holder").animate(
		{left : dir+"="+offset+"px"},
		{queue:true, duration:transitionInterval, complete: function() {jGallery_action = false;}}
	);
	
	return true;
}

function jGallery_restart(id, cnt, transitionInterval)
{
	if(jGallery_action)
		return false;
		
	var curr = document.getElementById("gallery-"+id+"-holder").style.left;
	curr = parseFloat(curr);

	if(isNaN(curr))
		curr = 0;
	if(curr >= 0)
		return false;

	jGallery_action = true;
	var offset = curr * (-1);

	$("#gallery-"+id+"-holder").animate(
		{left : "+="+offset+"px"},
		{queue:true, duration:transitionInterval*cnt, complete: function() {jGallery_action = false;}}
	);
	
	return true;
}

var jMenu_timeout    = 500;
var jMenu_effectTime = 200;
var jMenu_closetimer = 0;
var jMenu_ddmenuitem = 0;
var jMenu_openid = 0;
var jMenu_action = false;
function jMenu_open()
{
	jMenu_canceltimer();
	
	if($("a", this).html() == jMenu_openid)
		return;
		
	if(jMenu_action)
		return;
		
	jMenu_close();

	if($("ul", this).size() == 0)
		return;
	
	jMenu_action = true;
	jMenu_ddmenuitem = $(this).find('ul').slideDown(jMenu_effectTime, function() {jMenu_action = false;});
	jMenu_openid = $("a", this).html();
	if (document.getElementById('ul'))
		document.getElementById('ul').className = 'current';
}

function jMenu_close()
{
	if(jMenu_action)
		return;
			
	if(jMenu_ddmenuitem)
	{
		jMenu_action = true;
		jMenu_ddmenuitem.fadeOut(jMenu_effectTime, function() {jMenu_action = false;});
		jMenu_ddmenuitem = null;
		jMenu_openid = null;
	}
}

function jMenu_timer()
{
	jMenu_closetimer = window.setTimeout(jMenu_close, jMenu_timeout);
}

function jMenu_canceltimer()
{
	if(jMenu_closetimer)
	{
		window.clearTimeout(jMenu_closetimer);
		jMenu_closetimer = null;
	}
}

$(document).ready(function() {
	$('#jMenu > li').bind('mouseover', jMenu_open)
	$('#jMenu > li').bind('mouseout',  jMenu_timer)
	$('#jMenu > li > ul').bind('mouseover',  jMenu_canceltimer)
	$('#jMenu > li > ul > li').bind('mouseover',  jMenu_canceltimer)
});

document.onclick = jMenu_close;
