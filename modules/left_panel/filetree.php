<?php

MODULES::ADD('FILETREE');
class FILETREE extends WS_MODULE{

    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('plugins/jstree/dist/jstree.min.js');
        RESOURCE('modules/left_panel/filetree.dcss');
        RESOURCE('plugins/filetree/filetree.js');
        RESOURCE('plugins/jstree/dist/themes/default/style.css');

    }
    public function CONTENT(){
        
        $tree = FRAME("tree",FRAME('explorer')->CLASSES('ws_scrollbar')->STYLE("overflow-x:hidden;overflow-y:auto"));
    }
    public function AJAX(&$response){
        /*чудо, проверка перед тем как подгружать дерево, просто иногда данные по сесси не успевают сохранится в userXXX.php*/
        global $REQUEST;
        global $USERS;
        if ($REQUEST->ID=='user_is_loading'){
                $response =array('res'=>(($USERS->get('session')==$REQUEST->SHARE('session','error'))?1:0));
            return true;
        }
        return false;
    }
};



?>