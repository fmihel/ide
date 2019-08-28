
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
    
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
};

require_once UNIT('plugins','base/base.php');
WS_CONF::LOAD(__DIR__,'../../../../../dev/ws_conf.php');
require_once UNIT('../../../../../dev/source/connect/connect.php');


$table = 'NEWS';
$data =     [
    'MSG'=>['dup qwehfkj kjq hefk'],
    'PERMISSION'=>['[1,2,3]'],
    'ID_NEWS'=>30,
];
$where = '1>0';
$param =     [
    'refactoring'=>false,
    'where'=>'ID_NEWS = 30'
];

$insert = base::dataToSQL('insert',$table,$data,$param);
$update = base::dataToSQL('update',$table,$data,$param);
$insertOnDuplicate = base::dataToSQL('insertOnDuplicate',$table,$data,$param);

echo '<xmp>';
echo $insert;
echo "\n-------------------------------------------------\n";
echo $update;
echo "\n-------------------------------------------------\n";
echo $insertOnDuplicate;
echo '</xmp>';

try{
   //base::queryE($insertOnDuplicate,'deco');
}catch(Exception $e){
    echo $e->getMessage();
}

?>