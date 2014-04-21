<div id="header">
	<div style="float:left; width: 1000px;">&nbsp;
		
		<a href="<?=$this->config->item('base_url')?>">�� �������</a>
	
	</div>
	<? if (isset($user) && $user):?>
	
		<div id="welcome">
			������������, <?=$user->user_login;?>
			<a href="<?=$this->config->item('base_url')?>user/logout" >�����</a>
		</div>
	
	<? else:?>
	
		<div id="loginForm">
			<form id="loginForm" name="loginForm" method="POST" action="<?=$this->config->item('base_url')?>user/login">
				<table>
					<tr>
						<td>Login: </td>
						<td><input type="text" name="login" /></td>
					</tr>	
					<tr>
						<td>Password: </td>			
						<td><input type="password" name="password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>			
						<td><input type="submit" value="login"/></td>
					</tr>		
				</table>
			</form>
			
			<a href="/user/registration">�����������</a>
			|
			<a href="/user/passwordRecovery">�������������� ������</a>
			
		</div>
	
	<? endif;?>
</div>