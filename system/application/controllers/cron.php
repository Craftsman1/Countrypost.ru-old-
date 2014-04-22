<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;
		
class Cron extends BaseController {
	function __construct()
	{
		parent::__construct();	

		$this->config->load('cron');
	}
	
	public function sendAdminCommentsNotifications()
	{
		$this->load->model('UserModel', 'Users');
		
		self::sendAdminPCommentsNotifications($this->Users);
		self::sendAdminOCommentsNotifications($this->Users);
	}
	
	private function sendAdminPCommentsNotifications($users)
	{
		try
		{
			$this->load->model('PCommentModel', 'PComments');
			
			if ($comments = $this->PComments->getUnansweredComments($this->config->item('admin_comment_notification_delay')))
			{
				foreach ($comments as $comment)
				{
					Mailer::sendAdminNotification(
						Mailer::SUBJECT_NEW_COMMENT, 
						Mailer::NEW_PACKAGE_COMMENT_NOTIFICATION, 
						0,
						$comment->pcomment_package, 
						0,
						"http://countrypost.ru/admin/showPackageDetails/{$comment->pcomment_package}#comments",
						null,
						$this->Users);
						
					$pcomment = $this->PComments->getById($comment->pcomment_id);
					
					if ($pcomment)
					{
						$pcomment->pcomment_admin_notification_sent = 1;
						$this->PComments->addComment($pcomment);
					}					

					print("Отправлено уведомление о комментарии к посылке №{$comment->pcomment_package}<br />");
				}
			}
		}
		catch (Exception $ex)
		{
			print_r($ex);
		}
	}

	private function sendAdminOCommentsNotifications($users)
	{
		try
		{
			$this->load->model('OCommentModel', 'OComments');
			
			if ($comments = $this->OComments->getUnansweredComments($this->config->item('admin_comment_notification_delay')))
			{
				foreach ($comments as $comment)
				{
					Mailer::sendAdminNotification(
						Mailer::SUBJECT_NEW_COMMENT, 
						Mailer::NEW_ORDER_COMMENT_NOTIFICATION, 
						0,
						$comment->ocomment_order, 
						0,
						"http://countrypost.ru/admin/showOrderDetails/{$comment->ocomment_order}#comments",
						null,
						$users);
						
					$ocomment = $this->OComments->getById($comment->ocomment_id);
					
					if ($ocomment)
					{
						$ocomment->ocomment_admin_notification_sent = 1;
						$this->OComments->addComment($ocomment);
					}
					
					print("Отправлено уведомление о комментарии к заказу №{$comment->ocomment_order}<br />");
				}
			}
		}
		catch (Exception $ex)
		{
			print_r($ex);
		}
	}


}