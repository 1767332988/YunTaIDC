<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>仪表盘 | 云塔IDC系统v2.4</title>
  <meta name="description" content="app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <link rel="stylesheet" href="/assets/css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="/assets/css/animate.css" type="text/css" />
  <link rel="stylesheet" href="/assets/css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="/assets/css/simple-line-icons.css" type="text/css" />
  <link rel="stylesheet" href="/assets/css/font.css" type="text/css" />
  <link rel="stylesheet" href="/assets/css/app.css" type="text/css" />
</head>
<body>
  <div class="app app-header-fixed" id="app">
    <!-- navbar -->
    <div class="app-header navbar">
      <!-- navbar header -->
      <div class="navbar-header bg-dark">
        <button class="pull-right visible-xs dk" data-toggle="class:show" data-target=".navbar-collapse">
          <i class="glyphicon glyphicon-cog"></i>
        </button>
        <button class="pull-right visible-xs" data-toggle="class:off-screen" data-target=".app-aside" ui-scroll="app">
          <i class="glyphicon glyphicon-align-justify"></i>
        </button>
        <!-- brand -->
        <a href="#/" class="navbar-brand text-lt">
          <i class="fa fa-cloud"></i>
          <img src="/assets/img/logo.png" alt="." class="hide">
          <span class="hidden-folded m-l-xs">云塔v2.4</span>
        </a>
        <!-- / brand -->
      </div>
      <!-- / navbar header -->

      <!-- navbar collapse -->
      <div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">
        <!-- buttons -->
        <div class="nav navbar-nav hidden-xs">
          <a href="#" class="btn no-shadow navbar-btn" data-toggle="class:app-aside-folded" data-target=".app">
            <i class="fa fa-dedent fa-fw text"></i>
            <i class="fa fa-indent fa-fw text-active"></i>
          </a>
        </div>
        <!-- / buttons -->

        <!-- search form -->
        <form class="navbar-form navbar-form-sm navbar-left shift" ui-shift="prependTo" data-target=".navbar-collapse" role="search" ng-controller="TypeaheadDemoCtrl">
          <div class="form-group">
            <div class="input-group">
              <input type="text" ng-model="selected" typeahead="state for state in states | filter:$viewValue | limitTo:8" class="form-control input-sm bg-light no-border rounded padder" placeholder="快速搜索...">
              <span class="input-group-btn">
                <button type="submit" class="btn btn-sm bg-light rounded"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
        </form>
        <!-- / search form -->

      </div>
      <!-- / navbar collapse -->
    </div>
    <!-- / navbar -->

    <!-- menu -->
    <div class="app-aside hidden-xs bg-dark">
      <div class="aside-wrap">
        <div class="navi-wrap">
          <!-- nav -->
          <nav ui-nav class="navi">
            <ul class="nav">
              <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                <span translate="aside.nav.HEADER">导航</span>
              </li>
              <li ui-sref-active="active">
                <a ui-sref="app.calendar" href="/sysadmin/index.php">
                  <i class="glyphicon glyphicon-home icon text-primary-dker"></i>
                  <span class="font-bold" translate="aside.nav.CALENDAR">仪表盘</span>
                </a>
              </li>
              <li>
                <a href class="auto">      
                  <span class="pull-right text-muted">
                    <i class="fa fa-fw fa-angle-right text"></i>
                    <i class="fa fa-fw fa-angle-down text-active"></i>
                  </span>
                  <i class="glyphicon glyphicon-th"></i>
                  <span>产品服务</span>
                </a>
                <ul class="nav nav-sub dk">
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/type.php">
                      <span>产品组管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/product.php">
                      <span>产品管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/server.php">
                      <span>服务器管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/service.php">
                      <span>在线服务器管理</span>
                    </a>
                  </li><li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/worder.php">
                      <span>服务工单管理</span>
                    </a>
                  </li>
                </ul>
              </li><li>
                <a href class="auto">      
                  <span class="pull-right text-muted">
                    <i class="fa fa-fw fa-angle-right text"></i>
                    <i class="fa fa-fw fa-angle-down text-active"></i>
                  </span>
                  <i class="glyphicon glyphicon-user"></i>
                  <span>用户代理</span>
                </a>
                <ul class="nav nav-sub dk">
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/user.php">
                      <span>用户管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/price.php">
                      <span>价格组管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/site.php">
                      <span>分站管理</span>
                    </a>
                  </li>
                </ul>
              </li>
              <li>
                <a href class="auto">      
                  <span class="pull-right text-muted">
                    <i class="fa fa-fw fa-angle-right text"></i>
                    <i class="fa fa-fw fa-angle-down text-active"></i>
                  </span>
                  <i class="fa fa-cny"></i>
                  <span>支付交易</span>
                </a>
                <ul class="nav nav-sub dk">
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/pay.php">
                      <span>支付管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/code.php">
                      <span>优惠码管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/order.php">
                      <span>交易记录</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/invite.php">
                      <span>邀请记录</span>
                    </a>
                  </li>
                </ul>
              </li>
              <li>
                <a ui-sref="app.calendar" href="/sysadmin/admin.php">
                  <span class="pull-right text-muted">
                    <i class="fa fa-fw fa-angle-right text"></i>
                    <i class="fa fa-fw fa-angle-down text-active"></i>
                  </span>
                  <i class="glyphicon glyphicon-edit"></i>
                  <span>协同操作管理</span>
                </a>
              </li>
              <li>
                <a href class="auto">      
                  <span class="pull-right text-muted">
                    <i class="fa fa-fw fa-angle-right text"></i>
                    <i class="fa fa-fw fa-angle-down text-active"></i>
                  </span>
                  <i class="glyphicon glyphicon-briefcase"></i>
                  <span>网站管理</span>
                </a>
                <ul class="nav nav-sub dk">
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/setting.php?type=config">
                      <span>资料管理</span>
                    </a>
                  </li>
                  </li><li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/notice.php">
                      <span>公告管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/setting.php?type=smtp">
                      <span>SMTP邮件管理</span>
                    </a>
                  </li>
                  <li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/setting.php?type=template">
                      <span>模板管理</span>
                    </a>
                  </li><li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/setting.php?type=cloud">
                      <span>云中心管理</span>
                    </a>
                  </li><li ui-sref-active="active">
                    <a ui-sref="layout.app" href="/sysadmin/update.php">
                      <span>系统更新</span>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </nav>
          <!-- nav -->

          <!-- aside footer -->
          <div class="wrapper m-t">
            <div class="text-center-folded">
              <span class="pull-right pull-none-folded">90%</span>
              <span class="hidden-folded" translate="aside.MILESTONE">Pro</span>
            </div>
            <div class="progress progress-xxs m-t-sm dk">
              <div class="progress-bar progress-bar-info" style="width: 90%;">
              </div>
            </div>
            <div class="text-center-folded">
              <span class="pull-right pull-none-folded">100%</span>
              <span class="hidden-folded" translate="aside.RELEASE">Useful</span>
            </div>
            <div class="progress progress-xxs m-t-sm dk">
              <div class="progress-bar progress-bar-primary" style="width: 100%;">
              </div>
            </div>
          </div>
          <!-- / aside footer -->
        </div>
      </div>
    </div>
    <!-- / menu -->

    <!-- content -->
    <div class="app-content">
      <div ui-butterbar></div>
      <a href class="off-screen-toggle hide" data-toggle="class:off-screen" data-target=".app-aside" ></a>
      <div class="app-content-body fade-in-up">