<?php


MODULES::ADD('PROGRESS');
class PROGRESS extends WS_MODULE{
    function __construct($owner){
        parent::__construct($owner);
        
        RESOURCE('modules/progress/progress.dcss');
        RESOURCE('modules/progress/progress.js');

    }
    public function CONTENT(){

        $own = FRAME('progress',FRAME('modal'))
        ->STYLE('position:absolute')
        ->ALIGN('progress.align();')
        ->READY('
            
            progress.init({own:{$},stick:{by:Qs.bottom}});
            
        ');
        
    }
};

if ($Application->is_main(__FILE__)){
    echo 'module1';
}
?>