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

function updatePerPage(dropdown, handler)
{
	var id = $(dropdown).find('option:selected').val();
	window.location.href = '/' + handler + '/updatePerPage/' + id;
}

function getNowDate()
{
	var date = new Date();
	var day = date.getDate();
	var month = date.getMonth();
	var hours = date.getHours();
	var minutes = date.getMinutes();
	
	if (month < 10)
	{
		month = '0' + month;
	}

	if (day < 10)
	{
		day = '0' + day;
	}

	if (hours < 10)
	{
		hours = '0' + hours;
	}

	if (minutes < 10)
	{
		minutes = '0' + minutes;
	}
	
	return (day + "." + month + "." + date.getFullYear() + ' ' + hours + ':' + minutes);
}