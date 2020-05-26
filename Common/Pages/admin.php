<?php

use YunTaIDC\Security\Security;
use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\User\User;

class Pages{
    
    public $database;
    public $functions;
    public $security;
    public $user;
    public $site;
    public $subsite;
    public $conf;
    public $getparams;
    
    
    public function __construct($m, $conf, $site, $DB, $getparams){
        $this->security = new Security();
        $this->database = $DB;
        $this->conf = $conf;
        $this->site = $site;
        $this->getparams = $getparams;
        $this->functions = new Functions();
        $this->user = new User("", $DB);
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $this->user = $this->user->GetUserInfo();
        }
        if($this->database->num_rows("SELECT * FROM `ytidc_subsite` WHERE `user`='{$user['id']}'") != 1){
            exit('云塔提醒您：您并没有开通分站噢!');
        }else{
            $this->sutsite = $this->database->get_row("SELECT * FROM `ytidc_subsite` WHERE `user`='{$user['id']}'");
        }
        try{
            $this->$m();
        }catch(Error $e){
            exit("云塔提醒您：".$e);
        }
    }
    
    public function Index(){
        echo $this->Head();
        $usernum = $this->database->num_rows("SELECT * FROM `ytidc_user` WHERE `site`='{$this->subsite['id']}'");
        $order = $this->database->num_rows("SELECT * FROM `ytidc_order` WHERE `user`='{$this->user['id']}'");
        echo '<div class="hbox hbox-auto-xs hbox-auto-sm" ng-init="    app.settings.asideFolded = false;     app.settings.asideDock = false;  ">  <!-- main -->  <div class="col">    <!-- main header -->    <div class="bg-light lter b-b wrapper-md">      <div class="row">        <div class="col-sm-6 col-xs-12">          <h1 class="m-n font-thin h3 text-black">仪表盘</h1>          <small class="text-muted">欢迎使用云塔v3.0</small>        </div>      </div>    </div>    <!-- / main header -->    <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">      <!-- stats -->      <div class="row">        <div class="col-md-5">          <div class="row row-sm text-center">            <div class="col-xs-6">              <div class="panel padder-v item">                <div class="h1 text-info font-thin h1">'.$usernum.'</div>                <span class="text-muted text-xs">旗下用户</span>                <div class="top text-right w-full">                  <i class="fa fa-caret-down text-warning m-r-sm"></i>                </div>              </div>            </div>            <div class="col-xs-6">              <a href class="block panel padder-v bg-primary item">                <span class="text-white font-thin h1 block">'.$order.'</span>                <span class="text-muted text-xs">您的订单</span>                <span class="bottom text-right w-full">                  <i class="fa fa-cloud-upload text-muted m-r-sm"></i>                </span>              </a>            </div>            <div class="col-xs-12 m-b-md">              <div class="r bg-light dker item hbox no-border">                <div class="col dk padder-v r-r">                  <div class="text-primary-dk font-thin h1"><span>'.$this->user['money'].'元</span></div>                  <span class="text-muted text-xs">您的余额</span>                </div>              </div>            </div>          </div>        </div>        <div class="col-md-7">          <div class="panel wrapper">            <label class="i-switch bg-warning pull-right" ng-init="showSpline=true">              <input type="checkbox" ng-model="showSpline">              <i></i>            </label>            <h4 class="font-thin m-t-none m-b text-muted">在线服务</h4>            <div ui-jq="plot" ui-refresh="showSpline"style="height:246px" >            </div>          </div>        </div>      </div>      <!-- / stats -->      <!-- tasks -->      <div class="row">        <div class="col-md-12">          <div class="panel no-border">            <div class="panel-heading wrapper b-b b-light">              <h4 class="font-thin m-t-none m-b-none text-muted">最新消息</h4>                          </div>            <p>'.$this->subsite['notice'].'</p>            <div class="panel-footer">              <span class="pull-right badge badge-bg m-t-xs">More</span>              <a class="btn btn-primary btn-addon btn-sm" href="https://jq.qq.com/?_wv=1027&k=5od4Wkj"><i class="fa fa-plus"></i>更多消息</a>            </div>          </div>        </div>      </div>      <!-- / tasks -->    </div>  </div>  <!-- / main --></div>';
        echo $this->Foot();
    }
    
    public function Notice(){
        echo $this->Head();
        if($this->getparams['add'] == 1){
            if($this->database->exec("INSERT INTO `ytidc_notice` (`title`, `content`, `date`, `site`, `status`) VALUES ('新建公告{$rand}', '', '{$date}', '{$site['id']}', '1')")){
                @header("Location: ./index.php?p=admin&m=Notice");
                exit;
            }else{
                throw new Execption("插入数据库失败");
                return;
            }
        }
        if(isset($this->getparams['page']) && is_numeric($this->getparams['page']) && $this->getparams['page'] >= 1){
        	$page = $this->getparams['page'] - 1;
        }else{
        	$page = 0;
        }
        $start = $page * 10;
        echo '<div class="bg-light lter b-b wrapper-md">          <h1 class="m-n font-thin h3">公告管理</h1>        </div>        <div class="wrapper-md">          <div class="panel panel-default">            <div class="panel-heading">              公告列表<a href="?act=add" class="btn btn-primary btn-xs btn-small">添加</a>            </div>            <div class="table-responsive">              <table class="table table-striped b-t b-light">                <thead>                  <tr>                    <th>编号</th>                    <th>题目</th>                    <th>操作</th>                  </tr>                </thead>                <tbody>                	';                  	 foreach($this->database->get_rows("SELECT * FROM `ytidc_notice` WHERE `site`='{$this->subsite['id']}' LIMIT {$start}, 10") as $row){                  	 	echo '<tr>                    <td>'.$row['id'].'</td>                    <td>'.$row['title'].'</td>                    <td><a href="./editnotice.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./editnotice.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>                  </tr>';                  	 }                  	echo '                </tbody>              </table>            </div>		    <footer class="panel-footer">		      <div class="row">		        <div class="col-sm-12 text-right text-center-xs">                		          <ul class="pagination pagination-sm m-t-none m-b-none">		          	';		          		if($page != 0){		          			echo '<li><a href="./notice.php?page='.$page.'"><i class="fa fa-chevron-left"></i></a></li>';		          		}		          		$total = $this->database->num_rows("SELECT * FROM `ytidc_notice` WHERE `site`='{$this->subsite['id']}'");		          		$records = $total;		          		$total_pages = ceil($records / 10);		            	for($i = 1;$i <= $total_pages; $i++){		            		echo '<li><a href="./notice.php?page='.$i.'">'.$i.'</a></li>';		            	}		            	if($page+2 <= $total_pages){		            		$next_page = $page + 2;		            		echo '<li><a href="./notice.php?page='.$next_page.'"><i class="fa fa-chevron-right"></i></a></li>';		            	}		            		            echo '</ul>		        </div>		      </div>		    </footer>          </div>        </div>';
        echo $this->Foot();
    }
    
    public function Config(){
        
    }
    
    public function Head(){
        echo '<!DOCTYPE html> <html lang="en"> <head>   <meta charset="utf-8" />   <title>仪表盘 | 云塔IDC系统v3.0</title>   <meta name="description" content="app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />   <link rel="stylesheet" href="/assets/css/bootstrap.css" type="text/css" />   <link rel="stylesheet" href="/assets/css/animate.css" type="text/css" />   <link rel="stylesheet" href="/assets/css/font-awesome.min.css" type="text/css" />   <link rel="stylesheet" href="/assets/css/simple-line-icons.css" type="text/css" />   <link rel="stylesheet" href="/assets/css/font.css" type="text/css" />   <link rel="stylesheet" href="/assets/css/app.css" type="text/css" /> </head> <body>   <div class="app app-header-fixed" id="app">     <!-- navbar -->     <div class="app-header navbar">       <!-- navbar header -->       <div class="navbar-header bg-dark">         <button class="pull-right visible-xs dk" data-toggle="class:show" data-target=".navbar-collapse">           <i class="glyphicon glyphicon-cog"></i>         </button>         <button class="pull-right visible-xs" data-toggle="class:off-screen" data-target=".app-aside" ui-scroll="app">           <i class="glyphicon glyphicon-align-justify"></i>         </button>         <!-- brand -->         <a href="#/" class="navbar-brand text-lt">           <i class="fa fa-cloud"></i>           <img src="/assets/img/logo.png" alt="." class="hide">           <span class="hidden-folded m-l-xs">管理面板</span>         </a>         <!-- / brand -->       </div>       <!-- / navbar header -->        <!-- navbar collapse -->       <div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">         <!-- buttons -->         <div class="nav navbar-nav hidden-xs">           <a href="#" class="btn no-shadow navbar-btn" data-toggle="class:app-aside-folded" data-target=".app">             <i class="fa fa-dedent fa-fw text"></i>             <i class="fa fa-indent fa-fw text-active"></i>           </a>         </div>         <!-- / buttons -->          <!-- search form -->         <form class="navbar-form navbar-form-sm navbar-left shift" ui-shift="prependTo" data-target=".navbar-collapse" role="search" ng-controller="TypeaheadDemoCtrl">           <div class="form-group">             <div class="input-group">               <input type="text" ng-model="selected" typeahead="state for state in states | filter:$viewValue | limitTo:8" class="form-control input-sm bg-light no-border rounded padder" placeholder="快速搜索...">               <span class="input-group-btn">                 <button type="submit" class="btn btn-sm bg-light rounded"><i class="fa fa-search"></i></button>               </span>             </div>           </div>         </form>         <!-- / search form -->        </div>       <!-- / navbar collapse -->     </div>     <!-- / navbar -->      <!-- menu -->     <div class="app-aside hidden-xs bg-dark">       <div class="aside-wrap">         <div class="navi-wrap">           <!-- nav -->           <nav ui-nav class="navi">             <ul class="nav">               <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">                 <span translate="aside.nav.HEADER">导航</span>               </li>               <li ui-sref-active="active">                 <a ui-sref="app.calendar" href="/admin/index.php">                   <i class="glyphicon glyphicon-home icon text-primary-dker"></i>                   <span class="font-bold" translate="aside.nav.CALENDAR">仪表盘</span>                 </a>               </li>               <li ui-sref-active="active">                 <a ui-sref="app.calendar" href="/admin/notice.php">                   <i class="glyphicon glyphicon-list"></i>                   <span class="font-bold" translate="aside.nav.CALENDAR">公告设置</span>                 </a>               </li>               <li ui-sref-active="active">                 <a ui-sref="app.calendar" href="/admin/config.php">                   <i class="glyphicon glyphicon-briefcase"></i>                   <span class="font-bold" translate="aside.nav.CALENDAR">资料设置</span>                 </a>               </li>             </ul>           </nav>           <!-- nav -->            <!-- aside footer -->           <div class="wrapper m-t">             <div class="text-center-folded">               <span class="pull-right pull-none-folded">90%</span>               <span class="hidden-folded" translate="aside.MILESTONE">Pro</span>             </div>             <div class="progress progress-xxs m-t-sm dk">               <div class="progress-bar progress-bar-info" style="width: 90%;">               </div>             </div>             <div class="text-center-folded">               <span class="pull-right pull-none-folded">100%</span>               <span class="hidden-folded" translate="aside.RELEASE">Useful</span>             </div>             <div class="progress progress-xxs m-t-sm dk">               <div class="progress-bar progress-bar-primary" style="width: 100%;">               </div>             </div>           </div>           <!-- / aside footer -->         </div>       </div>     </div>     <!-- / menu -->      <!-- content -->     <div class="app-content">       <div ui-butterbar></div>       <a href class="off-screen-toggle hide" data-toggle="class:off-screen" data-target=".app-aside" ></a>       <div class="app-content-body fade-in-up">';
    }
    
    public function Foot(){
        echo '<!-- PASTE above -->      </div>    </div>    <!-- /content -->    <!-- footer -->    <div class="app-footer wrapper b-t bg-light">      <span class="pull-right">云塔版本v3.0    </div>    <!-- / footer -->  </div>  <!-- jQuery -->  <script src="/assets/vendor/jquery/jquery.min.js"></script>  <script src="/assets/vendor/jquery/bootstrap.js"></script>  <script type="text/javascript">    +function ($) {      $(function(){        // class        $(document).on(\'click\', \'[data-toggle^="class"]\', function(e){          e && e.preventDefault();          console.log(\'abc\');          var $this = $(e.target), $class , $target, $tmp, $classes, $targets;          !$this.data(\'toggle\') && ($this = $this.closest(\'[data-toggle^="class"]\'));          $class = $this.data()[\'toggle\'];          $target = $this.data(\'target\') || $this.attr(\'href\');          $class && ($tmp = $class.split(\':\')[1]) && ($classes = $tmp.split(\',\'));          $target && ($targets = $target.split(\',\'));          $classes && $classes.length && $.each($targets, function( index, value ) {            if ( $classes[index].indexOf( \'*\' ) !== -1 ) {              var patt = new RegExp( \'\\s\' +                   $classes[index].                    replace( /\*/g, \'[A-Za-z0-9-_]+\' ).                    split( \' \' ).                    join( \'\\s|\\s\' ) +                   \'\\s\', \'g\' );              $($this).each( function ( i, it ) {                var cn = \' \' + it.className + \' \';                while ( patt.test( cn ) ) {                  cn = cn.replace( patt, \' \' );                }                it.className = $.trim( cn );              });            }            ($targets[index] !=\'#\') && $($targets[index]).toggleClass($classes[index]) || $this.toggleClass($classes[index]);          });          $this.toggleClass(\'active\');        });        // collapse nav        $(document).on(\'click\', \'nav a\', function (e) {          var $this = $(e.target), $active;          $this.is(\'a\') || ($this = $this.closest(\'a\'));                    $active = $this.parent().siblings( ".active" );          $active && $active.toggleClass(\'active\').find(\'> ul:visible\').slideUp(200);                    ($this.parent().hasClass(\'active\') && $this.next().slideUp(200)) || $this.next().slideDown(200);          $this.parent().toggleClass(\'active\');                    $this.next().is(\'ul\') && e.preventDefault();          setTimeout(function(){ $(document).trigger(\'updateNav\'); }, 300);              });      });    }(jQuery);  </script></body></html>';
    }
    
}

?>