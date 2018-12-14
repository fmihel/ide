/*global Ws,Qs,$,editors,ut,jmenu,Find,menu_action,JDIALOG,popup*/


var explorer={
delete_inside:false,
paste:{from:'',enable:false},
editMode:false,
tree:function(){
	return Qs.tree.jstree(true);
},
current:function(type){
	var t=explorer,tree=t.tree(),sel;
	
	if (type=='hovered'){
		return Qs.tree.find(".jstree-wholerow-hovered");		
	}else{
		sel = tree.get_selected();
		return sel.length>0?sel[0]:null;		
	}
},
type:function(node){
	return (node?explorer.tree().get_type(node):false);	
},
refresh:function(){
    var t=explorer,tree=t.tree();
    tree.refresh();
},
deleteNode:function(){
	var tree = explorer.tree();
    var node = explorer.current();
	var type = explorer.type(node);
	
	JDIALOG({
        caption:"Delete:",
        msg:node,
        strip:false,
        close_btn_enable:true,
        pos:{w:500,h:150},
        shadow_opacity:0.5,
        css:{header:"jd_header_strip"},
        buttons:['Delete','Cancel'],
        onClick(o){
            if (o.id==0){
                if (node){
					explorer.delete_inside=true;
					tree.delete_node(node);					        		
				}   
         }}
    });			
},
editNode:function(bool){
	var tree = explorer.tree();
   	var node = explorer.current();
	
	if (bool===false){
		explorer.editMode = false;
		if (node)
			tree.get_node(node, true).children('.jstree-anchor').focus();
	}else{	
		
    	if (node) {
    		explorer.editMode = true;
    		tree.edit(node);  		
    	}
	}	
}

};

