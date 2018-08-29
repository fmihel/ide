<?php

MODULES::ADD('OPTIONS');
class OPTIONS extends WS_MODULE{
    private $tab_id;
    private $load;
    private $save;
    
    function __construct(){
        RESOURCE('modules/options/options.css');
        $this->tab_id=0;
        $this->load='';
        $this->save='';
    }
    public function tab($caption,$own){
        $this->tab_id++;
        FRAME('opt_cap_'.$this->tab_id,$own)
        ->TAG_NAME('h3')
        ->VALUE($caption);            
        
        return FRAME('opt_'.$this->tab_id,$own)->CLASSES('opt');            
    }
    public function CONTENT(){
        global $USERS;
        $options = FRAME("options",FRAME('modal'))
        ->SCRIPT('
        
        function options(){
            
            {$}.parent().jshadow("show");
            load_options();
            {$}.dialog( "option", "position", { my: "center top", at: "center top+50", of: Qs.page } );
            {$}.dialog( "open");
            Qs.opt_frame.accordion("refresh");
            
        }
        
        ')
        ->ATTR('title','Options')
        ->READY('
        
        {$}.need_restart = false;
        
        {$}.dialog({
            width: 400,
            height:500,
            resizable: true,
            autoOpen: false,
            beforeClose:function(){
                {$}.parent().jshadow("hide");  
            },
            resize: function() {
                Qs.opt_frame.accordion( "refresh" );
            },
            buttons: {
                "Ok": function() {
                    $( this ).dialog( "close" );
                    var opt = [];
                    save_options();
                    save_user_data(
                        function(){
                            if ({$}.need_restart){
                                popup({msg:"save ok. restart..",delay:10000});
                                setTimeout(function(){ location.reload(true);},3000);
                            }    
                        },
                        function(){popup({msg:"error save user data",type:"alert"});}
                    );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        
        {$}.parent().css("z-index",1000).jshadow({
            opacity:0.2,click:function(){{$}.dialog("close");}
        }).prev().css("z-index",1000);
        
        
        ')
        ->CLASSES('options');
        /*-------------------------------------------------------------------*/
        $frame = FRAME("opt_frame",$options)
        ->CLASSES('opt_frame')
        ->READY('{$}.accordion({
            heightStyle: "fill"
        });');
        /*-------------------------------------------------------------------*/
        FRAME()->SCRIPT('var OPTIONS={}');
        $tab=$this->tab('Interface',$frame)
        ->VALUE($this->combo('theme','Theme',array('dark','light'),'
            save_options("theme");
            save_user_data();
            setDcssTheme($(this).val());
            
        '));
        /*-------------------------------------------------------------------*/
        $tab=$this->tab('User data',$frame)
        ->VALUE(
            $this->edit('login','Login',$USERS->get('login')).
            $this->edit('password','Pass',$USERS->get('password'),'password')
        );
        /*-------------------------------------------------------------------*/
        FRAME()
            ->SCRIPT('function load_options(){'.$this->load.'};')
            ->SCRIPT('function save_options(){var name=arguments.length==0?"all":arguments[0]; '.$this->save.'};')
            ->SCRIPT('function save_user_data(done,error){
                Ws.ajax({id:"save_user_data",value:{user_data:Ws.user_data},
                error:function(){
                    if (error) error();    
                },
                done:function(data){
                if (!data.res){
                    if (error)error();
                }else
                    if (done)done();
                        
                }});                
            }');
    }
    
    private function edit($id,$caption,$value='',$type='text'){
        FRAME()->READY('OPTIONS.'.$id.'=$("#opt_'.$id.'");');
        
        $this->load.="OPTIONS.$id.val(Ws.user_data.$id);";
        $this->save.="if((name=='all')||(name=='".$id."')) Ws.user_data.$id=OPTIONS.$id.val();";
        
        return '<label><div class="labeled">'.$caption.'</div><input class="labeled_edit" id="opt_'.$id.'" value="'.$value.'" type="'.$type.'"></label><br><br>';
    }
    
    private function combo($id,$caption,$values,$onchange=''){
        
        FRAME()->READY('OPTIONS.'.$id.'=$("#'.$id.'");');
        if ($onchange!=='')
            FRAME()->READY('OPTIONS.'.$id.'.on("change",function(){'.$onchange.'});');
        
        $this->load.="OPTIONS.$id.val(Ws.user_data.$id);";
        $this->save.="if((name=='all')||(name=='".$id."')) Ws.user_data.$id=OPTIONS.$id.val();";
        
        $c='<label><div class="labeled">'.$caption.'</div>';
        $c.='<select class="labeled_edit" id="'.$id.'">';
        for($i=0;$i<count($values);$i++){
            $c.='<option value="'.$values[$i].'">'.$values[$i].'</option>';
        }
        $c.='</select></label><br><br>';
        return $c;
    }
    
    public function AJAX(&$response){
        
        global $REQUEST;
        global $USERS;
        if ($REQUEST->ID=='save_user_data'){
            $ud = $REQUEST->VALUE['user_data'];
            foreach($ud as $k=>$v){
                //_LOG("$k=[$v]");
                if(($k!=='password')||($v!=='xxxx'))
                    $USERS->put($k,$v);
            };
            if ($USERS->save_current())
                $response =array('res'=>1);
            else            
                $response =array('res'=>0);
            return true;
        }
        return false;
    }
};



?>