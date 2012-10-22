function validate_float(evt) 
{
	validate_generic(evt, /[0-9]|\./);
}

function validate_number(evt) 
{
	validate_generic(evt, /[0-9]/);
}

function validate_generic(evt, regex)
{
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode(key);
	
	if ( ! regex.test(key)) 
	{
		theEvent.returnValue = false;
		theEvent.preventDefault();
	}
}

function noty_generic(layout, message, ntype)
{
	var n = noty({
		text: message,
		type: ntype,
		dismissQueue: true,
		layout: layout,
		theme: 'defaultTheme',
		timeout: 2000
	});
	console.log('html: '+n.options.id);
}

function success(layout, message)
{
	noty_generic(layout, message, 'success');
}

function error(layout, message)
{
	noty_generic(layout, message, 'alert');
}