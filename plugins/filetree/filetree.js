/*global Ws,Qs,$,editors,ut,jmenu*/


var explorer={
delete_inside:false,
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
		 {caption:"Rename",id:"rename"},
		 '-',
		 {caption:"Run",id:"run",enable:true},
		 {caption:"Copy",id:"copy",enable:false},
		 {caption:"Paste",id:"paste",enable:false},
		 {caption:"Delete",id:"delete"},
		 '-',
		 {caption:"New folder",id:"new_folder"},
		 {caption:"New file",id:"new_file"},

		 ],
		
	onClick:function(o){
        var tree = explorer.tree();
        var node = explorer.current();
		var type = explorer.type(node);
		
        switch(o.id){

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
            if (node) tree.edit(node);  		
            break;
	    }
        case "run":{
            menu_action.run_from_tree(node);
            break;
	     }
	    
	    case "delete":{
	JDIALOG({
            caption:"Delete:",
            msg:node,
            strip:false,
            close_btn_enable:true,
            pos:{w:500,h:150},
            shadow_opacity:0.5,
            css:{header:"jd_header_strip"},
            buttons:['Delete','Cancel'],
                onClick:function(o){
                    if (o.id==0){
                         	if (node){
								explorer.delete_inside=true;
								tree.delete_node(node);					        		
					}   
                }}
            });		
			
		
			break;
	    }
        case "copy":{
              		
            break;
	    }
        case "paste":{
              		
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
    'animation':0,
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
.on('dblclick.jstree', function (event) {
	var node = $(event.target).closest('li');
	/*console.info(node);*/
	if (editors)
		editors.add({node:node});
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