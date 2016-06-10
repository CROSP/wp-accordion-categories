function typeOf (obj) {
  return {}.toString.call(obj).split(' ')[1].slice(0, -1).toLowerCase();
}
var accordionCategoryModule = (function($){
   var data =  {
   	  childrenParentItems : [],
   	  parentEmptyItems : [],
   	  childItems : [],

   };
   var config = {
      CSS:{
         classes:{
         	widgetMain : 'accordion-categories-widget',
         	catChildrenParent : 'cat-children-parent',
         	catTopChildrenParent : 'cat-top-children-parent',         	
            catParentEmpty : 'cat-parent-empty',
            catList : 'cat-list',
            itemIconContainer : 'item-icon-container',
            isExpaned : 'expanded',
            visibleList : 'visible-list',
            invisibleList : 'invisible-list',
            catChild : 'cat-child',
            expnadIndicator : 'expand-indicator'
         },
         IDs:{
            catId : 'cat-id-%d'
         },
         fontawesome: {
            expand : 'fa-chevron-down',
            collapse : 'fa-chevron-up'
         },
         bootstrap : {
         	listGroupItem : 'list-group-item',
         	listGroup : 'list-group',
         	badge : 'badge',
         	activeItem : 'active'
         }
      },
      labels : {
         previous:'back',
         next:'next',
         auto:'play'
      },
      settings : {
         animationTime : 1500,
         animationType : 'easeOutSine',
         skin : 'default',
         bootstrapEnabled : true
      },
   };
   function initDefaults(){
   	 // Initalization default stuff
	   	 collectAllWidgetData();
	   	 setEmptyParentClickListener(_handleEmptyParentClick);
	     setTopChildrenParentListener(_handleChildrenParentClick);
	   	 setChildrenParentListener(_handleChildrenParentClick);
	     setChildItemsListener(_handleChildClick);
	     setReferenceClickListener(_handleReferenceClick);
   };
   // Private
   var _handleEmptyParentClick = function () {
   	window.location=$(this).find("a").attr("href");
     	return false;
   };
   var _handleChildClick = function () {
      window.location=$(this).find("a").attr("href");
      return false;
   };
   var _handleReferenceClick = function(e) {
      e.stopPropagation();
   }
   var _handleChildrenParentClick = function (e) {
         var currentItem = $(this);
         var firstList = currentItem.toggleClass(config.CSS.classes.isExpaned).children('ul').first();
         firstList.toggleClass(config.CSS.classes.visibleList + " " + config.CSS.classes.invisibleList);
         var icon = currentItem.find('.' + config.CSS.classes.expnadIndicator).first();
         icon.toggleClass(config.CSS.fontawesome.expand + ' ' + config.CSS.fontawesome.collapse);
         e.stopPropagation();
   }; 
   var _collectChildrenParentItems =  function() {
   		data.childrenParentItems = $('.'+ config.CSS.classes.widgetMain + ' .'+ config.CSS.classes.catChildrenParent);		
   };
   var _collectTopChildrenParentItems =  function() {
   		data.topChildrenParentItems = $('.'+ config.CSS.classes.widgetMain + ' .' + config.CSS.classes.catTopChildrenParent);
   };
   var _collectEmptyParentItems =  function(){
   		data.parentEmptyItems = $('.'+ config.CSS.classes.widgetMain + ' .' + config.CSS.classes.catParentEmpty);
   };
   var _collectChildItems =  function(){
   		data.childItems = $('.'+ config.CSS.classes.widgetMain + ' .' + config.CSS.classes.catChild);
   };
   // Public
   var collectAllWidgetData =  function() {
   		_collectChildrenParentItems();
   		_collectTopChildrenParentItems();   		
   		_collectEmptyParentItems();
   		_collectChildItems();
   }
   var setReferenceClickListener = function(listener) {
         $('.'+ config.CSS.classes.widgetMain + ' a').click(listener);
   }
   var setChildItemsListener = function (listener) {
   		data.childItems.click(listener);
   }
   var setChildrenParentListener = function (listener) {
   		data.childrenParentItems.click(listener);
   }
   var setTopChildrenParentListener = function (listener) {
   		data.topChildrenParentItems.click(listener);
   }   
   var setEmptyParentClickListener = function (listener) {
   		data.parentEmptyItems.click(listener);
   }
   // Return object with function defined above
   return {
   		collectAllWidgetData : collectAllWidgetData,
   		setChildItemsListener : setChildItemsListener ,
   		setEmptyParentClickListener : setEmptyParentClickListener ,
   		setChildrenParentListener : setChildrenParentListener ,
	   	config : config,
	   	initDefaults : initDefaults,
	   	data : data
   }
})(jQuery);
accordionCategoryModule.initDefaults();