Ws.ready(function(){
Qs.fs = ut.hrefPath()+'modules/fs.php?session='+Ws.share.session+'&';    


Qs.TimerLoadTree = setInterval(function(){
Ws.ajax({id:'user_is_loading',
done:function(data){
if (data.res==1){	
clearInterval(Qs.TimerLoadTree);
/*
$(document).bind("dnd_start.vakata", function(e, data) {
    //console.log("Start dnd");
})
.bind("dnd_move.vakata", function(e, data) {
    
})
.bind("dnd_stop.vakata", function(e, data) {
    console.log("Stop dnd");
    console.log(arguments);
});
*/

Qs.expContextmenu = new jmenu({
    off:{sx:20},
    data:[
		 {caption:"Run",id:"run",enable:true},
		 '-',
		 {caption:"Copy",id:"copy",enable:true},
		 {caption:"Paste",id:"paste",enable:false},
		 '-',
		 {caption:"Duplicate",id:"duplicate",enable:true},
		 {caption:"Rename",id:"rename"},
		 {caption:"Delete",id:"delete"},
		 '-',
		 {caption:"New folder",id:"new_folder"},
		 {caption:"New file",id:"new_file"},
		 '-',
		 {caption:"Refresh tree",id:"refresh"},

		 ],
		
	onClick(o){
        var tree = explorer.tree();
        var node = explorer.current();
		var type = explorer.type(node);
		
        switch(o.id){
        case "refresh":{
            explorer.refresh();
            break;
	    }

        case "new_folder":{
  			if ((type!=='folder')&&(type!=='default'))
            	node = tree.get_parent(node);
			tree.create_node(node,{type:"folder"},"last",function(e){
        		setTimeout(function(){tree.edit(e.id);},10);
			});
            break;
	    }
        case "new_file":{
  			if ((type!=='folder')&&(type!=='default'))
            	node = tree.get_parent(node);
			tree.create_node(node,{type:"file"},"last",function(e){
        		setTimeout(function(){tree.edit(e.id);},10);
			});
              		
            break;
	    }
        case "rename":{
            explorer.editNode();
            break;
	    }
        case "run":{
            menu_action.run_from_tree(node);
            break;
	     }
	    
	    case "delete":{
			explorer.deleteNode();
			break;
	    }
	    case "duplicate":{
			Ws.ajax({
				id:'duplicate',
				value:{src:node},
				error(e){console.error(e);},
				done(data){
					if (data.res==1)
						explorer.refresh();
					else
						popup({type:'alert',msg:data.msg});
				}		
			});
	    }	
        case "copy":{
            
			explorer.paste.enable = true;
			explorer.paste.from = node;
			Qs.expContextmenu.enable('paste',true);
            break;
	    }
        case "paste":{
			explorer.paste.enable = false;
			Qs.expContextmenu.enable('paste',false);
			Ws.ajax({
				id:'paste',
				value:{from:explorer.paste.from,to:node},
				error(e){console.error(e);},
				done(data){
					if (data.res==1)
						explorer.refresh();
					else
						popup({type:'alert',msg:data.msg});
				}		
			});
            break;
	    }
	    
		}//switch
		return true;
	}
});
                
Qs.tree.on("contextmenu",false).on("mouseup",function(e){
	if (e.button==2){    
		explorer.current('hovered').trigger("click");
		setTimeout(function(){
	        Qs.expContextmenu.param.stick=$(e.target);
		    Qs.expContextmenu.show();
        },10);
	}
});


Qs.tree.jstree({
'core':{
    'animation':100,
	'data' : {
		'url' : Qs.fs+'operation=get_node',
		'data' : function (node) {
			return { 'id' : node.id };
		}
	},
	'check_callback' : function(o, n, p, i, m) {
		
		if(m && m.dnd && m.pos !== 'i') { return false; }
		if(o === 'move_node' || o === 'copy_node') {
			if(this.get_node(n).parent === this.get_node(p).id) { return false; }
	    }
	    if (explorer.delete_inside){ 
	    	explorer.delete_inside=false;
	    	return true;
	    }else{
	    	return (o!='delete_node');
	    }
	},
	'themes' : {
		'responsive' : false,
		'variant' : 'small',
		'stripes' : false,
		'dots':false
	}
},
'sort' : function(a, b) {
	return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
},
'contextmenu' : {
	'items' : function(node) {
		var tmp = $.jstree.defaults.contextmenu.items();
		delete tmp.create.action;
		tmp.create.label = 'New';
		
		tmp.create.submenu = {
			'create_folder' : {
				'separator_after'	: false,
				'label'				: 'Folder',
				'action'			: function (data) {
					
				    var inst = $.jstree.reference(data.reference),
					obj = inst.get_node(data.reference);
					inst.create_node(obj, { type : 'default' }, 'last', function (new_node) {
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
		}
		return tmp;
	}
},
'types' : {
	'default' : { 'icon' : 'folder' },
	'file' : { 'valid_children' : [], 'icon' : 'file' }
},
'unique' : {
	'duplicate' : function (name, counter) {
	    return name + ' ' + counter;
    }
},
'plugins' : ['state','dnd','sort','types','no_contextmenu','unique','wholerow']
})
.on('delete_node.jstree', function (e, data) {
	
	$.get(Qs.fs+'operation=delete_node', { 'id' : data.node.id }).fail(function () {
		data.instance.refresh();
	});
})
.on('create_node.jstree', function (e, data) {
	$.get(Qs.fs+'operation=create_node', { 'type' : data.node.type, 'id' : data.node.parent, 'text' : data.node.text })
		.done(function (d) {
			data.instance.set_id(data.node, d.id);
		})
		.fail(function () {
			data.instance.refresh();
		});
})
.on('rename_node.jstree', function (e, data) {
	$.get(Qs.fs+'operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
		.done(function (d) {
			data.instance.set_id(data.node, d.id);
			explorer.editNode(false);
			
		})
		.fail(function () {
			data.instance.refresh();
	});
})
.on('move_node.jstree', function (e, data) {
	$.get(Qs.fs+'operation=move_node', { 'id' : data.node.id, 'parent' : data.parent })
		.done(function (d) {
			/*data.instance.load_node(data.parent);*/
		    data.instance.refresh();
		})
		.fail(function () {
			data.instance.refresh();
		});
})
.on('copy_node.jstree', function (e, data) {
	$.get(Qs.fs+'operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent })
		.done(function (d) {
			/*data.instance.load_node(data.parent);*/
			data.instance.refresh();
		})
		.fail(function () {
			data.instance.refresh();
		});
})
.on('click.jstree', function (event) {
	var node = $(event.target).closest('li');
	var path =node[0].id;
	if (node.hasClass('jstree-open')||node.hasClass('jstree-closed'))
		path+='/';
	else
		path = ut.extPath(path);
	
	if (Find) Find.path(path);
	//if (editors)
	//	editors.add({node:node});
})
.on('keyup.jstree', function (e,data) { 
    var tree = explorer.tree();
    var node = explorer.current();
	
	if (e.key ==='F2')
		explorer.editNode();
		
	if ((e.key ==='Delete')&&(!explorer.editMode))	
		explorer.deleteNode();
		
	if (((e.key ==='ArrowUp')||(e.key ==='ArrowDown'))&&(!explorer.editMode)){
		$(e.target).trigger('click');
	}
		
})
.on('dblclick.jstree', function (event) {
	var node = $(event.target).closest('li');
	/*console.info(node);*/
	let open = node.hasClass('jstree-open');
	let close = node.hasClass('jstree-closed');
	let is_dir = (open||close);
	if ((!is_dir)&&(editors))
		editors.add({node:node});
	if (is_dir){
	    if (close)
	        explorer.tree().open_node(node);
	    else
	        explorer.tree().close_node(node);
	}
		
})
.on('changed.jstree', function (e, data) {
	
/*
	if(data && data.selected && data.selected.length) {
		$.get('?operation=get_content&id=' + data.selected.join(':'), function (d) {
			if(d && typeof d.type !== 'undefined') {
				$('#data .content').hide();
				switch(d.type) {
				    case 'text':
					case 'txt':
					case 'md':
					case 'htaccess':
					case 'log':
					case 'sql':
					case 'php':
					case 'js':
					case 'json':
					case 'css':
					case 'html':
					    $('#data .code').show();
					    $('#code').val(d.content);
					    break;
					case 'png':
					case 'ico':
					case 'jpg':
					case 'jpeg':
					case 'bmp':
					case 'gif':
						$('#data .image img').one('load', function () { $(this).css({'marginTop':'-' + $(this).height()/2 + 'px','marginLeft':'-' + $(this).width()/2 + 'px'}); }).attr('src',d.content);
						$('#data .image').show();
						break;
					default:
						$('#data .default').html(d.content).show();
						break;
				}
			}
		});
	} else {
		$('#data .content').hide();
		$('#data .default').html('Select a file from the tree.').show();
	}
*/
});
};}});},1000);


});/*Ws.ready*/				