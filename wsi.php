<?php

header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

define('WSI_DEV_VERSION','030719_1');
define('WSI_GIT_VERSION','1.1.1');

error_reporting(0);
/*
https://ws-framework-fmihel.c9users.io/ide/wsi.php
*/

require_once 'ws/utils/application.php';
$Application->LOG_ENABLE = true;
$Application->LOG_TO_ERROR_LOG = false;
//_LOG('$Application->LOG_ENABLE = true;',__FILE__,__LINE__);
RESOURCE('style/dcss.dcss');
RESOURCE('style/icons.dcss');
RESOURCE('style/style.dcss');
RESOURCE('style/mselect.dcss');

require_once UNIT('utils','dir.php');
require_once UNIT('utils','ctime.php');

require_once UNIT('ws','ws.php');
//require_once UNIT('style/dcss.php');

require_once UNIT('modules/users.php');
require_once UNIT('modules/autorize/autorize.php');

require_once UNIT('modules/layout.php');
require_once UNIT('modules/left_panel/left_panel.php');

require_once UNIT('modules/left_panel/filetree.php');
require_once UNIT('modules/editors.php');
require_once UNIT('modules/menu/menu.php');
require_once UNIT('modules/menu/urls.php');


require_once UNIT('modules/right_panel/right_panel.php');
require_once UNIT('modules/right_panel/struct/struct.php');
require_once UNIT('modules/right_panel/com/com.php');
require_once UNIT('modules/right_panel/template/template.php');

require_once UNIT('modules/bottom_panel.php');
require_once UNIT('modules/bottom_panel/log_ex/log_ex.php');
//require_once UNIT('modules/bottom_panel/log/log.php');
require_once UNIT('modules/bottom_panel/find/find.php');

require_once UNIT('modules/options/options.php');
require_once UNIT('modules/code_complit/code_complit.php');
require_once UNIT('modules/progress/progress.php');
require_once UNIT('modules/update/update.php');


RESOURCE('plugins','common/ut.js');
RESOURCE('plugins','common/local_storage.js');
RESOURCE('plugins','common/jlock.js');

RESOURCE("style/ui_dark/jquery-ui.js");
RESOURCE("[color:light]style/ui_light/jquery-ui.css");
RESOURCE("[color:dark]style/ui_dark/jquery-ui.css");

RESOURCE('plugins','form/form.js');
RESOURCE('style/form.dcss');

//RESOURCE('style/url_lighter1.dcss');
//RESOURCE('style/url_lighter2.dcss');
RESOURCE('style/url_lighter/define.dcss');
RESOURCE('style/url_lighter/tab.dcss');
RESOURCE('style/url_lighter/top.dcss');
RESOURCE('style/url_lighter/list.dcss');

RESOURCE('plugins','url_lighter/url_lighter.js');

WS_CONF::SET('bildFrameJS',0);

class WSI extends WS{
        
  public function ONLOAD(){
      
      global $REQUEST;
      global $USERS;

      
      if ($REQUEST->IsAjax()){
            
        if ($USERS->autorize_from_share()){
            
            MODULES::ENABLE(array('ENABLE'=>true));
            MODULES::ENABLE(array('MODULE'=>'AUTORIZE','ENABLE'=>false));
        
            
        }else{
            
            MODULES::ENABLE(array('ENABLE'=>false));
            MODULES::ENABLE(array('MODULE'=>'AUTORIZE','ENABLE'=>true));
        }
    
          
      }else{
        
        if ($REQUEST->MODULE=='check_autorize'){
          
            if ($USERS->autorize($REQUEST->VALUE['login'],$REQUEST->VALUE['password'])){

                MODULES::ENABLE(array('ENABLE'=>true));
                MODULES::ENABLE(array('MODULE'=>'AUTORIZE','ENABLE'=>false));
                
                $REQUEST->SHARE['session']  = $USERS->get('session');
                $REQUEST->SHARE['user_data']= $USERS->current_to_json();

            }else{
                
                MODULES::ENABLE(array('ENABLE'=>false));
                MODULES::ENABLE(array('MODULE'=>'AUTORIZE','ENABLE'=>true));
                
            }
            
        }else{
            
            MODULES::ENABLE(array('ENABLE'=>false));
            MODULES::ENABLE(array('MODULE'=>'AUTORIZE','ENABLE'=>true));
            
        };
        
        $styles = array('color'=>'dark','size'=>'normal');
        if ($USERS->is_autorize()){ 
            if ($USERS->get('theme')=='dark')
                $styles=array('color'=>'dark','size'=>'compact');
            else    
                $styles=array('color'=>'light','size'=>'normal');
        }        
        

        DCSS::STYLES($styles);

          
      };  
      
      return true;
  }
  
   public function CONTENT(){
        // remove user_data from "share", because not need pass every time on send "share"
        FRAME()->READY('
        
            Dcss.onLoad({func:function(){
                
                if (typeof(editors)!="undefined"){
                    if (Dcss.styles.color == "light")
                        editors.theme("light");
                    else
                        editors.theme("dark");
                    $(".ui-widget-header").css("border","0px");    
                    $(".ui-dialog-content").css("overflow","hidden");

                }    
                
            }});
            
            Qs.body.fadeOut(0);
            
            if (Ws.share.user_data){
                Ws.user_data = $.parseJSON(Ws.share.user_data);
                delete Ws.share.user_data;
            };
            
            
            Qs.body.fadeIn(1000,function(){
                Dcss.onLoad();
                Ws.align();
            
            });

        ')
        ->SCRIPT('
        
        function setDcssTheme(name){
            if (name=="light")
                Dcss.load({styles:{color:"light",size:"normal"}});
            else    
                Dcss.load({styles:{color:"dark",size:"compact"}});
        }
        
        ');
        
/*
            Qs.body.fadeOut(0);
            Dcss.load({styles:{color:"light",size:"normal"},done:function(){
                Qs.body.fadeIn(100);
                Ws.align();
            }});
*/

  }
  
  public function AJAX(&$response){

      return false;
  }
 
}      
 
if($Application->is_main(__FILE__)){

         
  $app = new WSI();
  $app->loadOptimizedResources = false;
  //$app->version='';
  //$app->version='nocache';
  $app->version=WSI_DEV_VERSION;
  $app->RUN();
  //echo $Application->debug_info();
  
}
?>
