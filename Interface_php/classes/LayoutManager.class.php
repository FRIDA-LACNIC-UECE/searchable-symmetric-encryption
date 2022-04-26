<?php


class LayoutManager{
	
	static $menuManager = array(); 

	static function buildLayout(){

		echo '
		<!DOCTYPE html>
		<html lang="pt-BR">
		<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    	<meta name="author" content="bruno">
    	<title>Digital Shield Security</title>

    	<!-- ** CSS ** -->
    	<!-- base library -->
	    <link rel="stylesheet" type="text/css" href="libs/extjs/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="libs/extjs/resources/css/xtheme-gray.css" />
				

    	<!-- overrides to base library -->
    	<link rel="stylesheet" type="text/css" href="styles/global.css" />
    	<script type="text/javascript" src="libs/extjs/ext-base.js"></script>
	    <script type="text/javascript" src="libs/extjs/ext-all.js"></script>
    	<script type="text/javascript" src="libs/extjs/GroupTabPanel.js"></script>
    	<script type="text/javascript" src="libs/extjs/GroupTab.js"></script>
    	<script type="text/javascript" src="libs/extjs/Richtag.js"></script>
		<script type="text/javascript">
		Ext.onReady(function() {'
		.self::generateItems()
		.self::generateSubmenu()
		.self::generateMenu().
		'
		Richtag.doLayout();
		});
		</script>		
		</head>
		<body>
		</body>
		</html>';

	}
///Stoped in here - How to generate menu and associate it to submenu id's activate.
	static function generateMenu(){
		
		$content = "Richtag.menuItems = [";
		
		$isFirstMenu = true;
		
		foreach(self::$menuManager['menu'] as $menu){
			$content 		 .= $isFirstMenu?"":",";
			$sb 			  = self::getSubmenus($menu);
			$content		 .= "{sbs:$sb,text : '{$menu['label']}',iconCls : 'x-icon-menuitem',handler : Richtag.active}";
			//self::$menuManager['submenu'][$id]['id'] = "sb{$id}";
			$isFirstMenu  = false;
		}
		
		$content .= "];";
		
		return $content;
		
	}
	
	static function getSubmenus($menu){
		
		$content = "[";
		
		$isFirstSubmenu = true;
		
		foreach($menu['submenu'] as $submenu){
			$content 		 .= $isFirstSubmenu?"":",";
			$content		 .= "'sb{$submenu['id']}'";
			$isFirstSubmenu  = false;
		}
		
		$content .= "]";
		
		return $content;
		
		
	}
	
	static function generateSubmenu(){
		
		$content = "Richtag.tabs = [";
		
		$isFirstSubmenu = true;
		
		$count = 0;
		
		foreach(self::$menuManager['submenu'] as $submenu){
			$content 		 .= $isFirstSubmenu?"":",";
			$content		 .= "{id:'sb{$submenu['id']}',expanded : false,items : Richtag.pages[{$count}]}";
			//self::$menuManager['submenu'][$id]['id'] = "sb{$id}";
			$isFirstSubmenu  = false;
			$count++;
		}
		
		$content .= "];";
	
		return $content;
	
	}
	
	static function generateItems(){
	
		$content = "
		Richtag.pages = [";

		$isFirstItem = true;
		$isFirstPage = true;
		
		$count = 0;
		
		foreach(self::$menuManager['items'] as $item){
			
			$content .= $isFirstItem?"":",";
			
			$isFirstPage = true;
			$isFirstItem = false;
			
			$content .= "[";
			
			$pages = array_keys($item);
			
			foreach($pages as $page){
				$content .= $isFirstPage?
				"{title : '".self::$menuManager['submenu'][$count]['label']."',	iconCls : 'x-icon-configuration',style : 'padding: 10px;',html : \"<iframe class='page' src='{$item[$page]}' frameborder='0'></iframe>\"},{title : '$page',	iconCls : 'x-icon-templates',style : 'padding: 10px;',html : \"<iframe class='page' src='{$item[$page]}' frameborder='0'></iframe>\"}":
				",{title : '$page',	iconCls : 'x-icon-templates',style : 'padding: 10px;',html : \"<iframe class='page' src='{$item[$page]}' frameborder='0'></iframe>\"}";
				$isFirstPage = false;
			}
			
			$content .= "]";
			
			$count++;
			
		}
		
		$content .="];";
	
		return $content;
	
	}

	
}
?>
