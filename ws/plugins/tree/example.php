<?php
/*
    https://ws-framework-fmihel.c9users.io/ide/ws/plugins/tree/example.php
*/

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);

    
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false;

};

require_once UNIT('ws','ws.php');

RESOURCE('plugins','common/jlock.js');
RESOURCE('plugins','common/jhandler.js');
RESOURCE('plugins','common/local_storage.js');

RESOURCE('plugins','shadow/jshadow.js');
RESOURCE('plugins','tree/mtree.dcss');
RESOURCE('plugins','tree/mtree.js');

RESOURCE('plugins','mbtn/mbtn.dcss');
RESOURCE('plugins','mbtn/mbtn.js');

    
class TWS extends WS{
        
    public function ONLOAD(){
        global $REQUEST;

        if ($REQUEST->IsAjax()){
            
        }else{
        
        };  
      
        return true;
    }
    
    private function css(){
        FRAME('page')->CSS('
            .btn,.btnr{
                width:90px;
                height:40px;
                line-height:40px;
                text-align:center;
                position:absolute;
                border:1px solid silver;
                cursor:pointer;
                
            }
            .btnr{
                border-radius:24px;
            }
            .label{
                width:70px;
                height:32px;
                border-bottom:1px solid silver;
                position:absolute;
            }
            .input{
                width:60px;
                height:24px;
                output:none;
                border:1px solid #3A8BD0;
                background:white;
                text-align:center;
                position:absolute;
            }
            
            .hint{
                position:absolute;
                width:200px;
            }
            ');
    }
    
    private function layout(){
        $body = FRAME()
        ->STYLE('                
            overflow-x:hidden;
            overflow-y:hidden;
            -webkit-font-smoothing:antialiased;
            
            margin:0px;
            padding:0px;
            
            font-family: Roboto, sans-serif;
            text-overflow: ellipsis;
            font-size:12px;
            
        ')
        ->ALIGN('JX.workplace(Qs.workplace,Qs.page);');
        
        $wp = FRAME('workplace',$body)
            ->STYLE('position:absolute;margin:0px;padding:0p;display:none')
            ->READY('{$}.hide().fadeIn(500,function(){Ws.align();})');
            
        FRAME('modal',FRAME())
            ->STYLE('left:0px;top:0px;position:absolute;width:0px;height:0px;z-index:1000');
        
        $page = FRAME('page',$wp)
        ->STYLE('border:0px;position:absolute');
        
        $menu = FRAME('menu',$page)->STYLE('position:absolute;width:350px;border:1px solid silver;overflow-x:none;overflow-y:auto')
        ->ALIGN('
            JX.arrange([Qs.menu,Qs.tree],{direct:"horiz",type:"left",align:"stretch",margin:5,gap:5});
        ')->ALIGN('
            JX.tile({$}.children(),{
                count:3,
                align:{
                    vert:"top",
                    horiz:"left"
                },
                margin:{left:20,top:20},
                gap:5
            });
        ');
        
        $tree = FRAME('tree',$page)->STYLE('position:absolute;width:300px;overflow-x:hidden;overflow-y:auto;border:1px solid silver');

    }
    
    private function menu(){
        $own = FRAME('menu');
        
        /* ------------------------------------------------------- */
        FRAME('getData',$own)
        ->VALUE('getData')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            console.info(my.mtree("data"));    
        }});');
        /* ------------------------------------------------------- */
        FRAME('canSelected',$own)
        ->VALUE('canSelected')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var bool = !my.mtree("canSelected");
            my.mtree({canSelected:bool});
            {$}.mbtn("active",bool);
            
        }});')
        ->READY('{$}.mbtn("active",my.mtree("canSelected"))');
        /* ------------------------------------------------------- */
        FRAME('multiSelect',$own)
        ->VALUE('multiSelect')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var bool = !my.mtree("multiSelect");
            my.mtree({multiSelect:bool});
            {$}.mbtn("active",bool);
            
        }});')
        ->READY('{$}.mbtn("active",my.mtree("multiSelect"))');
        /* ------------------------------------------------------- */
        FRAME('deepSelect',$own)
        ->VALUE('deepSelect')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var bool = !my.mtree("deepSelect");
            my.mtree({deepSelect:bool});
            {$}.mbtn("active",bool);
            
        }});')
        ->READY('{$}.mbtn("active",my.mtree("deepSelect"))');
        /* ------------------------------------------------------- */
        FRAME('selectOnlyLastNode',$own)
        ->VALUE('selOnlLastNod')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var bool = !my.mtree("selectOnlyLastNode");
            my.mtree({selectOnlyLastNode:bool});
            {$}.mbtn("active",bool);
            
        }});')
        ->READY('{$}.mbtn("active",my.mtree("selectOnlyLastNode"))');
        /* ------------------------------------------------------- */
        FRAME('toggleSelect',$own)
        ->VALUE('toggleSelect')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var bool = !my.mtree("toggleSelect");
            my.mtree({toggleSelect:bool});
            {$}.mbtn("active",bool);
            
        }});')
        ->READY('{$}.mbtn("active",my.mtree("toggleSelect"))');
        /* ------------------------------------------------------- */
        FRAME('collapseOnUnfold',$own)
        ->VALUE('colpsUnfold')
        ->CLASSES('btnr')->INIT('{$}.mbtn({click:function(){
            collapseOn("unfold");
        }});')
        ->SCRIPT('
        function collapseOn(type){
            
            Qs.collapseOnUnfold.mbtn("active",type=="unfold");
            Qs.collapseOnIcon.mbtn("active",type=="icon");
            Qs.collapseOnItem.mbtn("active",type=="item");
            my.mtree({collapseOn:type});
        }
        ')
        ->READY('collapseOn(my.mtree("collapseOn"));');
        
        FRAME('collapseOnIcon',$own)
        ->VALUE('colpsIcon')
        ->CLASSES('btnr')->INIT('{$}.mbtn({click:function(){
            collapseOn("icon");
        }});')
        ->READY('');
        
        FRAME('collapseOnItem',$own)
        ->VALUE('colpsItem')
        ->CLASSES('btnr')->INIT('{$}.mbtn({click:function(){
            collapseOn("item");
        }});')
        ->READY('');
        /* ------------------------------------------------------- */
        FRAME('select',$own)
        ->VALUE('select')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            
            console.info(my.mtree("select"));
            
        }});');
        /* ------------------------------------------------------- */
        FRAME('each',$own)
        ->VALUE('each')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            my.mtree("each",{
                info:true,
                step:function(o){
                    o.info.text.text(o.node[0].id  );
                }
                
            })
                
            
        }});');
        /* ------------------------------------------------------- */
        FRAME('find',$own)
        ->VALUE('find')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            
            var nodes = my.mtree("find",{id:"node272"});
            if (nodes.length>0){
                my.mtree("select",false);
                my.mtree("select",nodes[0],true);
                console.info(my.mtree("find","closest"));
            }
            
            
            
            
        }});');
        /* ------------------------------------------------------- */
        FRAME('collapse',$own)
        ->VALUE('collapse')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            my.mtree("collapse");            
        }});');
        /* ------------------------------------------------------- */
        FRAME('expand',$own)
        ->VALUE('expand')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            
            my.mtree("collapse");            
            my.mtree("expand",{animate:false});
        }});');
        /* ------------------------------------------------------- */
        FRAME('collapse2',$own)
        ->VALUE('collapse2')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            my.mtree("collapse",{ node:my.mtree("select")[0],env:true});            
        }});');
        /* ------------------------------------------------------- */
        FRAME('collapseEnv',$own)
        ->VALUE('collapseEnv')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var bool = !my.mtree("collapseEnv");
            my.mtree({collapseEnv:bool});
            {$}.mbtn("active",bool);
            
        }});')
        ->READY('{$}.mbtn("active",my.mtree("collapseEnv"))');
        /* ------------------------------------------------------- */
        FRAME('redraw',$own)
        ->VALUE('redraw')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            my.mtree("each",{
                info:true,
                step:function(s){
                    if (!s.info.last)
                        s.info.node.css("background-color","#F6F5F5");
                    else
                        s.info.node.css("background-color","white");
                }
            });
            
        }});');
        /* ------------------------------------------------------- */
        FRAME('animate',$own)
        ->VALUE('animate')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            if (my.mtree("animate")==0)
                my.mtree({animate:400});
            else    
                my.mtree({animate:0});
            
            {$}.mbtn("active",my.mtree("animate")!=0);
            
        }});')
        ->READY('{$}.mbtn("active",my.mtree("animate")!=0)');
        /* ------------------------------------------------------- */
        FRAME('story',$own)
        ->VALUE('story')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var data  = my.mtree("state");
            bdata.json_set({key:my[0].id,val:data});
            
            
        }});');
        /* ------------------------------------------------------- */
        FRAME('reStory',$own)
        ->VALUE('reStory')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var data = bdata.json_get({key:my[0].id});
            my.mtree("state",data);
            
        }});');
        /* ------------------------------------------------------- */
        FRAME('hide',$own)
        ->VALUE('hide')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var nodes = my.mtree("find","selected");
            if (nodes.length>0){
                JX.visible(nodes[0],false);
            }
        }});');
        ;
        /* ------------------------------------------------------- */
        FRAME('fill',$own)
        ->VALUE('fill')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            my.mtree("each",function(o){
                o.info.text.text(o.info.text.text()+"++");
            });
            
        }});');
        ;
        /* ------------------------------------------------------- */
        FRAME('icon',$own)
        ->VALUE('icon')
        ->CLASSES('btn')->INIT('{$}.mbtn({click:function(){
            var nodes = my.mtree("select");
            my.mtree("icon",nodes,"folder");
            
            
        }});');
        ;
        /* ------------------------------------------------------- */
        
    }
    
    
    private function fill_tree(){
        $own = FRAME('my');

        $c1 = 3;
        $c2 = 10;
        $c3  = 10;
        $c4 = 4;
        
        $out = array();
        for($i=0;$i<$c1;$i++){
            
            $folder = array('caption'=>'folder '.$i);
            $child = array();
            for($j=0;$j<$c2;$j++){
                $folder2 = array('caption'=>'sub '.$j);
                $child2 = array();
                for($k=0;$k<$c3;$k++){
                    
                    $folder3 = array('caption'=>'inside '.$k);
                    $child3 = array();
                    for($m=0;$m<$c4;$m++){
                        array_push($child3,array('caption'=>'file','icon'=>'file'));
                    }                    

                    array_push($child2,array('caption'=>'folder','child'=>$child3));
                    
                }
                $folder2['child'] = $child2;
                array_push($child,$folder2);
            }
            $folder['child'] = $child;
            
            array_push($out,$folder);
                        
        };
        
        return ARR::to_json($out);        
    }
    
    private function tree(){
        
        FRAME('tree')
        ->SCRIPT('var my;')
        ->INIT('
        {$}.mtree({
            data:'.$this->fill_tree().',
            onClick:function(o){
                console.info("onClick",o);
            },
            onSelect:function(o){
                console.info("onSelect",o);
                
                
                
            }
        });
        my = {$};
        
        
        ');
        
     $this->fill_tree();   
    }
    
    public function CONTENT(){
        $this->layout();
        $this->css();
        $this->menu();
        $this->tree();
        
    }
  
    public function AJAX(&$response){
        global $REQUEST;
      
        return false;
    }

}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS(); 
    $app->RUN();
    

}
?>
    