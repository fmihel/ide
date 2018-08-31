/*global Coms*/
if (typeof(Coms)!=='undefined'){
    /*c-condition,e-[extract,index of match],css-class (redefined) i-icon,n-notes*/
    Coms.addFuncs({
        php:[
            {c:/^\s*class\s*/,e:[/class\s+([^\s]+)(\s+extends|(\s)*)/,1],css:"com_block_class"},
            {c:/function\s+([^\s]*)\s*\([\s\S]*\)/,e:[/function\s+([^\s]*)\s*\([\s\S]*\)/,1],css:"com_block_func"}
            ]

    });
    
    Coms.addRules({
        php:[
            {c:/\/\*(c|com):[a-zA-Z,0-9,_]+[\s\S]*\*\//,e:[/(c|com)\:([^\s]*)(\s(.*)|\*\/)$/,2]},
            {c:/FRAME\([\s\S]+\,/,e:[/FRAME\(\s*['"]*([^\s'"]+[^"'])['"]*\,/,1]}
        ]
    });
    
}