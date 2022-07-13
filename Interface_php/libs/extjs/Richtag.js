Richtag = {
	logo : '<img src="/images/newlogo.png" style="max-height: 50px;margin-left: 20px;"/><span id="span-top" style="margin-left: 10px;margin-top:19px;float:right;font-size: 16px;">Digital Shield Security<span>',
	separator : '-',
	mover : '->',
	view : null,
	menu : new Ext.Toolbar({
		region : 'north',
		height : 60,
		style : 'background-color:#ffffff; background-image:none;'
	}),
	menuItems:[],
	submenu : null,
	tabs : [],
	pages : [],
	center:null,
	doTableLayout:function(id){
		var grid = new Ext.ux.grid.TableGrid(id, {stripeRows: true});
        grid.render();
	},
	init : function() {
		
		for(var i = 0; i != Richtag.tabs.length;i++){
			document.getElementById('submenu__'+Richtag.tabs[i].id).style.display = 'none';
			document.getElementById(Richtag.tabs[i].id).style.display = 'none';
		}
		
		for(var i = 0; i != Richtag.menuItems[0].sbs.length;i++){
			document.getElementById('submenu__'+Richtag.menuItems[0].sbs[i]).style.display = '';
			document.getElementById(Richtag.menuItems[0].sbs[i]).style.display = '';
		}
	},
	active : function(bn) {
	
		for(var i = 0; i != Richtag.tabs.length;i++){
			document.getElementById('submenu__'+Richtag.tabs[i].id).style.display = 'none';
			document.getElementById(Richtag.tabs[i].id).style.display = 'none';
		}
		
		for(var i = 0; i != bn.sbs.length;i++){
			document.getElementById('submenu__'+bn.sbs[i]).style.display = '';
			document.getElementById(bn.sbs[i]).style.display = '';
		}
	},
	buildMenu : function() {

		Richtag.menu.add(Richtag.logo);

		Richtag.menu.add(Richtag.mover);

		for ( var i = 0; i != Richtag.menuItems.length; i++) {
			Richtag.menu.add(Richtag.menuItems[i]);
			Richtag.menu.add(Richtag.separator);
		}

		Richtag.menu.add({
			text : 'Logout',
			iconCls : 'x-icon-logout',
			handler: function(){
				
			
					window.open('/logout.php', '_self');
			        
			
			}
        
		});

	},
	buildSubmenu : function() {

		Richtag.submenu = {
			id:'submenu',
			xtype : 'grouptabpanel',
			tabWidth : 170,
			region : 'center',
			activeGroup : 0,
			items : Richtag.tabs
		};
		
		Richtag.center = new Ext.Panel({
			id:'center',
			region : 'center',
			layout : 'border',
			style : 'background-color:#4E78B1;',
			items : [ Richtag.submenu ]
		});

		
	},
	doLayout : function() {

		Richtag.buildMenu();
		
		Richtag.buildSubmenu();

		new Ext.Viewport({layout : 'border',items : [ Richtag.menu,Richtag.center ]});

		Richtag.init();
		
	}

};
