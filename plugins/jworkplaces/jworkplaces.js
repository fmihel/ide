/*global $,ut,Ws,editors,jmenu*/
function jwp(o,data){
    var t=this;
    var p=$.extend(true,{
        own:null,
        contextmenu:null,
        css:{
            icon:{
                folder:'jwp_folder',
                file:'jwp_file',
                func:'jwp_func'
            }
                
        }
    },o);
    t.param=p;
    p.tree=p.own;

	$(document).bind("dnd_stop.vakata", function(e, data) {
		t.on_stop_dnd();	   
	});
    
    t._create_tree(data);
}

jwp.prototype.on_stop_dnd=function(){
	var t=this,p=t.param,dnd=p.dnd;
	if (dnd){
		
		
		var id = dnd.parent.children[dnd.idx];
		var node = t._tree().get_node(id);
	
		node.data = {type:"file",filename:(dnd.data?dnd.node.data.filename:dnd.node.id)};

		//console.info(node);
		p.dnd = undefined;
		t.do_change({deffer:true});
	};
};

jwp.prototype._create_tree=function(data){
    var t=this,p=t.param,css=p.css,tree=p.tree;

    tree.jstree({
    core:{
        animation:0,
	    check_callback : function(o, n, p, i, m) {
		    if ((o=='move_node')||(o=='rename_node'))
		    	t.do_change({deffer:true});
			
		    if (o=='copy_node'){
		    	t.param.dnd = {node:n,parent:p,idx:i,data:n.data};
		    
		    }
		    
		    //return o!='delete_node';
		    return true;
		    //if(m && m.dnd && m.pos !== 'i') { return false; }
		    //if(o === 'move_node' || o === 'copy_node') {
			//    if(this.get_node(n).parent === this.get_node(p).id) { return false; }
	       // }
		    
	    },        
        themes:{
            stripes:false,
            variant : 'small',
        },
        data:data
    },
    types:{
        "#" : {
            //"max_children" : 1,
            //"max_depth" : 4,
            "icon" : "",
            "valid_children" : ["root","default","pack"]
        },
        "root" : {
            "icon" : "",
            "valid_children" : ["default","folder",'pack']
        },
        "default" : {
            "icon" : "",
            "valid_children" : ["default","file"]
        },
        "pack" : {
            "icon" : css.icon.folder,
            "valid_children" : ["pack","file","func"]
        },
        "file" : {
        	"icon":'file',
            "valid_children" : ["func"]
        },
        "func" : {
            "icon" : css.icon.func,
            "valid_children" : []
        }
        
    },
    contextmenu : {
		items : function(node) {
		//var tmp = $.jstree.defaults.contextmenu.items();
		var tmp={
		    "new":{
		        label:"Add workspace",
		        icon:css.icon.folder,
		        action:function(data){
		            var inst = $.jstree.reference(data.reference),
			        obj = inst.get_node(data.reference);
			        inst.create_node(obj, { type : 'pack' }, 'last', function (new_node) {
				        setTimeout(function () { inst.edit(new_node); },0);
			        });
		            
		        }
		    },
		    "add_file":{
		        label:"Add current file"
		    },
		    "add_func":{
		        label:"Add current func",
		        separator_after: true
		    },
		    "rename":{
		        label:"Rename",
		        separator_after: false,
				"shortcut"			: 113,
				"shortcut_label"	: 'F2',
				"icon"				: "glyphicon glyphicon-leaf",
				"action"			: function (data) {
					var inst = $.jstree.reference(data.reference),
						obj = inst.get_node(data.reference);
					inst.edit(obj);
				}
		        
		    },
		    "delete":{
		        label:"Delete",
		        "action":function(data){
					var inst = $.jstree.reference(data.reference),
						obj = inst.get_node(data.reference);
					if(inst.is_selected(obj)) {
						inst.delete_node(inst.get_selected());
					}else {
						inst.delete_node(obj);
					}		        
		    	}
		    }
		};
		//delete tmp.create.action;
		/*
		tmp.create.label = 'New workplace';
		tmp.create.separator_after	= false;
		tmp.create.action=function(data){
		    var inst = $.jstree.reference(data.reference),
			obj = inst.get_node(data.reference);
			inst.create_node(obj, { type : 'pack' }, 'last', function (new_node) {
				setTimeout(function () { inst.edit(new_node); },0);
			});
		};
        */
	    /*	
		tmp.create.submenu = {
			'create_folder' : {
				'separator_after'	: false,
				'label'				: 'New workplace',
				'action'			: function (data) {
				    var inst = $.jstree.reference(data.reference),
					obj = inst.get_node(data.reference);
					inst.create_node(obj, { type : 'pack' }, 'last', function (new_node) {
						setTimeout(function () { inst.edit(new_node); },0);
					});
				}
			},
			'create_file' : {
				'label'				: 'File',
				'action'			: function (data) {
					var inst = $.jstree.reference(data.reference),
					obj = inst.get_node(data.reference);
					inst.create_node(obj, { type : 'file' }, 'last', function (new_node) {
						setTimeout(function () { inst.edit(new_node); },0);
					});
				}
			}
		};
		if(this.get_type(node) === 'file') {
			delete tmp.create;
		}*/
		return tmp;
	}
    },    
    plugins : ["nocontextmenu", "dnd", "search","state", "types", "wholerow"]
    }).on('dblclick.jstree', function (event) {
		var node = $(event.target).closest("li");
		node = t._tree().get_node(node);
		//console.info(node);

		if (editors)
			editors.add(node.data);
	})
	.on('changed.jstree', function (e, data) {
		//console.info('change');
		//t.do_change();
	});
	
	
	p.contextmenu = new jmenu({
    	off:{sx:20},
        
        data:[
			 
			{caption:"Rename",id:"rename"},
			 '-',
			{caption:"Run",id:"run",enable:true},
			{caption:"Copy",id:"copy",enable:false},
			{caption:"Paste",id:"paste",enable:false},
			{caption:"Delete",id:"delete"},
			 '-',
			 {caption:"New workspace",id:"new_workspace"}
		],
		
		onClick:function(o){
			var node = t.current();
			var _tree=t._tree();
			var type = (node?_tree.get_type(node):false);
			
            switch(o.id){
            case "new_workspace":{
            	if (type){
            		if (type!=='pack')
            			node = t._tree().get_parent(node);
            			
					_tree.create_node(node,{type:"pack"},"last",function(e){
        					_tree.rename_node(e.id,"new..");
        					_tree.edit(e.id);
        					e.data = {};	    
					});
            	}
            	break;	
            }	
           	case "rename":{
               		if (node)_tree.edit(node);
               		break;
	        }
           	case "run":{
           			var filename = t._tree().get_node(node).data.filename;
               		menu_action.run_from_tree(filename);
               		break;
	        }
	        case "delete":{
				if (node){ 
					_tree.delete_node(node);					        		
					t.do_change();
				}
				break;
	        }
			}//switch
			return true;
		}
	});
                
	p.own.on("contextmenu",false).on("mouseup",function(e){
		if (e.button==2){
			var obj = t.current_hovered();
			if (obj.length>0){
				obj.trigger("click");
				setTimeout(function(){
		        	p.contextmenu.param.stick=$(e.target);
			    	p.contextmenu.show();
            	},10);
			}
	    }
	});
	
};

