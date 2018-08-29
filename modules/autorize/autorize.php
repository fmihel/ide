<?php

MODULES::ADD('AUTORIZE');

class AUTORIZE extends WS_MODULE{
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/autorize/autorize.dcss') ;
        
    }
    public function CONTENT(){
        $wp = FRAME("workplace",FRAME())
            ->ALIGN('JX.stretch_scr({$})')
            ->STYLE('position:absolute;')
            ->CLASSES('workplace');
        $width=220;
        
        $form = FRAME("form",$wp)
            ->ALIGN('JX.arrange([{$}],{direct:"horiz",type:"center",align:"top",margin:{top:100}})')
            ->STYLE('display:none;position:absolute;')
            ->CLASSES('form')
        ->READY('{$}.hide().fadeIn(200);');
        
        $width=$width -45;
        $text = FRAME("text",$form)
            ->STYLE('position:absolute;left:20px;top:20px;color:gray')
            ->VALUE('Autorize');
        
        $login = FRAME("login",$form)
            ->TAG_NAME('input')
            ->ATTR('autocomplete',"new-password")
            ->ATTR('placeholder','login')
            ->READY('
                {$}.focus();
                {$}.on("keydown",function(e){
                    if ( e.which == 13 ) 
                        Qs.enter.trigger("click");
                });
            ')  
            ->STYLE('position:absolute;left:20px;top:50px;width:'.$width.'px');

        $password = FRAME("password",$form)
            ->TAG_NAME('input')
            ->ATTR('type','password')
            ->ATTR('autocomplete',"new-password")
            ->ATTR('placeholder','password')
            ->STYLE('position:absolute;left:20px;top:80px;width:'.$width.'px')
            ->READY('
                {$}.on("keydown",function(e){
                    if ( e.which == 13 ) 
                        Qs.enter.trigger("click");
                });
            ')  ;
        
        $enter = FRAME("enter",$form)
            ->STYLE('position:absolute')
            ->CLASSES('enter')
            ->VALUE('enter')
            ->READY('{$}.on("click",function(){
                delete Ws.share["session"];
                {AJAX}({id:"autorize",
                    value:{
                        login:Qs.login.val(),
                        password:Qs.password.val(),
                    },
                    done:function(data){
                        if (data.res==1)
                        Qs.form.hide("fade",{},100,function(){
                            {NAVIGATE}({
                                module:"check_autorize",
                                value:{
                                    login:Qs.login.val(),
                                    password:Qs.password.val(),
                                }
                            });
                        });
                    }})
                
            })');
    }
    public function AJAX(&$response){
        global $REQUEST;
        global $USERS;
        
        if ($REQUEST->ID=='autorize'){

            $login =  $REQUEST->VALUE['login'];
            $pass =   $REQUEST->VALUE['password'];
          
            if ($USERS->can_autorize($login,$pass))
                $response = array('res'=>1);
            else
                $response = array('res'=>0);
            return true;    
        }
        return false;
        
    }
};

?>