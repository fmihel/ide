/*global code_complit,$*/


var _css_replace=[
    {k:'position',n:'position:',o:'position:<{$cursor}>',t:'function',d:[':'],s:[
        {k:':absolute',n:'absolute',o:':absolute;<{$cursor}>',t:'string'},
        {k:':static',n:'static',o:':static;<{$cursor}>',t:'string'},
        {k:':relative',n:'relative',o:':relative;<{$cursor}>',t:'string'},
        {k:':fixed',n:'fixed',o:':fixed;<{$cursor}>',t:'string'}
    ]},
    {k:'float',n:'float:',o:'float:<{$cursor}>',t:'function',d:[':'],s:[
        {k:':left',n:'left',o:':left<{$cursor}>',t:'string'},
        {k:':right',n:'right',o:':right<{$cursor}>',t:'string'},
        {k:':none',n:'none',o:':none<{$cursor}>',t:'string'}
        
    ]},{k:'display',n:'display:',o:'display:<{$cursor}>',t:'function',d:[':'],s:[
        {k:':none',n:'none',o:':none<{$cursor}>',t:'string'},
        {k:':inline',n:'inline',o:':inline<{$cursor}>',t:'string'},
        {k:':block',n:'block',o:':block<{$cursor}>',t:'string'},
        {k:':inline-block',n:'inline-block',o:':inline-block<{$cursor}>',t:'string'}
        
    ]}
];