jwp.prototype.current=function(){
	var t=this,tree=t._tree(),sel = tree.get_selected();
	return sel.length>0?sel[0]:null;
};

jwp.prototype.current_hovered=function(){
	return this.param.own.find(".jstree-wholerow-hovered");
};


jwp.prototype._tree=function(){
    return this.param.tree.jstree(true);
    
};    

jwp.prototype.get=function () {
    var t=this,_tree=t._tree();        
    return _tree.get_json(undefined,{
		no_id:false,
		no_li_attr:false,
		no_a_attr:false,
		no_state:true

    });
};

jwp.prototype.set=function (o) {
    var t=this,_tree=t._tree();
    t._free();
    t._dn(o);
    t._create_tree(o);
};

jwp.prototype._free=function (){
    var t=this,p=t.param,_tree=t._tree();
    this.param.tree.jstree("destroy").empty();
    //this.param.tree.empty();
    //_tree.delete_node(_tree.get_json());
};

jwp.prototype.clear=function (){
    var t=this,p=t.param,_tree=t._tree();
    _tree.delete_node(_tree.get_json());
};

jwp.prototype.add=function (o) {
    var t=this,p=t.param,css=p.css,_tree=t._tree(),sel;
    var a=$.extend(true,{
        type:'pack',
        caption:"folder",
        id:undefined,
        parent:"",/*to root set parent="#"*/
        data:null,
        onadd:undefined,
        edit:false
        
    },o);
    
    sel=(a.parent=='#'?'#':_tree.get_selected());
    sel=(!sel.length?'#':sel[0]);

	sel=_tree.create_node(sel,{"type":a.type},"last",function(e){
        _tree.rename_node(e.id,a.caption);
	    if (a.id) _tree.set_id(e.id,a.id);
        e.data = a.data;	    
        if (a.edit)
        	_tree.edit(e.id);
        
        if (a.onadd) a.onadd({sender:t,id:(a.id?a.id:e.id)});
        
        
	});
    return (a.id?a.id:sel);

};

jwp.prototype.collapse=function(){
    this.param.tree.jstree('close_all');
};

jwp.prototype.do_change=function(o){
	var t=this,p=t.param,_tree=t._tree();
	var a=$.extend(true,{deffer:false,interval:3000},o);
	
	if (a.deffer){
		if (p._deffer_save)
			clearTimeout(p._deffer_save);
		p._deffer_save=setTimeout(function(){
			t.do_change({deffer:false});
			p._deffer_save=undefined;
		},a.interval);
			
	}else	
	Ws.ajax({
		id:'change_workspace',
		value:{data:t.get()},
		done:function(data){
			if (data.res==1)
				console.info('save ok');
			else
				console.info('error save workspace');
		}
	});
	
};

jwp.prototype.contextmenu=function(){
	
}


jwp.prototype._dn=function(data){
	if((data.data)&&(data.data=='')) data.data=null;
	if (data.children)for(var i=0;i<data.children.length;i++)this._dn(data.children[i]);
};