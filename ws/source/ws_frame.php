<?php

if(!isset($Application)) 
    require_once '../utils/application.php';

/**
 * интерфейсная ф-ция создания или получения созданного фрейма
 * FRAME(
 *      var_name - string           имя создаваемого или искомого фрейма  
 *      [owner]  - WS_FRAME|string  родительский фрейм, в котором будет создан фрейм !!! если не указан или указана строка, то будет произведен поиск ( в группе соответствующей значению в строке)
 *      [group]  - string           имя группу, к которой принадлежит создаваемый фрейм
 * );
 * 
 * example 
 * FRAME('my',FRAME(''))    - создает фрейм в теге <body> доступ к нему на стороне клиента (javascript)-  Qs.my
 * FRAME()или FRAME('body') - вернет самый верхний фрейм <body>
 * FRAME('my')  - вернет фрейм и именем 'my' в группе поумолчанию
 * FRAME('my',FRAME('form'),'MOD1')  - создание фрейма в фрейме <div id="form">.. доступ к нему на стороне клиента Qs.MOD1.my
 * FRAME('my','MOD1')   - поиск фрейма с именем 'my', в группе 'MOD1'
 * FRAME('MOD1:my',FRAME('form'))  - создание фрейма в фрейме <div id="form">.. доступ к нему на стороне клиента Qs.MOD1.my
 * FRAME('MOD1:my') - поиск фрейма с именем 'my', в группе 'MOD1'
 * 
*/ 
function FRAME(/*one or two params, one params - get frame,two - create frame*/){
    $len = func_num_args();

    $args=array();
    $args[]  =($len>0 ?   func_get_arg(0) : 'body');
    if ($len>1) $args[] = func_get_arg(1);
    if ($len>2) $args[] = func_get_arg(2);
    $args[0] = str_replace("\\",':',$args[0]);
    
    $id = $args[0]; 
    
    if ($len<2)
        return FRAME::GET($id);
    else if (($len===2)&&(gettype($args[1])==='string')){
        $group = trim($args[1]);
        if ($group!=='') $group.=':';
        return FRAME::GET($group.$id);
    }
    else if ($len===2){ 
        if (strpos($id,':')!==false) // внутри указана группа 
            return FRAME::ADD($id,$args[1]);
        else    
            return FRAME::ADD($id,$args[1],($args[1]!==null?$args[1]->group:''));
            
    }else if ($len===3){
        return FRAME::ADD($id,$args[1],$args[2]);        
    }    
};

class FRAME{
    
    static public function ADD($id,$owner = null,$group=''){
        global $_WS;
        if($owner===null) 
            $owner = $_WS->root;
            
        $frame = new WS_FRAME($owner);
        $var = WS_UTILS::extGroupAndId($id);
        if ($var['group']==='') $var['group'] = $group;
        
        $frame->ID($var['id']);
        $frame->GROUP($var['group']);
        
        if ($owner!==null)
            $owner->ADD($frame);
            
        return $frame;
    }
    
    static public function GET(){
        global $_WS;
        
        if ((func_num_args()===0)||(func_get_arg(0)=='body')) 
            return $_WS->root;
        else{
            
            $id = func_get_arg(0);
            $var = WS_UTILS::extGroupAndId($id);
        }    
            
        $res = null;
        $own = (func_num_args()>1?func_get_arg(1):$_WS->root);
        for($i=0;$i<count($own->childs);$i++){
            
            if (($own->childs[$i]->id == $var['id']) && ($own->childs[$i]->group == $var['group']))
                return $own->childs[$i];
                
            $res = FRAME::GET($id,$own->childs[$i]);
            if ($res!==null) return $res;
        }
    }    
};

class WS_FRAME extends WS_COMMON{
    public $id;
    public $style;
    public $classes;
    public $tag_name;
    public $script;
    public $align;
    public $ready;
    public $last;
    
    public $init;
    public $css;
    public $value;
    
    public $owner;
    public $childs;
    public $lineHeight;
    public $alignAsFunc;
    
    public $group;
    