code_complit.data.css=[{k:'!important',n:'!important :',o:'!important:<{$cursor}>',t:'function'}
,{k:'-moz-border-bottom-colors',n:'-moz-border-bottom-colors ',o:'-moz-border-bottom-colors<{$cursor}>',t:'function'}
,{k:'-moz-border-left-colors',n:'-moz-border-left-colors ',o:'-moz-border-left-colors<{$cursor}>',t:'function'}
,{k:'-moz-border-right-colors',n:'-moz-border-right-colors ',o:'-moz-border-right-colors<{$cursor}>',t:'function'}
,{k:'-moz-border-top-colors',n:'-moz-border-top-colors ',o:'-moz-border-top-colors<{$cursor}>',t:'function'}
,{k:'-moz-linear-gradient',n:'-moz-linear-gradient ',o:'-moz-linear-gradient<{$cursor}>',t:'function'}
,{k:'-moz-orient',n:'-moz-orient ',o:'-moz-orient<{$cursor}>',t:'function'}
,{k:'-moz-radial-gradient',n:'-moz-radial-gradient ',o:'-moz-radial-gradient<{$cursor}>',t:'function'}
,{k:'-moz-user-select',n:'-moz-user-select ',o:'-moz-user-select<{$cursor}>',t:'function'}
,{k:'-ms-interpolation-mode',n:'-ms-interpolation-mode ',o:'-ms-interpolation-mode<{$cursor}>',t:'function'}
,{k:'-ms-radial-gradient',n:'-ms-radial-gradient ',o:'-ms-radial-gradient<{$cursor}>',t:'function'}
,{k:'-o-linear-gradient',n:'-o-linear-gradient ',o:'-o-linear-gradient<{$cursor}>',t:'function'}
,{k:'-o-object-fit',n:'-o-object-fit ',o:'-o-object-fit<{$cursor}>',t:'function'}
,{k:'-o-radial-gradient',n:'-o-radial-gradient ',o:'-o-radial-gradient<{$cursor}>',t:'function'}
,{k:'-webkit-linear-gradient',n:'-webkit-linear-gradient ',o:'-webkit-linear-gradient<{$cursor}>',t:'function'}
,{k:'-webkit-radial-gradient',n:'-webkit-radial-gradient ',o:'-webkit-radial-gradient<{$cursor}>',t:'function'}
,{k:'-webkit-user-select',n:'-webkit-user-select ',o:'-webkit-user-select<{$cursor}>',t:'function'}
,{k:'@charset',n:'@charset ',o:'@charset<{$cursor}>',t:'function'}
,{k:'@font-face',n:'@font-face ',o:'@font-face<{$cursor}>',t:'function'}
,{k:'@import',n:'@import ',o:'@import<{$cursor}>',t:'function'}
,{k:'@media',n:'@media ',o:'@media<{$cursor}>',t:'function'}
,{k:'@page',n:'@page ',o:'@page<{$cursor}>',t:'function'}
,{k:'animation-delay',n:'animation-delay :',o:'animation-delay:<{$cursor}>',t:'function'}
,{k:'attr()',n:'attr() :',o:'attr():<{$cursor}>',t:'function'}
,{k:'background',n:'background :',o:'background:<{$cursor}>',t:'function'}
,{k:'background-attachment',n:'background-attachment :',o:'background-attachment:<{$cursor}>',t:'function'}
,{k:'background-clip',n:'background-clip :',o:'background-clip:<{$cursor}>',t:'function'}
,{k:'background-color',n:'background-color :',o:'background-color:<{$cursor}>',t:'function'}
,{k:'background-image',n:'background-image :',o:'background-image:<{$cursor}>',t:'function'}
,{k:'background-origin',n:'background-origin :',o:'background-origin:<{$cursor}>',t:'function'}
,{k:'background-position',n:'background-position :',o:'background-position:<{$cursor}>',t:'function'}
,{k:'background-position-x',n:'background-position-x :',o:'background-position-x:<{$cursor}>',t:'function'}
,{k:'background-position-y',n:'background-position-y :',o:'background-position-y:<{$cursor}>',t:'function'}
,{k:'background-repeat',n:'background-repeat :',o:'background-repeat:<{$cursor}>',t:'function'}
,{k:'background-size',n:'background-size :',o:'background-size:<{$cursor}>',t:'function'}
,{k:'border',n:'border :',o:'border:<{$cursor}>',t:'function'}
,{k:'border-bottom',n:'border-bottom :',o:'border-bottom:<{$cursor}>',t:'function'}
,{k:'border-bottom-color',n:'border-bottom-color :',o:'border-bottom-color:<{$cursor}>',t:'function'}
,{k:'border-bottom-left-radius',n:'border-bottom-left-radius :',o:'border-bottom-left-radius:<{$cursor}>',t:'function'}
,{k:'border-bottom-right-radius',n:'border-bottom-right-radius :',o:'border-bottom-right-radius:<{$cursor}>',t:'function'}
,{k:'border-bottom-style',n:'border-bottom-style :',o:'border-bottom-style:<{$cursor}>',t:'function'}
,{k:'border-bottom-width',n:'border-bottom-width :',o:'border-bottom-width:<{$cursor}>',t:'function'}
,{k:'border-collapse',n:'border-collapse :',o:'border-collapse:<{$cursor}>',t:'function'}
,{k:'border-color',n:'border-color :',o:'border-color:<{$cursor}>',t:'function'}
,{k:'border-image',n:'border-image :',o:'border-image:<{$cursor}>',t:'function'}
,{k:'border-left',n:'border-left :',o:'border-left:<{$cursor}>',t:'function'}
,{k:'border-left-color',n:'border-left-color :',o:'border-left-color:<{$cursor}>',t:'function'}
,{k:'border-left-style',n:'border-left-style :',o:'border-left-style:<{$cursor}>',t:'function'}
,{k:'border-left-width',n:'border-left-width :',o:'border-left-width:<{$cursor}>',t:'function'}
,{k:'border-radius',n:'border-radius :',o:'border-radius:<{$cursor}>',t:'function'}
,{k:'border-right',n:'border-right :',o:'border-right:<{$cursor}>',t:'function'}
,{k:'border-right-color',n:'border-right-color :',o:'border-right-color:<{$cursor}>',t:'function'}
,{k:'border-right-style',n:'border-right-style :',o:'border-right-style:<{$cursor}>',t:'function'}
,{k:'border-right-width',n:'border-right-width :',o:'border-right-width:<{$cursor}>',t:'function'}
,{k:'border-spacing',n:'border-spacing :',o:'border-spacing:<{$cursor}>',t:'function'}
,{k:'border-style',n:'border-style :',o:'border-style:<{$cursor}>',t:'function'}
,{k:'border-top',n:'border-top :',o:'border-top:<{$cursor}>',t:'function'}
,{k:'border-top-color',n:'border-top-color :',o:'border-top-color:<{$cursor}>',t:'function'}
,{k:'border-top-left-radius',n:'border-top-left-radius :',o:'border-top-left-radius:<{$cursor}>',t:'function'}
,{k:'border-top-right-radius',n:'border-top-right-radius :',o:'border-top-right-radius:<{$cursor}>',t:'function'}
,{k:'border-top-style',n:'border-top-style :',o:'border-top-style:<{$cursor}>',t:'function'}
,{k:'border-top-width',n:'border-top-width :',o:'border-top-width:<{$cursor}>',t:'function'}
,{k:'border-width',n:'border-width :',o:'border-width:<{$cursor}>',t:'function'}
,{k:'bottom',n:'bottom :',o:'bottom:<{$cursor}>',t:'function'}
,{k:'box-shadow',n:'box-shadow :',o:'box-shadow:<{$cursor}>',t:'function'}
,{k:'box-sizing',n:'box-sizing :',o:'box-sizing:<{$cursor}>',t:'function'}
,{k:'caption-side',n:'caption-side :',o:'caption-side:<{$cursor}>',t:'function'}
,{k:'clear',n:'clear :',o:'clear:<{$cursor}>',t:'function'}
,{k:'clip',n:'clip :',o:'clip:<{$cursor}>',t:'function'}
,{k:'color',n:'color :',o:'color:<{$cursor}>',t:'function'}
,{k:'column-count',n:'column-count :',o:'column-count:<{$cursor}>',t:'function'}
,{k:'column-gap',n:'column-gap :',o:'column-gap:<{$cursor}>',t:'function'}
,{k:'column-rule',n:'column-rule :',o:'column-rule:<{$cursor}>',t:'function'}
,{k:'column-width',n:'column-width :',o:'column-width:<{$cursor}>',t:'function'}
,{k:'columns',n:'columns :',o:'columns:<{$cursor}>',t:'function'}
,{k:'content',n:'content :',o:'content:<{$cursor}>',t:'function'}
,{k:'counter-increment',n:'counter-increment :',o:'counter-increment:<{$cursor}>',t:'function'}
,{k:'counter-reset',n:'counter-reset :',o:'counter-reset:<{$cursor}>',t:'function'}
,{k:'cursor',n:'cursor :',o:'cursor:<{$cursor}>',t:'function'}
,{k:'direction',n:'direction :',o:'direction:<{$cursor}>',t:'function'}
,{k:'display',n:'display :',o:'display:<{$cursor}>',t:'function'}
,{k:'empty-cells',n:'empty-cells :',o:'empty-cells:<{$cursor}>',t:'function'}
,{k:'filter',n:'filter :',o:'filter:<{$cursor}>',t:'function'}
,{k:'float',n:'float :',o:'float:<{$cursor}>',t:'function'}
,{k:'font',n:'font :',o:'font:<{$cursor}>',t:'function'}
,{k:'font-family',n:'font-family :',o:'font-family:<{$cursor}>',t:'function'}
,{k:'font-size',n:'font-size :',o:'font-size:<{$cursor}>',t:'function'}
,{k:'font-stretch',n:'font-stretch :',o:'font-stretch:<{$cursor}>',t:'function'}
,{k:'font-style',n:'font-style :',o:'font-style:<{$cursor}>',t:'function'}
,{k:'font-variant',n:'font-variant :',o:'font-variant:<{$cursor}>',t:'function'}
,{k:'font-weight',n:'font-weight :',o:'font-weight:<{$cursor}>',t:'function'}
,{k:'hasLayout',n:'hasLayout :',o:'hasLayout:<{$cursor}>',t:'function'}
,{k:'height',n:'height :',o:'height:<{$cursor}>',t:'function'}
,{k:'hyphens',n:'hyphens :',o:'hyphens:<{$cursor}>',t:'function'}
,{k:'image-rendering',n:'image-rendering :',o:'image-rendering:<{$cursor}>',t:'function'}
,{k:'left',n:'left :',o:'left:<{$cursor}>',t:'function'}
,{k:'letter-spacing',n:'letter-spacing :',o:'letter-spacing:<{$cursor}>',t:'function'}
,{k:'line-height',n:'line-height :',o:'line-height:<{$cursor}>',t:'function'}
,{k:'list-style',n:'list-style :',o:'list-style:<{$cursor}>',t:'function'}
,{k:'list-style-image',n:'list-style-image :',o:'list-style-image:<{$cursor}>',t:'function'}
,{k:'list-style-position',n:'list-style-position :',o:'list-style-position:<{$cursor}>',t:'function'}
,{k:'list-style-type',n:'list-style-type :',o:'list-style-type:<{$cursor}>',t:'function'}
,{k:'margin',n:'margin :',o:'margin:<{$cursor}>',t:'function'}
,{k:'margin-bottom',n:'margin-bottom :',o:'margin-bottom:<{$cursor}>',t:'function'}
,{k:'margin-left',n:'margin-left :',o:'margin-left:<{$cursor}>',t:'function'}
,{k:'margin-right',n:'margin-right :',o:'margin-right:<{$cursor}>',t:'function'}
,{k:'margin-top',n:'margin-top :',o:'margin-top:<{$cursor}>',t:'function'}
,{k:'max-height',n:'max-height :',o:'max-height:<{$cursor}>',t:'function'}
,{k:'max-width',n:'max-width :',o:'max-width:<{$cursor}>',t:'function'}
,{k:'min-height',n:'min-height :',o:'min-height:<{$cursor}>',t:'function'}
,{k:'min-width',n:'min-width :',o:'min-width:<{$cursor}>',t:'function'}
,{k:'opacity',n:'opacity :',o:'opacity:<{$cursor}>',t:'function'}
,{k:'orphans',n:'orphans :',o:'orphans:<{$cursor}>',t:'function'}
,{k:'outline',n:'outline :',o:'outline:<{$cursor}>',t:'function'}
,{k:'outline-color',n:'outline-color :',o:'outline-color:<{$cursor}>',t:'function'}
,{k:'outline-offset',n:'outline-offset :',o:'outline-offset:<{$cursor}>',t:'function'}
,{k:'outline-style',n:'outline-style :',o:'outline-style:<{$cursor}>',t:'function'}
,{k:'outline-width',n:'outline-width :',o:'outline-width:<{$cursor}>',t:'function'}
,{k:'overflow',n:'overflow :',o:'overflow:<{$cursor}>',t:'function'}
,{k:'overflow-x',n:'overflow-x :',o:'overflow-x:<{$cursor}>',t:'function'}
,{k:'overflow-y',n:'overflow-y :',o:'overflow-y:<{$cursor}>',t:'function'}
,{k:'padding',n:'padding :',o:'padding:<{$cursor}>',t:'function'}
,{k:'padding-bottom',n:'padding-bottom :',o:'padding-bottom:<{$cursor}>',t:'function'}
,{k:'padding-left',n:'padding-left :',o:'padding-left:<{$cursor}>',t:'function'}
,{k:'padding-right',n:'padding-right :',o:'padding-right:<{$cursor}>',t:'function'}
,{k:'padding-top',n:'padding-top :',o:'padding-top:<{$cursor}>',t:'function'}
,{k:'page-break-after',n:'page-break-after :',o:'page-break-after:<{$cursor}>',t:'function'}
,{k:'page-break-before',n:'page-break-before :',o:'page-break-before:<{$cursor}>',t:'function'}
,{k:'page-break-inside',n:'page-break-inside :',o:'page-break-inside:<{$cursor}>',t:'function'}
,{k:'position',n:'position :',o:'position:<{$cursor}>',t:'function'}
,{k:'quotes',n:'quotes :',o:'quotes:<{$cursor}>',t:'function'}
,{k:'resize',n:'resize :',o:'resize:<{$cursor}>',t:'function'}
,{k:'right',n:'right :',o:'right:<{$cursor}>',t:'function'}
,{k:'scrollbar-3dlight-color',n:'scrollbar-3dlight-color :',o:'scrollbar-3dlight-color:<{$cursor}>',t:'function'}
,{k:'scrollbar-arrow-color',n:'scrollbar-arrow-color :',o:'scrollbar-arrow-color:<{$cursor}>',t:'function'}
,{k:'scrollbar-base-color',n:'scrollbar-base-color :',o:'scrollbar-base-color:<{$cursor}>',t:'function'}
,{k:'scrollbar-darkshadow-color',n:'scrollbar-darkshadow-color :',o:'scrollbar-darkshadow-color:<{$cursor}>',t:'function'}
,{k:'scrollbar-face-color',n:'scrollbar-face-color :',o:'scrollbar-face-color:<{$cursor}>',t:'function'}
,{k:'scrollbar-highlight-color',n:'scrollbar-highlight-color :',o:'scrollbar-highlight-color:<{$cursor}>',t:'function'}
,{k:'scrollbar-shadow-color',n:'scrollbar-shadow-color :',o:'scrollbar-shadow-color:<{$cursor}>',t:'function'}
,{k:'scrollbar-track-color',n:'scrollbar-track-color :',o:'scrollbar-track-color:<{$cursor}>',t:'function'}
,{k:'tab-size',n:'tab-size :',o:'tab-size:<{$cursor}>',t:'function'}
,{k:'table-layout',n:'table-layout :',o:'table-layout:<{$cursor}>',t:'function'}
,{k:'text-align',n:'text-align :',o:'text-align:<{$cursor}>',t:'function'}
,{k:'text-align-last',n:'text-align-last :',o:'text-align-last:<{$cursor}>',t:'function'}
,{k:'text-decoration',n:'text-decoration :',o:'text-decoration:<{$cursor}>',t:'function'}
,{k:'text-decoration-color',n:'text-decoration-color :',o:'text-decoration-color:<{$cursor}>',t:'function'}
,{k:'text-decoration-line',n:'text-decoration-line :',o:'text-decoration-line:<{$cursor}>',t:'function'}
,{k:'text-decoration-style',n:'text-decoration-style :',o:'text-decoration-style:<{$cursor}>',t:'function'}
,{k:'text-indent',n:'text-indent :',o:'text-indent:<{$cursor}>',t:'function'}
,{k:'text-overflow',n:'text-overflow :',o:'text-overflow:<{$cursor}>',t:'function'}
,{k:'text-shadow',n:'text-shadow :',o:'text-shadow:<{$cursor}>',t:'function'}
,{k:'text-transform',n:'text-transform :',o:'text-transform:<{$cursor}>',t:'function'}
,{k:'top',n:'top :',o:'top:<{$cursor}>',t:'function'}
,{k:'transform',n:'transform :',o:'transform:<{$cursor}>',t:'function'}
,{k:'transform-origin',n:'transform-origin :',o:'transform-origin:<{$cursor}>',t:'function'}
,{k:'transform-style',n:'transform-style :',o:'transform-style:<{$cursor}>',t:'function'}
,{k:'transition',n:'transition :',o:'transition:<{$cursor}>',t:'function'}
,{k:'transition-delay',n:'transition-delay :',o:'transition-delay:<{$cursor}>',t:'function'}
,{k:'transition-property',n:'transition-property :',o:'transition-property:<{$cursor}>',t:'function'}
,{k:'transition-timing-function',n:'transition-timing-function :',o:'transition-timing-function:<{$cursor}>',t:'function'}
,{k:'unicode-bidi',n:'unicode-bidi :',o:'unicode-bidi:<{$cursor}>',t:'function'}
,{k:'vertical-align',n:'vertical-align :',o:'vertical-align:<{$cursor}>',t:'function'}
,{k:'visibility',n:'visibility :',o:'visibility:<{$cursor}>',t:'function'}
,{k:'white-space',n:'white-space :',o:'white-space:<{$cursor}>',t:'function'}
,{k:'widows',n:'widows :',o:'widows:<{$cursor}>',t:'function'}
,{k:'width',n:'width :',o:'width:<{$cursor}>',t:'function'}
,{k:'word-break',n:'word-break :',o:'word-break:<{$cursor}>',t:'function'}
,{k:'word-spacing',n:'word-spacing :',o:'word-spacing:<{$cursor}>',t:'function'}
,{k:'word-wrap',n:'word-wrap :',o:'word-wrap:<{$cursor}>',t:'function'}
,{k:'writing-mode',n:'writing-mode :',o:'writing-mode:<{$cursor}>',t:'function'}
,{k:'z-index',n:'z-index :',o:'z-index:<{$cursor}>',t:'function'}
,{k:'zoom',n:'zoom :',o:'zoom:<{$cursor}>',t:'function'}
,{k:"sub",n:"sub class",o:"<{$cursor}>",t:"function",d:[":"],s:[
  {k:'::-moz-placeholder',n:'::-moz-placeholder',o:'::-moz-placeholder<{$cursor}>',t:'string'}
,  {k:'::-moz-selection',n:'::-moz-selection',o:'::-moz-selection<{$cursor}>',t:'string'}
,  {k:'::-ms-browse',n:'::-ms-browse',o:'::-ms-browse<{$cursor}>',t:'string'}
,  {k:'::-ms-check',n:'::-ms-check',o:'::-ms-check<{$cursor}>',t:'string'}
,  {k:'::-ms-clear',n:'::-ms-clear',o:'::-ms-clear<{$cursor}>',t:'string'}
,  {k:'::-ms-expand',n:'::-ms-expand',o:'::-ms-expand<{$cursor}>',t:'string'}
,  {k:'::-ms-fill',n:'::-ms-fill',o:'::-ms-fill<{$cursor}>',t:'string'}
,  {k:'::-ms-reveal',n:'::-ms-reveal',o:'::-ms-reveal<{$cursor}>',t:'string'}
,  {k:'::-ms-value',n:'::-ms-value',o:'::-ms-value<{$cursor}>',t:'string'}
,  {k:'::-webkit-input-placeholder',n:'::-webkit-input-placeholder',o:'::-webkit-input-placeholder<{$cursor}>',t:'string'}
,  {k:'::after',n:'::after',o:'::after<{$cursor}>',t:'string'}
,  {k:'::before',n:'::before',o:'::before<{$cursor}>',t:'string'}
,  {k:'::first-letter',n:'::first-letter',o:'::first-letter<{$cursor}>',t:'string'}
,  {k:'::first-line',n:'::first-line',o:'::first-line<{$cursor}>',t:'string'}
,  {k:'::selection',n:'::selection',o:'::selection<{$cursor}>',t:'string'}
,  {k:':active',n:':active',o:':active<{$cursor}>',t:'string'}
,  {k:':after',n:':after',o:':after<{$cursor}>',t:'string'}
,  {k:':before',n:':before',o:':before<{$cursor}>',t:'string'}
,  {k:':checked',n:':checked',o:':checked<{$cursor}>',t:'string'}
,  {k:':default',n:':default',o:':default<{$cursor}>',t:'string'}
,  {k:':disabled',n:':disabled',o:':disabled<{$cursor}>',t:'string'}
,  {k:':empty',n:':empty',o:':empty<{$cursor}>',t:'string'}
,  {k:':enabled',n:':enabled',o:':enabled<{$cursor}>',t:'string'}
,  {k:':first-child',n:':first-child',o:':first-child<{$cursor}>',t:'string'}
,  {k:':first-letter',n:':first-letter',o:':first-letter<{$cursor}>',t:'string'}
,  {k:':first-line',n:':first-line',o:':first-line<{$cursor}>',t:'string'}
,  {k:':first-of-type',n:':first-of-type',o:':first-of-type<{$cursor}>',t:'string'}
,  {k:':focus',n:':focus',o:':focus<{$cursor}>',t:'string'}
,  {k:':hover',n:':hover',o:':hover<{$cursor}>',t:'string'}
,  {k:':indeterminate',n:':indeterminate',o:':indeterminate<{$cursor}>',t:'string'}
,  {k:':invalid',n:':invalid',o:':invalid<{$cursor}>',t:'string'}
,  {k:':lang',n:':lang',o:':lang<{$cursor}>',t:'string'}
,  {k:':last-child',n:':last-child',o:':last-child<{$cursor}>',t:'string'}
,  {k:':last-of-type',n:':last-of-type',o:':last-of-type<{$cursor}>',t:'string'}
,  {k:':link',n:':link',o:':link<{$cursor}>',t:'string'}
,  {k:':not',n:':not',o:':not<{$cursor}>',t:'string'}
,  {k:':nth-child',n:':nth-child',o:':nth-child<{$cursor}>',t:'string'}
,  {k:':nth-last-child',n:':nth-last-child',o:':nth-last-child<{$cursor}>',t:'string'}
,  {k:':nth-last-of-type',n:':nth-last-of-type',o:':nth-last-of-type<{$cursor}>',t:'string'}
,  {k:':nth-of-type',n:':nth-of-type',o:':nth-of-type<{$cursor}>',t:'string'}
,  {k:':only-child',n:':only-child',o:':only-child<{$cursor}>',t:'string'}
,  {k:':only-of-type',n:':only-of-type',o:':only-of-type<{$cursor}>',t:'string'}
,  {k:':optional',n:':optional',o:':optional<{$cursor}>',t:'string'}
,  {k:':read-only',n:':read-only',o:':read-only<{$cursor}>',t:'string'}
,  {k:':read-write',n:':read-write',o:':read-write<{$cursor}>',t:'string'}
,  {k:':required',n:':required',o:':required<{$cursor}>',t:'string'}
,  {k:':root',n:':root',o:':root<{$cursor}>',t:'string'}
,  {k:':target',n:':target',o:':target<{$cursor}>',t:'string'}
,  {k:':valid',n:':valid',o:':valid<{$cursor}>',t:'string'}
,  {k:':visited',n:':visited',o:':visited<{$cursor}>',t:'string'}
]}];

code_complit.code_preload(function(){
    var t = code_complit,to = code_complit.data.css;

    for(var i=0;i<to.length;i++){
        
        var ex = t._code_db_get(_css_replace,to[i].k);
        if (ex)
            to[i] = ex.item;                        
    }

});
