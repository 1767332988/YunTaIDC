<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('product_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./product.php");
  	exit;
}
$act = daddslashes($_GET['act']);
if($act == "del"){
	if(!in_array('*', $permission) && !in_array('product_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
	if($DB->query("SELECT * FROM `ytidc_service` WHERE `product`='{$id}'")->num_rows >= 1){
		@header("Location: ./msg.php?msg=该产品尚有在线服务，暂时不允许删除");
		exit;
	}else{
  		$DB->query("DELETE FROM `ytidc_product` WHERE `id`='{$id}'");
  		@header("Location: ./product.php");
  		exit;
	}
}
if($act == "edit"){
  	foreach($_POST as $k => $v){
      	$value = daddslashes($v);
      	$DB->query("UPDATE `ytidc_product` SET `{$k}`='{$value}' WHERE `id`='{$id}'");
    }
    $time = daddslashes($_POST['period']);
	foreach($time as $k => $v){
		if(!empty($v['name'])){
			$timearray[$k] = $v;
		}
 	}
	$time = json_encode(url_encode($timearray));
	$DB->query("UPDATE `ytidc_product` SET `period`='{$time}' WHERE `id`='{$id}'");
  	$configoption = json_encode(daddslashes($_POST['configoption']));
  	$DB->query("UPDATE `ytidc_product` SET `configoption`='{$configoption}' WHERE `id`='{$id}'");
  	@header("Location: ./editproduct.php?id={$id}");
  	exit;
}
$row = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'")->fetch_assoc();
$row['configoption'] = json_decode($row['configoption'], 1);
$time = json_decode(url_decode($row['period']), true);
$timecount = count($time);
$type = $DB->query("SELECT * FROM `ytidc_type` WHERE `status`='1'");
$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `status`='1'");
if(!empty($row['server'])){
	$serverinfo = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$row['server']}'")->fetch_assoc();
}
$descriptionhtml = file_get_contents("../templates/".$conf['template']."/user_buy_product_modal.template");
if(!empty($serverinfo['plugin'])){
	$plugin = "../plugins/server/".$serverinfo['plugin']."/main.php";
	if(!file_exists($plugin) || !is_file($plugin)){
		@header("Location: ./msg.php?msg=服务器插件不存在");
		exit;
	}
	include($plugin);
}
include("./head.php");
?>
		<script>
        var count = <?=$timecount?>;

        //用来判断是删除 还是增加按钮 以便count值进行计算
        function checkCount(boolOK, coun) {
            if (boolOK == true) {
                return count++;
            }
            else {
                count--;
            }
        }
        function AddTimeInput() {
            // checkCount(2, true);
            countAA = checkCount(true, count);
            // alert(countAA);
            //count++;
            var time = document.getElementById("timetable");

            var tr = document.createElement('tr');
    	    	var td = document.createElement('td');
    	    	td.innerHTML='<input type="text" class="form-control" name="period[' + count + '][name]" value="" style="min-width: 100px;"/>';
    			tr.appendChild(td);
    	    	var td = document.createElement('td');
    	    	td.innerHTML='<input type="text" class="form-control" name="period[' + count + '][price]" value="" style="min-width: 100px;"/>';
    			tr.appendChild(td);
    	    	var td = document.createElement('td');
    	    	td.innerHTML='<input type="text" class="form-control" name="period[' + count + '][day]" value="" style="min-width: 100px;"/>';
    			tr.appendChild(td);
    	    	var td = document.createElement('td');
    	    	td.innerHTML='<input type="text" class="form-control" name="period[' + count + '][remark]" value="" style="min-width: 100px;"/>';
    			tr.appendChild(td);
    	    	var td = document.createElement('td');
    	    	td.innerHTML='<select type="select" class="form-control" name="period[' + count + '][renew]" style="min-width: 100px;"><option value="1">允许</option><option value="0">不允许</option></select>';
    			tr.appendChild(td);
    		time.appendChild(tr);

        }
</script>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">编辑产品</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑产品</div>
        <div class="panel-body">
          <form role="form" action="./editproduct.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>产品名称</label>
              <input type="text" name="name" class="form-control" placeholder="产品名称" value="<?=$row['name']?>">
            </div>
            <div class="form-group">
              <label>产品介绍</label>
              <textarea class="form-control" name="description"><?=$row['description']?></textarea>
            </div>
            <div class="form-group">
              <label>模板推荐产品介绍</label>
              <textarea class="form-control" disabled=""><?=$descriptionhtml?></textarea>
            </div>
            <div class="form-group">
              <label>产品分类</label>
              <select name="type" class="form-control m-b">
              	<?php
              	while($row2 = $type->fetch_assoc()){
              		if($row2['id'] == $row['type']){
              			echo '<option value="'.$row2['id'].'" selected>'.$row2['name'].'</option>';
              		}else{
              			echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
              		}
              	}
              	?>
              </select>
            </div>
            <div class="form-group">
              <label>产品服务器</label>
              <select name="server" class="form-control m-b">
              	<?php
              	while($row2 = $server->fetch_assoc()){
              		if($row2['id'] == $row['server']){
              			echo '<option value="'.$row2['id'].'" selected>'.$row2['name'].'</option>';
              		}else{
              			echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
              		}
              	}
              	?>
              </select>
            </div>
            <div class="form-group">
              <label>产品权重（越大越前）</label>
              <input type="number" name="weight" class="form-control" placeholder="产品权重" value="<?=$row['weight']?>">
            </div>
            <div class="form-group">
              <label>限制购买（0为不限制）</label>
              <input type="number" name="limit" class="form-control" placeholder="限制购买" value="<?=$row['limit']?>">
            </div>
            <div class="form-group">
              <label>隐藏产品</label>
              <select name="hidden" class="form-control m-b">
            	<?php
            		if($row['hidden'] == 1){
            			echo '<option value="0">否</option><option value="1" selected>是</option>';
            		}else{
            			echo '<option value="0" selected>否</option><option value="1">是</option>';
            		}
            	?>
              </select>
            </div>
            <div class="form-group">
              <label>产品周期【留空名称为删除】<button class="btn btn-small btn-xs btn-primary" onclick="AddTimeInput()" type="button">新增周期</button></label>
	            <div class="table-responsive">
	              <table class="table table-striped b-t b-light">
	                <thead>
	                  <tr>
	                    <th style="min-width: 100px;">周期名称</th>
	                    <th style="min-width: 100px;">周期价格</th>
	                    <th style="min-width: 100px;">开通天数</th>
	                    <th style="min-width: 100px;">周期备注</th>
	                    <th style="min-width: 100px;">允许续费</th>
	                  </tr>
	                </thead>
	                <tbody id="timetable">
                    <?php
                                        	foreach($time as $k => $v){
                                            	echo '<tr>
                                                <td><input type="text" class="form-control" placeholder="周期名称" name="period['.$k.'][name]" value="'.$v['name'].'" style="min-width: 100px;"></td>
                                                <td><input type="text" class="form-control" placeholder="周期价格" name="period['.$k.'][price]" value="'.$v['price'].'" style="min-width: 100px;"></td>
                                                <td><input type="text" class="form-control" placeholder="开通日数" name="period['.$k.'][day]" value="'.$v['day'].'" style="min-width: 100px;"></td>
                                                <td><input type="text" class="form-control" placeholder="周期介绍" name="period['.$k.'][remark]" value="'.$v['remark'].'" style="min-width: 100px;"></td>';
                                                if($v['renew'] == 1){
                                                	echo '<td><select type="select" class="form-control" name="period['.$k.'][renew]" style="min-width: 100px;"><option value="1" selected>允许</option><option value="0">不允许</option></select></td>';
                                                }else{
                                                	echo '<td><select type="select" class="form-control" name="period['.$k.'][renew]" style="min-width: 100px;"><option value="1" >允许</option><option value="0" selected>不允许</option></select></td>';
                                                }
                                            	echo '</tr>';
                                            }
                                        	?>
	                </tbody>
	              </table>
	            </div>
            </div>
            <?php
            if(!empty($serverinfo['plugin'])){
                                        if(function_exists($serverinfo['plugin']."_ConfigOption")){
                                        	$function = $serverinfo['plugin']."_ConfigOption";
                                        	$configoption = $function($serverinfo);
                                        	foreach($configoption as $k => $v){
                                        		if($v['type'] == "text"){
                                        			echo '<div class="form-group">
                                            <label>【插件配置】：'.$v['label'].'</label>
                                            <input type="text" class="form-control" name="configoption['.$k.']" placeholder="'.$v['placeholder'].'" maxlength="256" value="'.$row['configoption'][$k].'">
                                        </div>';
                                        		}
                                        		if($v['type'] == "textarea"){
                                        			echo '<div class="form-group">
                                            <label>【插件配置】：'.$v['label'].'</label>
                                            <textarea class="form-control" name="configoption['.$k.']" placeholder="'.$v['placeholder'].'">'.$row['configoption'][$k].'</textarea>
                                        </div>';
                                        		}
                                        		if($v['type'] == "number"){
                                        			echo '<div class="form-group">
                                            <label>【插件配置】：'.$v['label'].'</label>
                                            <input type="number" class="form-control" name="configoption['.$k.']" placeholder="'.$v['placeholder'].'" maxlength="256" value="'.$row['configoption'][$k].'">
                                        </div>';
                                        		}
                                        		if($v['type'] == "select"){
                                        			echo '<div class="form-group">
                                            		<label>【插件配置】：'.$v['label'].'</label>
                                        			<select name="configoption['.$k.']" class="form-control">';
                                        			foreach($v['option'] as $k1 => $v1){
                                        				if($row['configoption'][$k] == $v1){
                                        					echo '<option value="'.$v1.'" selected>'.$k1.'</option>';
                                        				}else{
                                        					echo '<option value="'.$v1.'">'.$k1.'</option>';
                                        				}
                                        			}
                                        			echo '</select></div>';
                                        		}
                                        	}
                                        }
            						}else{
            							echo '<div class="form-group">
                                            <label>插件配置：请先在服务器设置里面设置插件！</label>
                                        </div>';
            						}
            ?>
            <button type="submit" class="btn btn-sm btn-primary">提交</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php

include("./foot.php");

?>