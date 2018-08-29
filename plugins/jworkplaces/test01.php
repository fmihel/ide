<?php
/*
https://ws-framework-fmihel.c9users.io/ide/plugins/jworkplaces/test01.php
*/

require_once '../../../ws/utils/application.php';
$Application->LOG_ENABLE = true;

require_once UNIT('ws','ws.php');

RESOURCE('jworkplaces.js');
RESOURCE('jworkplaces.css');
RESOURCE('../jstree/dist/jstree.min.js');
RESOURCE('../jstree/dist/themes/default/style.min.css');
RESOURCE('plugins','common/ut.js');


class WSI extends WS{
        
  public function CONTENT(){
   $frame = FRAME()
   ->CSS('
    body{
        font-family:Arial;
        font-size:12px;
    }
    .tree_frame{
        top:100px;
        width:200px;
        height:400px;
        outline:1px solid gray;
        position:absolute;
        overflow-x:hidden;
        overflow-y:auto;
    }
    .panel{
        outline:1px dashed gray;
        padding:2px;
        
    }
    .btn{
        min-width:50px;
        min-height:24px;
        margin-left:1px;
        cursor:pointer;
    }
    .btn:hover{
        color:blue;
    }
   ');
   
   FRAME('tree1',$frame)
   ->CLASSES('tree_frame')
    ->STYLE('left:50px;')
    ->SCRIPT('var tree1;')
    ->READY('tree1=new jwp({own:{$}},
    [
        {text:"root1",type:"pack",id:"root1",
            children:[{text:"file2",type:"file",id:"file2"}]
        }
        
    ]);');

    
   FRAME('tree2',$frame)
   ->CLASSES('tree_frame')
    ->STYLE('left:300px;')
    ->SCRIPT('var tree2;')
    ->READY('tree2=new jwp({own:{$}},
    [
        {text:"root2",type:"pack",id:"root2"}
        
    ]
    );');
    
   $panel = FRAME('panel1',FRAME())
    ->CLASSES('panel');
    $this->add_to_panel($panel,'btn1','tree1');

   $panel = FRAME('panel2',FRAME())
    ->CLASSES('panel');
    $this->add_to_panel($panel,'btn2','tree2');
    

   
  }
  
  private function add_to_panel($panel,$pref,$tree){
      
    FRAME($pref.'_root',$panel)
    ->TAG_NAME('input')
    ->ATTR('type','button')
    ->CLASSES('btn')
    ->VALUE('root')
    ->EVENT("click","
        $tree.add({caption:'Root',parent:'#'});
    ");
    
    
    $ii = $pref.'ii';
    FRAME($pref.'_pack',$panel)
    ->TAG_NAME('input')
    ->CLASSES('btn')
    ->ATTR('type','button')
    ->VALUE('pack')
    ->SCRIPT("var $ii=0;")
    ->EVENT('click',"
        $ii++;
        $tree.add({caption:$ii+' data ',data:{i:$ii},type:'pack'});                
    ");

   FRAME($pref.'_file',$panel)
    ->TAG_NAME('input')
    ->CLASSES('btn')
    ->ATTR('type','button')
    ->VALUE('file')
    ->EVENT("click","
        $ii++;
        $tree.add({caption:$ii+' data ',data:{i:$ii},type:'file'});                
    ");
    
    $story = $pref.'_story';
   FRAME($pref."_get",$panel)
    ->TAG_NAME('input')
    ->CLASSES('btn')
    ->ATTR('type','button')
    ->VALUE('get')
    ->SCRIPT("var $story={}")
    ->EVENT("click","
        $story = $tree.get();                
        console.info($story);
    ");

   FRAME($pref."_clear",$panel)
    ->TAG_NAME('input')
    ->CLASSES('btn')
    ->ATTR('type','button')
    ->VALUE('clear')
    ->EVENT('click',"
        $tree.clear();
    ");

   FRAME($pref."_set",$panel)
    ->TAG_NAME('input')
    ->CLASSES('btn')
    ->ATTR('type','button')
    ->VALUE('set')
    ->EVENT("click","
        $tree.set($story);
    ");
  
      
  }
  public function AJAX(&$response){
      
      
      
      return false;
  }

}      

if($Application->is_main(__FILE__)){
  $app = new WSI();
  $app->RUN();
}
?>