<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once(ROOT.'PHPMailer/src/PHPMailer.php');
require_once(ROOT.'PHPMailer/src/SMTP.php');
class SendMail{
	
	public function SendEMail($mailto, $subject, $content, $conf){
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->SMTPAuth = true;
		$mail->Host = $conf['smtp_host'];
		$mail->Username = $conf['smtp_user'];
		$mail->Password = $conf['smtp_pass'];
		$mail->SMTPSecure = $conf['smtp_secure'];
		$mail->Port = $conf['smtp_port'];
		$mail->setFrom($conf['smtp_user'], '云塔IDC财务管理系统');
		$mail->isHTML(true);
		$mail->charSet = "UTF-8";
		$mail->addAddress($mailto);
		$mail->Subject = $subject;
		$mail->Body = $content;
		$mail->send();
	}
	
	public function FindPassMail($user, $code, $conf, $site){
		$subject = "[云塔IDC系统]用户密码找回";
		$mailto = $user['email'];
		$content = "亲爱的客户您好：<br /><br />
		您在{$site['title']}请求了重置密码，您的验证码为：{$code}，若非您本人所谓无需理会此邮件！<br /><br />{$site['title']}<br />{$site['domain']}";
		$this->SendEMail($mailto, $subject, $content, $conf);
	}
	
	public function ServiceRenewMail($user, $service, $conf, $site){
		$subject = "[云塔IDC系统]产品续费提醒";
		$mailto = $user['email'];
		$content = "亲爱的客户您好：<br /><br />
		您在{$site['title']}购买的服务即将到期，服务账号为：{$service['username']}，请尽快前往续费避免数据丢失，再次感谢您选择了我们<br /><br />{$site['title']}<br />{$site['domain']}";
		$this->SendEMail($mailto, $subject, $content, $conf);
	}
	
	public function WorderReplyMail($user, $worder, $conf, $site){
		$subject = "[云塔IDC系统]工单回复提醒";
		$mailto = $user['email'];
		$content = "亲爱的客户您好：<br /><br />
		您在{$site['title']}提交的工单已经被回复，服务账号为：{$worder['title']}，请尽快前往查看回复，再次感谢您选择了我们<br /><br />{$site['title']}<br />{$site['domain']}";
		$this->SendEMail($mailto, $subject, $content, $conf);
		
	}
	
}
?>