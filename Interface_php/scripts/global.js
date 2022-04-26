Ext.onReady(function() {
	
	Richtag.pages = [
		             [{	title : 'Projeto',	iconCls : 'x-icon-configuration',style : 'padding: 10px;',html : "<iframe class='page' src='' frameborder='0'></iframe>"	}, {title : 'Xxx',iconCls : 'x-icon-templates',	style : 'padding: 10px;',html : "<iframe class='page' src='' frameborder='0'></iframe>"}],
		             [{	title : 'Projeto',	iconCls : 'x-icon-configuration',style : 'padding: 10px;',html : "<iframe class='page' src='' frameborder='0'></iframe>"	}, {title : 'Xxx',iconCls : 'x-icon-templates',	style : 'padding: 10px;',html : "<iframe class='page' src='' frameborder='0'></iframe>"}],
		             [{	title : 'Projeto',	iconCls : 'x-icon-configuration',style : 'padding: 10px;',html : "<iframe class='page' src='' frameborder='0'></iframe>"	}, {title : 'Xxx',iconCls : 'x-icon-templates',	style : 'padding: 10px;',html : "<iframe class='page' src='' frameborder='0'></iframe>"}]
		             ];
			
	Richtag.tabs = [{id:'sb1',expanded : true,items : Richtag.pages[0]},{id:'sb2',expanded : true,items : Richtag.pages[1]},{id:'sb3',expanded : true,items : Richtag.pages[2]}];
	
	Richtag.menuItems = [ 
	{
		sbs:['sb1','sb2'],
		text : 'Menu 1',
		iconCls : 'menuItem',
		handler : Richtag.active
	}, 
	{
		sbs:['sb3'],
		text : 'Menu 2',
		iconCls : 'menuItem',
		handler : Richtag.active
	} 
	];

	Richtag.doLayout();

});