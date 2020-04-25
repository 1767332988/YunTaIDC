<?php

/********************
 * 云中心连接文件
 * 连接云中心时使用
 * Powered by 云塔
 * Auther: Ru味筱雨
********************/

class Cloud{
	var $url = 'https://cloud.yunta.cc/';
	
	public function getSystemNews(){
		return json_decode(file_get_contents($this->url.'api/YunTaIDC/News/news.php'), true);
	}
	
	public function GetSystemVersion(){
		return json_decode(file_get_contents($this->url.'api/YunTaIDC/System/version.php'), true);
	}
	
	public function GetSystemUpdate($version){
		$download_url = $this->url.'down/YunTaIDC/update_'.$version.'.zip';
		if(copy(trim($download_url), ROOT.'/update.zip')){
		    echo "远程更新包下载成功......<br>";
		}else{
			exit('下载文件失败！<a href="/sysadmin/index.php">点击返回主系统！</a>');
		}
		$zip = new ZipArchive();
		if($zip->open(ROOT.'/update.zip') !== true){
			exit('无法打开压缩包，请手动前往解压并且删除！<a href="/sysadmin/index.php">点击返回主系统！</a>');
		}
		$zip->extractTo(ROOT);
		$zip->close();
		unlink(ROOT.'/update.zip');
		exit('更新文件成功！<a href="/sysadmin/index.php">点击返回主系统！</a>');
	}
}

?>