/*global code_complit,$,JX*/

code_complit.data.js=[
    {k:"$",n:"jQuery",o:"$",d:['.'],t:'object',s:[
        {k:".extend",n:"extend(deep,target,source)",o:".extend(true,{<{$cursor}>});",t:"function"},
        {k:".data",n:"data(dom,name[,data])",o:".data(<{$cursor}>);",t:"function"}
  
  ]}
];


code_complit.code_preload(function(){
    var t = code_complit,to = code_complit.data.js;
    if (to==undefined){
        code_complit.data.js=[];
        to = code_complit.data.js;
    };
    
    t.code_db_add(to,"$",$);
    t.code_db_add(to,"JX",JX);
    t.code_db_add(to,"window",window);
    
});
