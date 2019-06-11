<?php
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
};


RESOURCE('test.dcss');
//RESOURCE('jedit.dcss');

class TWS extends WS{
    
    public function CONTENT(){
        $own = FRAME();
        
        
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
    $app->title = 'test';

    // ----------------------------------------------------------
    //  $app->tabColor - in mobile Chrome tab color
    $app->tabColor = '#DCDCDC';
    
    $app->RUN();

}



//echo '<xmp>'.$text.'</xmp>';

?>
    