    function __construct($owner){
        $this->owner    = $owner;
        $this->childs   = array(); 

        $this->tag_name = 'div';
        $this->id       = '';
        $this->style    = '';
        $this->classes  = '';
        $this->align    = '';
        $this->script   = '';
        $this->ready    = '';
        $this->last    = '';
        $this->init     = '';
        $this->css      = '';
        $this->attr     = array();
        $this->value    = '';
        $this->lineHeight = false;
        $this->alignAsFunc = false;
        $this->group = '';
    }
    
    public function ADD($child){
        $this->childs[]=$child;
        return $child;
    }
    
    
    public function RENDER($context){
        $res = '';
        //----------------------------------------------------------------------
        if ($context==='html'){
            global $_dcss;
            if ($this->tag_name!=='input'){
                
                $res.="<$this->tag_name";
                $res.=(trim($this->id)!==''       ?" id='".$this->RENDER('id')."'"          :'');
                $res.=(trim($this->classes)!==''  ?" class='$this->classes'"  :'');
                //$res.=(trim($this->style)!==''    ?" style='".$_dcss->generate($this->style)."'"    :'');
                $res.=(trim($this->style)!==''    ?" style='".WS_UTILS::macro($this,$this->style)."'"    :'');
                foreach($this->attr as $attr=>$val)
                    $res.=" $attr='$val'";
                //if (trim($this->value)!=='')
                //    $res.="value='".$this->value."'";
                    
                    
                $res.='>';
                $res.=$this->value;
    
                for($i=0;$i<count($this->childs);$i++)
                    $res.=$this->childs[$i]->RENDER('html');

                $res.="</$this->tag_name>";
                
            }else{
                
                $res.="<$this->tag_name";
                
                $res.=(trim($this->id)!==''       ?" id='".$this->RENDER('id')."'"          :'');
                $res.=(trim($this->classes)!==''  ?" class='$this->classes'"  :'');
                //$res.=(trim($this->style)!==''    ?" style='".$_dcss->generate($this->style)."'"    :'');
                
                $res.=(trim($this->style)!==''    ?" style='".WS_UTILS::macro($this,$this->style)."'"    :'');
                
                
                if (!array_key_exists('type',$this->attr))
                    $res.=" type='text'";
                    
                foreach($this->attr as $a=>$v)
                    $res.=' '.$a.'="'.$v.'"';

                $res.=(trim($this->value)!==''       ?" value='$this->value'"          :'');
                $res.=' />';
                
    
                for($i=0;$i<count($this->childs);$i++)
                    $res.=$this->childs[$i]->RENDER('html');

                
            }

        //----------------------------------------------------------------------
        }else if ($context==='script'){

            $res=WS_UTILS::macro($this,$this->script).DCR;
            
            for($i=0;$i<count($this->childs);$i++)
                $res=WS_UTILS::concat($res,$this->childs[$i]->RENDER('script')).DCR;

            if ($this->alignAsFunc){
                
                $astr = WS_UTILS::macro($this,$this->align);
                for($i=0;$i<count($this->childs);$i++)
                    $astr=WS_UTILS::concat($astr,$this->childs[$i]->RENDER('align')).DCR;
                $astr='Ws.only("'.$this->RENDER('id').'",function(){'.DCR.$astr.'});'.DCR;
                
                $res.=$astr;
            }

        //----------------------------------------------------------------------
        }else if ($context==='ready'){
            
            $res=WS_UTILS::macro($this,$this->ready).DCR;
            for($i=0;$i<count($this->childs);$i++)
                $res=WS_UTILS::concat($res,$this->childs[$i]->RENDER('ready')).DCR;
        //----------------------------------------------------------------------
        }else if ($context==='init'){
            
            $res=WS_UTILS::macro($this,$this->init).DCR;
            for($i=0;$i<count($this->childs);$i++)
                $res=WS_UTILS::concat($res,$this->childs[$i]->RENDER('init')).DCR;
        //----------------------------------------------------------------------
        }else if ($context==='last'){
            
            $res=WS_UTILS::macro($this,$this->last).DCR;
            for($i=0;$i<count($this->childs);$i++)
                $res=WS_UTILS::concat($res,$this->childs[$i]->RENDER('last')).DCR;

        }else if ($context==='align'){
            if ($this->alignAsFunc){
                
                $res = 'Ws.only("'.$this->RENDER('id').'");'.DCR;
                
            }else{    
                $res=WS_UTILS::macro($this,$this->align).DCR;
                for($i=0;$i<count($this->childs);$i++)
                    $res=WS_UTILS::concat($res,$this->childs[$i]->RENDER('align')).DCR;
            };    

        }else if ($context==='css'){

            //$res=WS_UTILS::macro($this,$this->css);
            $res=$this->css;
            for($i=0;$i<count($this->childs);$i++)
                $res.=$this->childs[$i]->RENDER('css');

        }else if ($context==='var'){
            $group = trim($this->group);
            $delim=array('id'=>($group!==''?'_':''),'obj'=>($group!==''?'.':''));
            
            
            $res=($this->id!==''?'this.'.$group.$delim['obj'].$this->id.'=$("#'.$group.$delim['id'].$this->id.'");':'').DCR;
            for($i=0;$i<count($this->childs);$i++)
                $res.=$this->childs[$i]->RENDER('var');

        }else if ($context==='groups'){
            $is_root = (func_num_args()===1);            
            $groups = trim($this->group);
            
            for($i=0;$i<count($this->childs);$i++)
                $groups.=($groups!==''?';':'').$this->childs[$i]->RENDER('groups',true);
            
            if ($is_root){

                $groups = array_unique(explode(';',$groups));
                foreach($groups as $i=>$g){
                    if (trim($g)!==''){
                        $res.='this.'.trim($g).'={};'.DCR;                
                    }                        
                }
            }else
                $res=$groups;
                

        }else if ($context === 'id'){
            
            $group = trim($this->group);
            $res = $group.($group!==''?'_':'').$this->id;
            
        }else if ($context === 'group.var'){
            
            $group = trim($this->group);
            $res = $group.($group!==''?'.':'').$this->id;
            
        }else if ($context==='lineHeight'){
            
            $res=$this->lineHeight?(WS_UTILS::macro($this,'{$}')):'';
             
            for($i=0;$i<count($this->childs);$i++){
                $rnd = $this->childs[$i]->RENDER('lineHeight');
                if ($rnd!=='')
                    $res.=($res!==''?',':'').$rnd;
            }    

        }    
        
        return $res;
    }
    
