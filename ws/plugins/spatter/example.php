<?php
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
};
RESOURCE('http://windeco.su/wsi/ide/style/ui_light/jquery-ui.min.js');
RESOURCE('http://windeco.su/wsi/ide/style/ui_light/jquery-ui.css');
require_once UNIT('utils','framet.php');

RESOURCE('plugins','spatter/spatter.js');

class TWS extends WS{
    
    public function CONTENT(){
        $own = FRAME()
        ->CSS('
        body{
            overflow:hidden;
        }
        tr{
            border:1px solid silver;
            height:32px;
        }
        td{
            border:1px solid silver;
        }
        
        ');
        
        FRAMET('
            %area = "width:150px;height:150px";
            %area2 = "width:20px;height:20px;border:1px solid silver";
            %area3 = "border:1px solid silver";
            %abs = "position:static";
            %col = "position:static";
            
            <start {width:150px;height:32px;border:1px solid gray}|click|>
            <from {border:1px solid green;left:100px;top:100px;%area} |from|
                <item1 {%area2;left:10px;top:20px}>
                <item2 {%area2;left:50px;top:20px}>
                <item3 {%area2;left:80px;top:20px}>
                <img:img [src="https://www.google.ru/images/branding/googlelogo/2x/googlelogo_color_120x44dp.png"]{%area3}>
            >
            <to {border:1px solid black;left:300px;top:100px;%area} |to|>
            <t:table {width:400px;height:400px;left:50px;top:500px;border:1px solid}
                <t1:tr {position:static}
                    <td1:td|click| {%col}>
                    <td2:td|col2| {position:static}>
                >
                <t2:tr {position:static}
                    <td3:td|col3| {position:static}>
                    <td4:td|col4| {position:static}>
                >
            >
        
        ',$own)
        ->READY('
            Qs.from.draggable();
            Qs.from.resizable();
            Qs.to.draggable();
            Qs.to.resizable();
            
            Qs.t1.on("click",function(){
                spatter({from:Qs.t1,to:Qs.to,css:{background:"#CEDDF3"}});    
            });
        
        ')
        ->SCRIPT('

        ')
        ->EVENT('click','
            spatter({from:Qs.from,to:Qs.to,opacity:1});
        ');

    }
  
}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS();
    // ----------------------------------------------------------
    //  $app->version = ''; - custom clear cache 
    //  $app->version = 'nocache'; no caching data always
    //  $app->verison = 'XXXXX'; caching data (version control)
    $app->version = ''; 
    
    // ----------------------------------------------------------
    //  $app->title - browser tab name
    $app->title = 'spatter';

    // ----------------------------------------------------------
    //  $app->tabColor - in mobile Chrome tab color
    $app->tabColor = '#DCDCDC';
    
    $app->RUN();

}
?>
    