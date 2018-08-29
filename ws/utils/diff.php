<?php
/** сравнение текстов 
 * @param {string} textA - первый текст к сравнению (более новый)
 * @param {string} textB - второй текст к сравнению (более старый)
 * @return array(textA,textB) - возвращает отформатированный текст к отображению через html в тегах <pre>
 * Для выделения изменений и добавлений в тексте используется css class changed
 * Для выделения удалений из текста используется css class deleted
 * 
 * Алгоритм реализован "по мотивам" алогритма поиска Наиболее Общих Последовательностей. Однако менее критичен к 
 * используемой память.
 * 
*/
class DIFF{
    
    public static function apply($textA,$textB){
        $A = array();
        $B = array();
        $arrA = explode("\n", str_replace("\r", "", $textA));
        $arrB = explode("\n", str_replace("\r", "", $textB));
        
        
        
        for($i=0;$i<count($arrA);$i++)
            $A[$i]=array($i,$arrA[$i]);
        
        for($i=0;$i<count($arrB);$i++)
            $B[$i]=array($i,$arrB[$i]);
        
        $MaxI = 1;
        $loop = max(count($arrA),count($arrB))+2;
        
        while ($MaxI>=0){
            $loop--;
            if ($loop<=0){
                echo 'loop error ';
                exit;
            }
            
            
            
            $res = self::_lcs($A,$B);
            
            $Sd     = $res[0];
            $MaxI   = $res[1];
            
            if ($MaxI>-1){
                
                array_splice($A,$Sd[$MaxI][1]-$Sd[$MaxI][3],$Sd[$MaxI][0]);
                array_splice($B,$Sd[$MaxI][2]-$Sd[$MaxI][3],$Sd[$MaxI][0]);
            }
        }
        
        //--------------------------------------------------------------------------
        $RND = '@'.mt_rand(100,999);
        $space = '   ';
        for($i=0;$i<count($A);$i++){
            if ($arrA[$A[$i][0]]=='')$arrA[$A[$i][0]]=$space;
            $arrA[$A[$i][0]]= '{CHANGED:}'.$RND.$arrA[$A[$i][0]].'{:CHANGED}'.$RND;
        }    
        
        for($i=0;$i<count($B);$i++){
            if ($arrB[$B[$i][0]]=='')$arrB[$B[$i][0]]=$space;
            $arrB[$B[$i][0]]='{DELETED:}'.$RND.$arrB[$B[$i][0]].'{:DELETED}'.$RND;
        }
         $from = array(
            '{DELETED:}'.$RND,
            '{:DELETED}'.$RND,
            '{CHANGED:}'.$RND,
            '{:CHANGED}'.$RND,
        );
        $to = array(
            '<span class="deleted">',
            '</span>',
            '<span class="changed">',
            '</span>'
        );
        
        $textA =  htmlspecialchars(implode("\n",$arrA));
        $textB =  htmlspecialchars(implode("\n",$arrB));

        //--------------------------------------------------------------------------
        
        return array(str_replace($from,$to,$textA),str_replace($from,$to,$textB));
        
    }
    public static function simple($textA,$textB,$tag='span',$changed='changed',$deleted='deleted'){
        
        $data = self::struct($textA,$textB,true);
        for($i=0;$i<count($data['diff'][0]);$i++){
            $idx = $data['diff'][0][$i];
            $data['rows'][0][$idx]='<'.$tag.' class="'.$changed.'">'.$data['rows'][0][$idx].'</'.$tag.'>';
        }    
        
        for($i=0;$i<count($data['diff'][1]);$i++){
            $idx = $data['diff'][1][$i];
            $data['rows'][1][$idx]='<'.$tag.' class="'.$deleted.'">'.$data['rows'][1][$idx].'</'.$tag.'>';
        }    

        return array(
            implode("\n",$data['rows'][0]),
            implode("\n",$data['rows'][1]),
            self::_splitDiff($data['diff'][0]),
            self::_splitDiff($data['diff'][1])
        );
    }
    private static function _splitDiff($d){
        $out=array();
        $prev = -1;
        
        for($i=0;$i<count($d);$i++){
            if ($prev!==$d[$i]){
                array_push($out,$d[$i]);
                $prev = $d[$i];
            };
            $prev++;
        }
        
        return $out;
        
    }
    public static function struct($textA,$textB,$htmlSpecial=false){
        $A = array();
        $B = array();
        
        $textA = str_replace("\r", "", $textA);
        $textB = str_replace("\r", "", $textB);
        
        if ($htmlSpecial){
            $textA = htmlspecialchars($textA);
            $textB = htmlspecialchars($textB);
        }
        
        $arrA = explode("\n",$textA);
        $arrB = explode("\n", $textB);
        
        
        
        for($i=0;$i<count($arrA);$i++)
            $A[$i]=array($i,$arrA[$i]);
        
        for($i=0;$i<count($arrB);$i++)
            $B[$i]=array($i,$arrB[$i]);
        
        $MaxI = 1;
        $loop = max(count($arrA),count($arrB))+2;
        
        while ($MaxI>=0){
            $loop--;
            if ($loop<=0){
                echo 'loop error ';
                exit;
            }

            $res = self::_lcs($A,$B);
            
            $Sd     = $res[0];
            $MaxI   = $res[1];
            
            if ($MaxI>-1){
                
                array_splice($A,$Sd[$MaxI][1]-$Sd[$MaxI][3],$Sd[$MaxI][0]);
                array_splice($B,$Sd[$MaxI][2]-$Sd[$MaxI][3],$Sd[$MaxI][0]);
            }
        }
        
        //--------------------------------------------------------------------------
        $Ai=array();
        for($i=0;$i<count($A);$i++)
                array_push($Ai,$A[$i][0]);

        $Bi=array();
        for($i=0;$i<count($B);$i++)
            array_push($Bi,$B[$i][0]);
        //--------------------------------------------------------------------------
        
        return array('rows'=>array($arrA,$arrB),'diff'=>array($Ai,$Bi));
        
    }
    
    private static function _lcs($A,$B){
        
        $Sd = array();
        $MaxI = -1;
        $MaxD = 0;

        $countA = count($A);
        $countB = count($B);
        //echo 'countA:'.$countA.'==='.'countB:'.$countB."\n";
        
        for($i=0;$i<$countA;$i++){
    
            $Sd[$i] = array(0);
            for($j=0;$j<$countB;$j++){
        
                $_Sd = array(0);
                if ($A[$i][1] === $B[$j][1]){
                    // $Sd = (D,i,j,off)
                    $_Sd = array(1,$i,$j,0);
                    $min = min($i,$j)+1;

                    for($k=1;$k<$min;$k++){
                        if ($A[$i-$k][1] === $B[$j-$k][1]){
                            $_Sd[0]=$_Sd[0]+1;
                            $_Sd[3]=$_Sd[3]-1;
                        }else 
                            break;    
                    }

                    $min = min($countA-$i,$countB-$j);
            
                    for($k=1;$k<$min;$k++){
                        if ($A[$i+$k][1] === $B[$j+$k][1]){
                            $_Sd[0]=$_Sd[0]+1;
                        }else 
                            break;    
                    }
            
                    if ($Sd[$i][0]<$_Sd[0])
                        $Sd[$i]=$_Sd;
                }
            }
    
            if ($i===0){
                $MaxD = $Sd[0][0];
                if ($MaxD>0) $MaxI = $i;
            }else{
                if ($Sd[$i][0]>$MaxD){
                    $MaxI = $i;
                    $MaxD = $Sd[$i][0];
                }
            }
        }
        return array($Sd,$MaxI);
        
    }//function _lcs    
}
?>