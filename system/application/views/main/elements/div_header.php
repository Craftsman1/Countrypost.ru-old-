<?
View::show('main/elements/div_top');

if (isset($user) AND $user AND
	($pageinfo['mname'] != 'index' OR
	$this->uri->segment(1) == 'dealers' OR
	$this->uri->segment(1) == 'clients' OR
	$this->uri->segment(1) == 'profile' OR
	$this->uri->segment(1) == 'terms'))
{
	View::show($user->user_group.'/elements/div_header');
}
elseif (isset($user) AND
	$user AND
	$pageinfo['mname'] == 'index' AND
	$this->uri->segment(1) != 'dealers' AND
	$this->uri->segment(1) != 'clients')
{
	View::show('main/elements/auth/header');
}
elseif ($pageinfo['mname'] == 'index' AND
		$this->uri->segment(1) != 'dealers' AND
		$this->uri->segment(1) != 'clients' AND
		$this->uri->segment(1) != 'signup' AND
		$this->uri->segment(1) != 'terms')
{
	View::show('main/elements/auth/small_form');
}
else
{
	View::show('main/elements/auth/big_form');
}
?>