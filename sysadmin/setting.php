<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	if(empty($_GET['type'])){
		$type = "config";
	}else{
		$type = daddslashes($_GET['type']);
	}
	$permission_type = 'main_'.$type;
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array($permission_type, $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$act = daddslashes($_GET['act']);
if($act == "edit"){
  	foreach($_POST as $k => $v){
      	$value = daddslashes($v);
      	$DB->query("UPDATE `ytidc_config` SET `v`='{$value}' WHERE `k`='{$k}'");
    }
  	@header("Location: ./setting.php?type={$type}");
  	exit;
}
include("./head.php");
if($type == "config"){
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">站点资料</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑资料</div>
        <div class="panel-body">
          <form role="form" action="./setting.php?type=config&act=edit" method="POST">
            <div class="form-group">
              <label>主站标题</label>
              <input type="text" name="mainsite_title" class="form-control" placeholder="主站标题" value="<?=$conf['mainsite_title']?>">
            </div>
            <div class="form-group">
              <label>主站副标题</label>
              <input type="text" name="mainsite_subtitle" class="form-control" placeholder="主站副标题" value="<?=$conf['mainsite_subtitle']?>">
            </div>
            <div class="form-group">
              <label>主站SEO介绍</label>
              <input type="text" name="mainsite_description" class="form-control" placeholder="主站SEO介绍" value="<?=$conf['mainsite_description']?>">
            </div>
            <div class="form-group">
              <label>主站SEO关键词</label>
              <input type="text" name="mainsite_keywords" class="form-control" placeholder="主站SEO关键词" value="<?=$conf['mainsite_keywords']?>">
            </div>
            <div class="form-group">
              <label>客服1QQ</label>
              <input type="text" name="contactqq1" class="form-control" placeholder="客服1QQ" value="<?=$conf['contactqq1']?>">
            </div>
            <div class="form-group">
              <label>客服2QQ</label>
              <input type="text" name="contactqq2" class="form-control" placeholder="客服2QQ" value="<?=$conf['contactqq2']?>">
            </div>
            <div class="form-group">
              <label>邀请用户花费奖励（百分比，1%填1）</label>
              <input name="invitepercent" type="text" class="form-control" id="serverusername" placeholder="邀请用户花费奖励" value="<?=$conf['invitepercent']?>" oninput="value=value.replace(/[^\d.]/g,'')">
            </div>
            <div class="form-group">
              <label>分站公告</label>
              <textarea name="sitenotice" class="form-control"><?=$conf['sitenotice']?></textarea>
            </div>
            <div class="form-group">
              <label>分站可选用二级域名后缀</label>
              <input name="sitedomain" type="text" class="form-control" id="sererdns1" placeholder="分站可选用二级域名" value="<?=$conf['sitedomain']?>">
            </div>
            <div class="form-group">
              <label>分站价格</label>
              <input name="siteprice" type="text" class="form-control" id="serverdns2" placeholder="分站价格" value="<?=$conf['siteprice']?>" oninput="value=value.replace(/[^\d.]/g,'')">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">提交</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
}
if($type == "cloud"){
	?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">云中心控制台</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">云中心开关</div>
        <div class="panel-body">
          <form role="form" action="./setting.php?type=cloud&act=edit" method="POST">
            <div class="form-group">
              <label>接收云中心信息</label>
              <select name="cloud_get_news" class="form-control">
              	<?php
              	if($conf['cloud_get_news'] == 0){
              		echo '<option value="1">开启</option>
              	<option value="0" selected>关闭</option>';
              	}else{
              		echo '<option value="1" selected>开启</option>
              	<option value="0">关闭</option>';
              	}
              	?>
              	
              </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">提交</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
	<?php
}
if($type == "smtp"){
	?>
	
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">SMTP管理</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">SMTP服务设置</div>
        <div class="panel-body">
          <form role="form" action="./setting.php?type=smtp&act=edit" method="POST">
            <div class="form-group">
              <label>服务器地址</label>
              <input type="text" name="smtp_host" class="form-control" placeholder="SMTP服务器地址" value="<?=$conf['smtp_host']?>">
            </div>
            <div class="form-group">
              <label>SMTP账号</label>
              <input type="text" name="smtp_user" class="form-control" placeholder="SMTP账号" value="<?=$conf['smtp_user']?>">
            </div>
            <div class="form-group">
              <label>SMTP密码</label>
              <input type="text" name="smtp_pass" class="form-control" placeholder="SMTP密码" value="<?=$conf['smtp_pass']?>">
            </div>
            <div class="form-group">
              <label>SMTP端口</label>
              <input type="text" name="smtp_port" class="form-control" placeholder="SMTP端口" value="<?=$conf['smtp_port']?>">
            </div>
            <div class="form-group">
              <label>SMTP加密方式</label>
              <select name="smtp_secure" class="form-control">
              <?php
            	if($conf['smtp_secure'] == 'tls'){
            		echo '<option value="tls" selected>TLS加密</option><option value="ssl">SSL加密</option><option value="0">不加密</option>';
            	}elseif($conf['smtp_secure'] == "ssl"){
            		echo '<option value="tls">TLS加密</option><option value="ssl" selected>SSL加密</option><option value="0">不加密</option>';
            	}else{
            		echo '<option value="tls">TLS加密</option><option value="ssl">SSL加密</option><option value="0" selected>不加密</option>';
            	}
              ?>
              </select>
            </div>
            <div class="form-group">
              <label>提前续费提醒（天）</label>
              <input type="text" name="mail_alert" class="form-control" placeholder="续费提醒" value="<?=$conf['mail_alert']?>">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">提交</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
	<?php
}
if($type == "template"){
	$templates = get_dir(ROOT."/templates/");
	?>
	
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">模板管理</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">更换模板</div>
        <div class="panel-body">
          <form role="form" action="./setting.php?type=template&act=edit" method="POST">
            <div class="form-group">
              <label>默认模板</label>
              <select name="template" class="form-control">
              	<?php
              		foreach($templates as $k => $v){
              			if($conf['template'] == $v){
              				$selected = "selected";
              			}else{
              				$selected = "";
              			}
              			echo '<option value="'.$v.'" '.$selected.'>'.$k.'</option>';
              		}
              	?>
              </select>
            </div>
            <div class="form-group">
              <label>手机端模板</label>
              <select name="template_mobile" class="form-control">
              	<?php
              		foreach($templates as $k => $v){
              			if($conf['template_mobile'] == $v){
              				$selected = "selected";
              			}else{
              				$selected = "";
              			}
              			echo '<option value="'.$v.'" '.$selected.'>'.$k.'</option>';
              		}
              	?>
              </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">提交</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
	<?php
}
include("./foot.php");

?>