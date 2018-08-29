/*global localStorage,$*/
function bdata(){}; /*browser data story*/
bdata.exist = function(o){ 
    if (typeof(o) == "string") o={key:o};
    var a=$.extend(true,{
        key:'',
        type:'local'
    },o);
    if (a.key=='') return false;
    return (a.type!='local'?(sessionStorage.getItem(a.key)!==null):(localStorage.getItem(a.key)!==null));    
};

bdata.get = function(o){
    if (typeof(o) == "string") o={key:o};
    var a=$.extend(true,{
        key:'',
        type:'local',
        def:''
    },o);
    if (a.key=='') return -1;
    if (bdata.exist(a))
        return (a.type!='local'?(sessionStorage.getItem(a.key)):(localStorage.getItem(a.key)));    
    else
        return a.def;
};

bdata.set = function(o,val){ 
    if (typeof(o) == "string") o={key:o,val:val};
    var a=$.extend(true,{
        key:'',
        type:'local',
        val:''
    },o);
    if (a.key=='') return;    
    var data = (typeof(a.val) == "object"?JSON.stringify(a.val):a.val);

    (a.type!='local'?(sessionStorage.setItem(a.key,data)):(localStorage.setItem(a.key,data)));    
    
};

bdata.json_get = function(o){ 
    if (typeof(o) == "string") o={key:o};
    var a=$.extend(true,{
        key:'',
        type:'local',
        def:{}
    },o);
    if (a.key=='') return -1;
    if (bdata.exist(a))
        return JSON.parse(bdata.get(a));
    else
        return a.def;
};

bdata.json_set = function(o){ 
    bdata.set(o);    
};


bdata.del = function(o){ 
    if (typeof(o) == "string") o={key:o};
    var a=$.extend(true,{
        key:'',
        type:'local',
    },o);
    if (a.key=='') return;
    if (bdata.exist(a))
        (a.type!='local'?(sessionStorage.removeItem(a.key)):(localStorage.removeItem(a.key)));    
};