<?php

/*
https://ws-framework-fmihel.c9users.io/ide/modules/code_complit/db/db_css.php
*/

if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = true;

};



if($Application->is_main(__FILE__)){


$data=array('!important',
'-moz-border-bottom-colors',
'-moz-border-left-colors',
'-moz-border-right-colors',
'-moz-border-top-colors',
'-moz-linear-gradient',
'-moz-orient',
'-moz-radial-gradient',
'-moz-user-select',
'-ms-interpolation-mode',
'-ms-radial-gradient',
'-o-linear-gradient',
'-o-object-fit',
'-o-radial-gradient',
'-webkit-linear-gradient',
'-webkit-radial-gradient',
'-webkit-user-select',
'::-moz-placeholder',
'::-moz-selection',
'::-ms-browse',
'::-ms-check',
'::-ms-clear',
'::-ms-expand',
'::-ms-fill',
'::-ms-reveal',
'::-ms-value',
'::-webkit-input-placeholder',
'::after',
'::before',
'::first-letter',
'::first-line',
'::selection',
':active',
':after',
':before',
':checked',
':default',
':disabled',
':empty',
':enabled',
':first-child',
':first-letter',
':first-line',
':first-of-type',
':focus',
':hover',
':indeterminate',
':invalid',
':lang',
':last-child',
':last-of-type',
':link',
':not',
':nth-child',
':nth-last-child',
':nth-last-of-type',
':nth-of-type',
':only-child',
':only-of-type',
':optional',
':read-only',
':read-write',
':required',
':root',
':target',
':valid',
':visited',
'@charset',
'@font-face',
'@import',
'@media',
'@page',
'animation-delay',
'attr()',
'background',
'background-attachment',
'background-clip',
'background-color',
'background-image',
'background-origin',
'background-position',
'background-position-x',
'background-position-y',
'background-repeat',
'background-size',
'border',
'border-bottom',
'border-bottom-color',
'border-bottom-left-radius',
'border-bottom-right-radius',
'border-bottom-style',
'border-bottom-width',
'border-collapse',
'border-color',
'border-image',
'border-left',
'border-left-color',
'border-left-style',
'border-left-width',
'border-radius',
'border-right',
'border-right-color',
'border-right-style',
'border-right-width',
'border-spacing',
'border-style',
'border-top',
'border-top-color',
'border-top-left-radius',
'border-top-right-radius',
'border-top-style',
'border-top-width',
'border-width',
'bottom',
'box-shadow',
'box-sizing',
'caption-side',
'clear',
'clip',
'color',
'column-count',
'column-gap',
'column-rule',
'column-width',
'columns',
'content',
'counter-increment',
'counter-reset',
'cursor',
'direction',
'display',
'empty-cells',
'filter',
'float',
'font',
'font-family',
'font-size',
'font-stretch',
'font-style',
'font-variant',
'font-weight',
'hasLayout',
'height',
'hyphens',
'image-rendering',
'left',
'letter-spacing',
'line-height',
'list-style',
'list-style-image',
'list-style-position',
'list-style-type',
'margin',
'margin-bottom',
'margin-left',
'margin-right',
'margin-top',
'max-height',
'max-width',
'min-height',
'min-width',
'opacity',
'orphans',
'outline',
'outline-color',
'outline-offset',
'outline-style',
'outline-width',
'overflow',
'overflow-x',
'overflow-y',
'padding',
'padding-bottom',
'padding-left',
'padding-right',
'padding-top',
'page-break-after',
'page-break-before',
'page-break-inside',
'position',
'quotes',
'resize',
'right',
'scrollbar-3dlight-color',
'scrollbar-arrow-color',
'scrollbar-base-color',
'scrollbar-darkshadow-color',
'scrollbar-face-color',
'scrollbar-highlight-color',
'scrollbar-shadow-color',
'scrollbar-track-color',
'tab-size',
'table-layout',
'text-align',
'text-align-last',
'text-decoration',
'text-decoration-color',
'text-decoration-line',
'text-decoration-style',
'text-indent',
'text-overflow',
'text-shadow',
'text-transform',
'top',
'transform',
'transform-origin',
'transform-style',
'transition',
'transition-delay',
'transition-property',
'transition-timing-function',
'unicode-bidi',
'vertical-align',
'visibility',
'white-space',
'widows',
'width',
'word-break',
'word-spacing',
'word-wrap',
'writing-mode',
'z-index',
'zoom'
    );

echo '<xmp>';
$sub_class=array();

echo 'code_complit.data.css=[';
for($i=0;$i<count($data);$i++){
    
    if ((strpos($data[$i],':')===0)){
        array_push($sub_class,$data[$i]);    
    }else{
        if ($i!==0) echo ',';
        $add = '';
        
        if ((strpos($data[$i],'-')!==0)&&(strpos($data[$i],'@')!==0))
        $add = ':';
    
        echo '{';
        echo "k:'".$data[$i]."',n:'".$data[$i]." ".$add."',o:'".$data[$i].$add.'<{$cursor}>'."',t:'function'";
        echo '}'."\n";
    }    
    
}
echo ',{k:"sub",n:"sub class",o:"<{$cursor}>",t:"function",d:[":"],s:['."\n";
for($i=0;$i<count($sub_class);$i++){
    if($i>0) echo ',';
    echo '  {';
    echo "k:'".$sub_class[$i]."',n:'".$sub_class[$i]."',o:'".$sub_class[$i].'<{$cursor}>'."',t:'string'";
    echo '}'."\n";
}
echo ']}';
echo '];';
echo '</xmp>';
}

?>