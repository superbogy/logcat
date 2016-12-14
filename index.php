<?php
if(empty($_GET) && empty($_POST) && php_sapi_name()!='cli'){
    echo file_get_contents('view/index.html');
    flush();
}

set_time_limit(0);
require "lib/config.php"; //$sysConfig
$config = json_decode(file_get_contents('config.json'),true);

include "lib/Log.php";
if(isset($_GET['p']) && isset($config['plugin'][$_GET['p'])){
    $p = $_GET['p'];
    include $config['plugin'][$p];
    $class = unfirst($p)
    $log = new $class;
}else{
    $log = new Log;
}

if(isset($_POST['collector_id']) && isset($_POST['log']) && isset($config['collector_log_file'][$_POST['collector_id']])){
    $log->writeLog($config['dataDir'].'/'.$config['collector_log_file'][$_POST['collector_id']],$_POST['log']);
    exit;
}

$config = array_merge($sysConfig,$config);
date_default_timezone_set($config['timezone']);
$log->makeIndex($config);

isset($_GET['sum']) && $log->sum = $_GET['sum'];
isset($_GET['count']) && $_GET['count']!='false' && $log->count = $_GET['count'];
isset($_GET['table']) && $log->table = $_GET['table'];
isset($_GET['period']) && $log->period = $_GET['period'];
isset($_GET['stime']) && $log->stime = $_GET['stime'];
isset($_GET['etime']) && $log->etime = $_GET['etime'];
isset($_GET['datetimerange']) && $log->datetimerange = $_GET['datetimerange'];
isset($_GET['group']) && $log->group = $_GET['group'];
if(isset($_GET['where_f']) && isset($_GET['where_v'])){
    $log->where = array_combine($_GET['where_f'], $_GET['where_v']);
}

if(isset($_GET['getConfig'])){
    echo json_encode($config);
}elseif($_GET){
    $data = $log->get();
    echo json_encode($data);
}