    public function ID($value){
        $this->id=$value;
        return $this;
    }
    public function GROUP($value){
        $this->group=$value;
        return $this;
    }

    public function TAG_NAME($value){
        $this->tag_name=$value;
        return $this;
    }
    public function CLASSES($value){
        $this->classes.=($this->classes!==''?' ':'').$value;
        return $this;
    }
    public function ATTR($mean,$value){
        $this->attr[$mean] = $value;
        return $this;
    }
    public function INIT($code){
        $this->init=WS_UTILS::concat($this->init,$code).DCR;
        return $this;
    }
    public function READY($code){
        $this->ready=WS_UTILS::concat($this->ready,$code).DCR;
        return $this;
    }
    public function LAST($code){
        $this->last=WS_UTILS::concat($this->last,$code).DCR;
        return $this;
    }
    public function EVENT($events,$body){
        $code = '{$}.on("'.$events.'",function(){'.DCR.$body.DCR.'});'.DCR;
        return $this->READY($code);
    }
    public function SCRIPT($code){
        $this->script=WS_UTILS::concat($this->script,$code).DCR;
        return $this;
    }
    public function ALIGN($code){
        $this->align=WS_UTILS::concat($this->align,$code).DCR;
        return $this;
    }
    public function STYLE($style,$replace=false){  
        $this->style=( $replace ? $style : WS_UTILS::concat($this->style,$style) );
        return $this;
    }
    public function VALUE($value,$replace=true){
        if ($replace)
            $this->value=$value;
        else
            $this->value.=$value;
        return $this;
    }
    public function LH($bool){  
        $this->lineHeight=($bool?true:false);
        return $this;
    }
    public function ONLY($bool){  
        $this->alignAsFunc=($bool?true:false);
        return $this;
    }
    public function CSS($css){
        $this->css.=trim($css);
        return $this;
    }
}    


?>