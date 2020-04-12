<?php

include("../includes/common.php");
if(!empty($_GET['type'])){
  	$type = daddslashes($_GET['type']);
  	$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `type`='{$type}' AND `hidden`='0' ORDER BY `weight` DESC");
}else{
  	$type = $DB->query("SELECT * FROM `ytidc_type` ORDER BY `weight` DESC")->fetch_assoc();
  	$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `type`='{$type['id']}' AND `hidden`='0' ORDER BY `weight` DESC");
}
$template = file_get_contents("../templates/".$template_name."/user_buy.template");
$type = $DB->query("SELECT * FROM `ytidc_type` WHERE `status`='1' AND `father`='0' ORDER BY `weight` DESC");
$type_template = find_list_html("分类列表", $template);
$subtype_template = find_list_html("二级分类列表", $type_template[1][0]);
while($row = $type->fetch_assoc()){
	$subtype_template_new = "";
	$subtype = $DB->query("SELECT * FROM `ytidc_type` WHERE `status`='1' AND `father`='{$row['id']}' ORDER BY `weight` DESC");
	while($row2 = $subtype->fetch_assoc()){
		$subtype_template_code = array(
			'name' => $row2['name'],
			'id' => $row2['id'],
		);
		$subtype_template_new = $subtype_template_new . template_code_replace($subtype_template[1][0], $subtype_template_code);
	}
	$type_template_cache = str_replace($subtype_template[0][0], $subtype_template_new, $type_template[1][0]);
	$type_template_code = array(
		'name' => $row['name'],
		'id' => $row['id'],
	);
	$type_template_new = $type_template_new . template_code_replace($type_template_cache, $type_template_code);
}
$template = str_replace($type_template[0][0], $type_template_new, $template);
$product_template = find_list_html("产品列表", $template);
while($row = $product->fetch_assoc()){
	$period = json_decode(url_decode($row['period']), true);
	$product_template_code = array(
		'name' => $row['name'],
		'id' => $row['id'],
		'price' => $period[1]['price'],
		'period' => $period[1]['name'],
		'description' => $row['description'],
	);
	$product_template_new = $product_template_new . template_code_replace($product_template[1][0], $product_template_code);
}
$template = str_replace($product_template[0][0], $product_template_new, $template);
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'user' => $user,
);
echo set_template($template, $template_name, $template_code);

?>