<?php
/** 
 * предварительное определение параметров настройки среды ws
*/

WS_CONF::DEF('renderPath','_render/');  // путь к папке с билдом
WS_CONF::DEF('mode','development');     // режим сборки  
WS_CONF::DEF('bildFrameJS',1);          // добавлять или нет в production JS-код генерируемый из FRAME (если 0 - то данный код будет перезагружаться при каждой подгрузке)
WS_CONF::DEF('optimizeJS',1);           // оптимизировать (1-да,0-нет) код после рендеринга 
WS_CONF::DEF('urlOptimizeCompiler','https://closure-compiler.appspot.com/compile');      // REST API для компилятора оптимизациия



?>