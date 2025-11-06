/**
 * Copyright Agile Logix 
 * License: GNU General Public License v2 or later
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
 * and associated documentation files (the "Software"), to use, copy, modify, merge, publish, 
 * distribute, sublicense, and/or sell copies of the Software, subject to the following conditions:
 * 
 * 1. Redistributions of the Software must retain the above copyright notice, 
 *    this list of conditions, and the following disclaimer.
 * 
 * 2. No resale of the Software or its derivatives is permitted, whether for profit or otherwise, 
 *    without express written permission from the Author.
 * 
 * The Software is provided "AS IS", without warranty of any kind, express or implied, 
 * including but not limited to the warranties of merchantability, fitness for a particular purpose 
 * and noninfringement. In no event shall the authors or copyright holders be liable for any claim, 
 * damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of 
 * or in connection with the software or the use or other dealings in the software.
 */



/**
 * [asl_gdpr Enable the GDPR for the Store Locator]
 * @return {[type]} [description]
 */
var asl_gdpr = function(_accepted) {

    window['asl_async_callback'] = function(){
      asl_store_locator();
    };
  
    /**
     * [load_google_maps Load the Google Maps Async]
     * @return {[type]} [description]
     */
    function load_google_maps() {
  
      var script  = document.createElement('script');
      script.type = 'text/javascript';
      script.src  = 'https://maps.googleapis.com/maps/api/js?libraries=places,drawing&callback=asl_async_callback&key=' + asl_configuration.api_key;
      document.body.appendChild(script);
    };
  
  
    /**
     * [gdpr_accepted Once user approve it]
     * @return {[type]} [description]
     */
    function gdpr_accepted() {
  
      window.localStorage && window.localStorage.setItem('asl-gdpr', 1);
        
      //  disable it once accepted
      asl_configuration.gdpr = false;
  
      load_google_maps();
  
      //  remove the GDPR box
      jQuery('.asl-cont #sl-btn-gdpr').parent().parent().parent().remove();
    };
  
    //  Accepted by the Borlabs Cookies
    if(_accepted) {
      load_google_maps();
      return;
    }
  
    //  Accept the GDPR :: Simple
    jQuery('.asl-cont #sl-btn-gdpr').bind('click', gdpr_accepted);
    
    //  Check if already approved
    if(window.localStorage && window.localStorage.getItem('asl-gdpr') == '1') {
      gdpr_accepted();
    }
  };
  
  //  Call the GDPR
  if(asl_configuration.gdpr == '1') {
    asl_configuration.gdpr_enabled = true;
    asl_gdpr();
  }
  
  
  
  /**
   * [asl_store_locator Store Locator Main Function]
   * @return {[type]} [description]
   */
  function asl_store_locator() {
    
    //  when GDPR is enabled
    if(asl_configuration.gdpr == '1')return;
  
    //  Make sure the Google Maps is loaded
    if (!window['google'] || !google.maps) {
  
      if(!asl_configuration.gdpr_enabled)
        console.warn('Store Locator Error! Google Maps library is not loaded, check your cache plugin');
  
      return;
    }
  
    //  Multiple instance not allowed
    if(jQuery('.storelocator-main').length >= 2) {
      console.warn('Store Locator Error! Multiple instances of store locator loaded on the page.');
    }
  
    if (!window['_asl_map_customize']) {
      window['_asl_map_customize'] = null;
    }
  
    
    //  Don't allow to load twice
    if(asl_configuration.is_loaded) {
      return;
    }
  
    asl_configuration.is_loaded = true;
  
    //  the Locator instance
    var asl_locator = function() {};
  
  
  
    //info box library
    function InfoBox(e){e=e||{};google.maps.OverlayView.apply(this,arguments);this.content_=e.content||"";this.disableAutoPan_=e.disableAutoPan||false;this.maxWidth_=e.maxWidth||0;this.pixelOffset_=e.pixelOffset||new google.maps.Size(0,0);this.position_=e.position||new google.maps.LatLng(0,0);this.zIndex_=e.zIndex||null;this.boxClass_=e.boxClass||"infoBox";this.boxStyle_=e.boxStyle||{};this.closeBoxMargin_=e.closeBoxMargin||"2px";this.closeBoxURL_=e.closeBoxURL||"https://www.google.com/intl/en_us/mapfiles/close.gif";if(e.closeBoxURL===""){this.closeBoxURL_=""}this.infoBoxClearance_=e.infoBoxClearance||new google.maps.Size(1,1);if(typeof e.visible==="undefined"){if(typeof e.isHidden==="undefined"){e.visible=true}else{e.visible=!e.isHidden}}this.isHidden_=!e.visible;this.alignBottom_=e.alignBottom||false;this.pane_=e.pane||"floatPane";this.enableEventPropagation_=e.enableEventPropagation||false;this.div_=null;this.closeListener_=null;this.moveListener_=null;this.contextListener_=null;this.eventListeners_=null;this.fixedWidthSet_=null}InfoBox.prototype=new google.maps.OverlayView;InfoBox.prototype.createInfoBoxDiv_=function(){var e;var t;var n;var r=this;var i=function(e){e.cancelBubble=true;if(e.stopPropagation){e.stopPropagation()}};var s=function(e){e.returnValue=false;if(e.preventDefault){e.preventDefault()}if(!r.enableEventPropagation_){i(e)}};if(!this.div_){this.div_=document.createElement("div");this.setBoxStyle_();if(typeof this.content_.nodeType==="undefined"){this.div_.innerHTML=this.getCloseBoxImg_()+this.content_}else{this.div_.innerHTML=this.getCloseBoxImg_();this.div_.appendChild(this.content_)}this.getPanes()[this.pane_].appendChild(this.div_);this.addClickHandler_();if(this.div_.style.width){this.fixedWidthSet_=true}else{if(this.maxWidth_!==0&&this.div_.offsetWidth>this.maxWidth_){this.div_.style.width=this.maxWidth_;this.div_.style.overflow="auto";this.fixedWidthSet_=true}else{n=this.getBoxWidths_();this.div_.style.width=this.div_.offsetWidth-n.left-n.right+"px";this.fixedWidthSet_=false}}this.panBox_(this.disableAutoPan_);if(!this.enableEventPropagation_){this.eventListeners_=[];t=["mousedown","mouseover","mouseout","mouseup","click","dblclick","touchstart","touchend","touchmove"];for(e=0;e<t.length;e++){this.eventListeners_.push(this.div_.addEventListener(t[e],i))}this.eventListeners_.push(this.div_.addEventListener("mouseover",function(e){this.style.cursor="default"}))}this.contextListener_=this.div_.addEventListener("contextmenu",s);google.maps.event.trigger(this,"domready")}};InfoBox.prototype.getCloseBoxImg_=function(){var e="";if(this.closeBoxURL_!==""){e="<img";e+=" src='"+this.closeBoxURL_+"'";e+=" align=right";e+=" style='";e+=" position: relative;";e+=" cursor: pointer;";e+=" margin: "+this.closeBoxMargin_+";";e+="'>"}return e};InfoBox.prototype.addClickHandler_=function(){var e;if(this.closeBoxURL_!==""){e=this.div_.firstChild;this.closeListener_=e.addEventListener("click",this.getCloseClickHandler_())}else{this.closeListener_=null}};InfoBox.prototype.getCloseClickHandler_=function(){var e=this;return function(t){t.cancelBubble=true;if(t.stopPropagation){t.stopPropagation()}google.maps.event.trigger(e,"closeclick");e.close()}};InfoBox.prototype.panBox_=function(e){var t;var n;var r=0,i=0;if(!e){t=this.getMap();if(t instanceof google.maps.Map){if(!t.getBounds().contains(this.position_)){t.setCenter(this.position_)}n=t.getBounds();var s=t.getDiv();var o=s.offsetWidth;var u=s.offsetHeight;var a=this.pixelOffset_.width;var f=this.pixelOffset_.height;var l=this.div_.offsetWidth;var c=this.div_.offsetHeight;var h=this.infoBoxClearance_.width;var p=this.infoBoxClearance_.height;var d=this.getProjection().fromLatLngToContainerPixel(this.position_);if(d.x<-a+h){r=d.x+a-h}else if(d.x+l+a+h>o){r=d.x+l+a+h-o}if(this.alignBottom_){if(d.y<-f+p+c){i=d.y+f-p-c}else if(d.y+f+p>u){i=d.y+f+p-u}}else{if(d.y<-f+p){i=d.y+f-p}else if(d.y+c+f+p>u){i=d.y+c+f+p-u}}if(!(r===0&&i===0)){var v=t.getCenter();t.panBy(r,i)}}}};InfoBox.prototype.setBoxStyle_=function(){var e,t;if(this.div_){this.div_.className=this.boxClass_;this.div_.style.cssText="";t=this.boxStyle_;for(e in t){if(t.hasOwnProperty(e)){this.div_.style[e]=t[e]}}this.div_.style.WebkitTransform="translateZ(0)";if(typeof this.div_.style.opacity!=="undefined"&&this.div_.style.opacity!==""){this.div_.style.MsFilter='"progid:DXImageTransform.Microsoft.Alpha(Opacity='+this.div_.style.opacity*100+')"';this.div_.style.filter="alpha(opacity="+this.div_.style.opacity*100+")"}this.div_.style.position="absolute";this.div_.style.visibility="hidden";if(this.zIndex_!==null){this.div_.style.zIndex=this.zIndex_}}};InfoBox.prototype.getBoxWidths_=function(){var e;var t={top:0,bottom:0,left:0,right:0};var n=this.div_;if(document.defaultView&&document.defaultView.getComputedStyle){e=n.ownerDocument.defaultView.getComputedStyle(n,"");if(e){t.top=parseInt(e.borderTopWidth,10)||0;t.bottom=parseInt(e.borderBottomWidth,10)||0;t.left=parseInt(e.borderLeftWidth,10)||0;t.right=parseInt(e.borderRightWidth,10)||0}}else if(document.documentElement.currentStyle){if(n.currentStyle){t.top=parseInt(n.currentStyle.borderTopWidth,10)||0;t.bottom=parseInt(n.currentStyle.borderBottomWidth,10)||0;t.left=parseInt(n.currentStyle.borderLeftWidth,10)||0;t.right=parseInt(n.currentStyle.borderRightWidth,10)||0}}return t};InfoBox.prototype.onRemove=function(){if(this.div_){this.div_.parentNode.removeChild(this.div_);this.div_=null}};InfoBox.prototype.draw=function(){this.createInfoBoxDiv_();var e=this.getProjection().fromLatLngToDivPixel(this.position_);this.div_.style.left=e.x+this.pixelOffset_.width+"px";if(this.alignBottom_){this.div_.style.bottom=-(e.y+this.pixelOffset_.height)+"px"}else{this.div_.style.top=e.y+this.pixelOffset_.height+"px"}if(this.isHidden_){this.div_.style.visibility="hidden"}else{this.div_.style.visibility="visible"}};InfoBox.prototype.setOptions=function(e){if(typeof e.boxClass!=="undefined"){this.boxClass_=e.boxClass;this.setBoxStyle_()}if(typeof e.boxStyle!=="undefined"){this.boxStyle_=e.boxStyle;this.setBoxStyle_()}if(typeof e.content!=="undefined"){this.setContent(e.content)}if(typeof e.disableAutoPan!=="undefined"){this.disableAutoPan_=e.disableAutoPan}if(typeof e.maxWidth!=="undefined"){this.maxWidth_=e.maxWidth}if(typeof e.pixelOffset!=="undefined"){this.pixelOffset_=e.pixelOffset}if(typeof e.alignBottom!=="undefined"){this.alignBottom_=e.alignBottom}if(typeof e.position!=="undefined"){this.setPosition(e.position)}if(typeof e.zIndex!=="undefined"){this.setZIndex(e.zIndex)}if(typeof e.closeBoxMargin!=="undefined"){this.closeBoxMargin_=e.closeBoxMargin}if(typeof e.closeBoxURL!=="undefined"){this.closeBoxURL_=e.closeBoxURL}if(typeof e.infoBoxClearance!=="undefined"){this.infoBoxClearance_=e.infoBoxClearance}if(typeof e.isHidden!=="undefined"){this.isHidden_=e.isHidden}if(typeof e.visible!=="undefined"){this.isHidden_=!e.visible}if(typeof e.enableEventPropagation!=="undefined"){this.enableEventPropagation_=e.enableEventPropagation}if(this.div_){this.draw()}};InfoBox.prototype.setContent=function(e){this.content_=e;if(this.div_){if(this.closeListener_){google.maps.event.removeListener(this.closeListener_);this.closeListener_=null}if(!this.fixedWidthSet_){this.div_.style.width=""}if(typeof e.nodeType==="undefined"){this.div_.innerHTML=this.getCloseBoxImg_()+e}else{this.div_.innerHTML=this.getCloseBoxImg_();this.div_.appendChild(e)}if(!this.fixedWidthSet_){this.div_.style.width=this.div_.offsetWidth+"px";if(typeof e.nodeType==="undefined"){this.div_.innerHTML=this.getCloseBoxImg_()+e}else{this.div_.innerHTML=this.getCloseBoxImg_();this.div_.appendChild(e)}}this.addClickHandler_()}google.maps.event.trigger(this,"content_changed")};InfoBox.prototype.setPosition=function(e){this.position_=e;if(this.div_){this.draw()}google.maps.event.trigger(this,"position_changed")};InfoBox.prototype.setZIndex=function(e){this.zIndex_=e;if(this.div_){this.div_.style.zIndex=e}google.maps.event.trigger(this,"zindex_changed")};InfoBox.prototype.setVisible=function(e){this.isHidden_=!e;if(this.div_){this.div_.style.visibility=this.isHidden_?"hidden":"visible"}};InfoBox.prototype.getContent=function(){return this.content_};InfoBox.prototype.getPosition=function(){return this.position_};InfoBox.prototype.getZIndex=function(){return this.zIndex_};InfoBox.prototype.getVisible=function(){var e;if(typeof this.getMap()==="undefined"||this.getMap()===null){e=false}else{e=!this.isHidden_}return e};InfoBox.prototype.show=function(){this.isHidden_=false;if(this.div_){this.div_.style.visibility="visible"}};InfoBox.prototype.hide=function(){this.isHidden_=true;if(this.div_){this.div_.style.visibility="hidden"}};InfoBox.prototype.open=function(e,t){var n=this;if(t){this.position_=t.getPosition();this.moveListener_=google.maps.event.addListener(t,"position_changed",function(){n.setPosition(this.getPosition())})}this.setMap(e);if(this.div_){this.panBox_()}};InfoBox.prototype.close=function(){var e;if(this.closeListener_){google.maps.event.removeListener(this.closeListener_);this.closeListener_=null}if(this.eventListeners_){for(e=0;e<this.eventListeners_.length;e++){google.maps.event.removeListener(this.eventListeners_[e])}this.eventListeners_=null}if(this.moveListener_){google.maps.event.removeListener(this.moveListener_);this.moveListener_=null}if(this.contextListener_){google.maps.event.removeListener(this.contextListener_);this.contextListener_=null}this.setMap(null)};
  
  
    //mobile test
    function _isMobileDevice() {
      var check = (window.innerWidth < 768)? true: false;
      (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
      return check;
    };
  
  
    /**
     * [addHours Add the hours to the Date]
     * @param {[type]} h [description]
     */
    Date.prototype.addHours = function(h) {
      this.setTime(this.getTime() + (h*60*60*1000));
      return this;
    };
  
    /**
     * [subDays description]
     * @param  {[type]} h [description]
     * @return {[type]}   [description]
     */
    Date.prototype.subDays = function(h) {
      this.setTime(this.getTime() - (h*60*60*1000 * 24));
      return this;
    };
  
  
    /**
     * [getDecimal Get the decimal of the number]
     * @return {[type]} [description]
     */
    function getDecimal(_value) {
  
        return Math.ceil((_value - Math.floor(_value)) * 100)
    };
  
    //  Remove the Roboto when GDPR enabled
    if(asl_configuration.gdpr_enabled) {
  
      var head = document.getElementsByTagName('head')[0];
  
      // Save the original method
      var insertBefore = head.insertBefore;
  
      // Replace it!
      head.insertBefore = function (newElement, referenceElement) {
  
        if (newElement.href && newElement.href.indexOf('https://fonts.googleapis.com/css?family=Roboto') === 0) {
            return;
        }
  
        insertBefore.call(head, newElement, referenceElement);
      };
    }
  
    /**
     * [asl_drawing Code for ASL Drawing]
     * @type {Object}
     */
    var asl_drawing  = {
      shapes: [],
      shapes_index: 0,
      current_map: null,
      loadData: function(saved, _map) {
  
        var that = this;
        that.current_map = _map;
  
        for (var i in saved.shapes) {
          if (!saved.shapes[i]) continue;
  
          if (saved.shapes[i].type == 'polygon') {
            that.shapes.push(
              that.create_polygon.call(that, saved.shapes[i].coord, _map, saved.shapes[i])
            );
          } 
          else if (saved.shapes[i].type == 'polyline') {
  
            that.shapes.push(
              that.create_polyline.call(that, saved.shapes[i].coord, _map, saved.shapes[i])
            );
          }
          else if (saved.shapes[i].type == 'circle') {
            that.shapes.push(
              that.create_circle.call(that, saved.shapes[i], _map)
            );
          } else if (saved.shapes[i].type == 'rectangle') {
            that.shapes.push(
              that.create_rectangle.call(that, saved.shapes[i], _map)
            );
          }
        }
      },
      create_rectangle: function(_data) {
  
        var _map = this.current_map;
  
        return new google.maps.Rectangle({
          strokeColor: _data.strokeColor,
          fillColor: _data.color,
          strokeWeight: 1,
          type: 'rectangle',
          editable: (asl_drawing.allow_edit) ? asl_drawing.allow_edit : false,
          map: _map,
          bounds: new google.maps.LatLngBounds(
            new google.maps.LatLng(_data.sw[0], _data.sw[1]),
            new google.maps.LatLng(_data.ne[0], _data.ne[1]))
        });
      },
      create_circle: function(_data, _map) {
  
        var _map = this.current_map;
  
        return new google.maps.Circle({
          strokeColor: _data.strokeColor,
          fillColor: _data.color,
          type: 'circle',
          strokeWeight: 1,
          map: _map,
          editable: (asl_drawing.allow_edit) ? asl_drawing.allow_edit : false,
          center: new google.maps.LatLng(_data.center[0], _data.center[1]),
          radius: _data.radius
        });
  
      },
      create_polyline: function(_points, _map, _shape) {
  
        var _map = this.current_map;
  
        var coords = [];
  
        for (var i in _points) {
  
          coords.push({ lat: _points[i][0], lng: _points[i][1] });
        }
  
        return new google.maps.Polyline({
          path: coords,
          strokeColor: _shape.strokeColor || '#000000',
          strokeWeight: 3,
          editable: false, // Editable geometry off by default
          type: 'polyline',
          map: _map
        });
      },
      create_polygon: function(_points, _map, _shape) {
  
        var _map = this.current_map;
  
        var coords = [];
  
        for (var i in _points) {
  
          coords.push({ lat: _points[i][0], lng: _points[i][1] });
        }
  
        return new google.maps.Polygon({
          paths: coords,
          fillColor: _shape.color,
          strokeColor: _shape.strokeColor,
          strokeWeight: 1,
          editable: (asl_drawing.allow_edit) ? true : false, // Editable geometry off by default
          type: 'polygon',
          map: _map
        });
      }
    },
    ASL_CLOSE_BUTTON = '<button class="asl-search-clr asl-clear-btn hide" type="button"><svg width="12" height="12" viewBox="0 0 12 12" xmlns="https://www.w3.org/2000/svg"><path d="M.566 1.698L0 1.13 1.132 0l.565.566L6 4.868 10.302.566 10.868 0 12 1.132l-.566.565L7.132 6l4.302 4.3.566.568L10.868 12l-.565-.566L6 7.132l-4.3 4.302L1.13 12 0 10.868l.566-.565L4.868 6 .566 1.698z"></path></svg></button>',
    ASL_PICKUP_ROW   = (asl_configuration.pickup || asl_configuration.ship_from)? '<div class="sl-row mt-2 sl-pickup-row"><div class="pol"><a class="btn btn-block btn-asl sl-pickup">'+ ((asl_configuration.ship_from)? asl_configuration.words.ship_from: asl_configuration.words.pickup) +'</a></div></div>': null;
  
    /*ASL Script*/
    (function($) {
  
      //  Hack for the Template
      if (!$.templates) {
        $.templates = jQuery.templates;
      }
  
      
      if (!$.views) {
        $.templates = jQuery.views;
      }
  
  
      //  Enable Stars
      if($.views && $.views.tags) {
        
        $.views.tags("stars", function(rating) {
          if(!isNaN(rating)) {
            rating = (Math.round(parseFloat(rating) * 4) / 4);
            var starPercentage        = (rating / 5) * 100;
            var starPercentageRounded = (Math.round(starPercentage / 5) * 5) + '%';
            return '<span class="sl-stars"><div class="sl-stars-out icon-star"><div style="width:' + starPercentageRounded+'" class="sl-stars-in icon-star"></div></div></span>';
          }
        });
      }
  
    
      /**
       * [ASLConsole Take Over the Console]
       * @return {[type]} [description]
       */
      if(asl_configuration.debug == '1') {
        
        (function ASLConsole() {
          var console = window.console
          if (!console) return
  
          function intercept(method) {
              var original = console[method]
  
              console[method] = function() {
                 
                 // check message
                 if(arguments[0] && arguments[0].indexOf('Google') !== -1) {
  
                    var $err_msg = $('<div class="alert alert-danger asl-geo-err"></div>');
                    $err_msg.html(arguments[0]);
                    $err_msg.appendTo('.asl-cont .asl-map');
                    window.setTimeout(function() {
                      $err_msg.remove();
                    }, 5000);
                 }
                 
                  if (original.apply) {
                      // Do this for normal browsers
                      original.apply(console, arguments)
                  } else {
                      // Do this for IE
                      var message = Array.prototype.slice.apply(arguments).join(' ')
                      original(message)
                  }
              }
          }
          var methods = ['error'];
          for (var i = 0; i < methods.length; i++)
              intercept(methods[i])
        }());
      }
      
  
      /**
       * [hook_event Event HOOK]
       * @param  {[type]} _event_type [description]
       * @param  {[type]} _data       [description]
       * @return {[type]}             [description]
       */
      asl_locator.hook_event = function(_event) {
  
        //  bind the hook, init
        if(window['asl_event_hook'] && typeof window['asl_event_hook']  == 'function') {
          asl_event_hook.call(this, _event);
        }
      };
  
      /**
       * [add_clear_search Add the clear search]
       * @param {[type]} _input [description]
       */
      asl_locator.add_clear_button = function(_input) {
  
        var _clear_btn = $(ASL_CLOSE_BUTTON);
  
        _input.after(_clear_btn);
        
        //  Clear button Event
        _clear_btn.bind('click', function() {
  
          asl_view.search_text = asl_view._location = null;
  
          asl_view.clear_search(_input);
          _clear_btn.addClass('hide');
        });
  
        return _clear_btn;
      };
  
  
      /**
       * [save_analytics Save Analytics]
       * @param  {[type]} _store_or_loc [description]
       * @param  {[type]} _viewed       [description]
       * @return {[type]}               [description]
       */
      asl_locator.save_analytics = function(_store_or_loc, _viewed) {
  
      };
  
      asl_locator.toRad_ = function(degrees) {
        return degrees * Math.PI / 180;
      };
  
      asl_locator.Store = function(id, location, categories, props) {
        this.id_        = id;
        this.location_  = location;
        this.categories_ = categories /* || asl_locator.FeatureSet.NONE*/ ;
        this.props_ = props || {};
        this.v_id   = props.vendor_id;
      };
  
  
      asl_locator.Store.prototype.setMarker = function(marker) {
        this.marker_ = marker;
        google.maps.event.trigger(this, 'marker_changed', marker);
      };
  
      /**
       * Gets this store's Marker
       * @return {google.maps.Marker} the store's marker.
       */
      asl_locator.Store.prototype.getMarker = function() {
        return this.marker_;
      };
  
      /**
       * Gets this store's ID.
       * @return {string} this store's ID.
       */
      asl_locator.Store.prototype.getId = function() {
        return this.id_;
      };
  
      /**
       * Gets this store's location.
       * @return {google.maps.LatLng} this store's location.
       */
      asl_locator.Store.prototype.getLocation = function() {
        return this.location_;
      };
  
      /**
       * Gets this store's features.
       * @return {asl_locator.FeatureSet} this store's features.
       */
      /*
      //Not used
      asl_locator.Store.prototype.getStoreFeatures = function() {
      return this.categories_;
      };
      */
  
      /**
       * Checks whether this store has a particular feature.
       * @param {!asl_locator.Feature} feature the feature to check for.
       * @return {boolean} true if the store has the feature, false otherwise.
       */
      asl_locator.Store.prototype.hasCategory = function(featureID) {
  
        return this.categories_.indexOf(featureID) != -1;
      };
  
      /**
       * Checks whether this store has all the given features.
       * @param {asl_locator.FeatureSet} features the features to check for.
       * @return {boolean} true if the store has all features, false otherwise.
       */
      asl_locator.Store.prototype.hasAnyCategory = function(active_features) {
  
        if (!active_features.array_.length) {
          return asl_configuration.on_select;
        }
  
  
        var featureList = active_features.asList();
  
        for (var i = 0, ii = featureList.length; i < ii; i++) {
  
          if (this.categories_.indexOf(featureList[i].id_) != -1)
            return true;
  
          /*
            if (this.hasCategory(featureList[i].id_)) {
              return true;
            }
            */
        }
  
        return false;
      };
  
      /**
       * [hasAllCategory Checks whether this store has all the given features.]
       * @param  {[type]}  active_features [description]
       * @return {Boolean}                 [description]
       */
      asl_locator.Store.prototype.hasAllCategory = function(active_features) {
  
        if (!active_features.array_.length) {
          return asl_configuration.on_select;
        }
  
        var featureList = active_features.asList();
  
        for (var i = 0, ii = featureList.length; i < ii; i++) {
          
          if (this.categories_.indexOf(featureList[i].id_) == -1) {
            return false;
          }
        }
  
        return true;
      };
  
  
      /**
       * Gets additional details about this store.
       * @return {Object} additional properties of this store.
       */
      asl_locator.Store.prototype.getDetails = function() {
        return this.props_;
      };
  
      /**
       * Generates HTML for additional details about this store.
       * @private
       * @param {Array.<string>} fields the optional fields of this store to output.
       * @return {string} html version of additional fields of this store.
       */
      asl_locator.Store.prototype.generateFieldsHTML_ = function(fields) {
  
        var html = [];
  
        for (var i = 0, ii = fields.length; i < ii; i++) {
          var prop = fields[i];
  
          if (this.props_[prop]) {
            html.push('<div class="');
            html.push(prop);
            html.push('">');
            html.push(prop + ': ');
            html.push((isNaN(this.props_[prop])) ? this.props_[prop] : numberWithCommas(this.props_[prop]));
            html.push('</div>');
          }
        }
        return html.join('');
      };
  
      /**
       * Generates a HTML list of this store's features.
       * @private
       * @return {string} html list of this store's features.
       */
      asl_locator.Store.prototype.generateFeaturesHTML_ = function() {
  
        var html = [];
        html.push('<ul class="features">');
        var featureList = this.categories_.asList();
        for (var i = 0, feature; feature = featureList[i]; i++) {
          html.push('<li>');
          html.push(feature.getDisplayName());
          html.push('</li>');
        }
        html.push('</ul>');
        return html.join('');
      };
  
      /**
       * [getStoreContent description]
       * @return {[type]} [description]
       */
      asl_locator.Store.prototype.getStoreContent = function() {
  
        if (!this.content_) {
          // TODO(cbro): make this a setting?
  
          var tmpl = (window['asl_tmpl_list_item']) ? window['asl_tmpl_list_item'] : $.templates((window['asl_tmpls'] && window['asl_tmpls']['list'] || '#tmpl_list_item'));
          window['asl_tmpl_list_item'] = tmpl;
  
          this.props_.target = asl_configuration.target_blank;
  
          this.content_ = $(tmpl.render(this.props_));
  
  
          // When we have a pickup row
          if(asl_configuration.pickup || asl_configuration.ship_from) {
            this.content_.append(ASL_PICKUP_ROW);
          }
        }
  
        return this.content_;
      };
  
      /**
       * [advMkrContent Generate the content of the advanced marker]
       * @return {[type]} [description]
       */
      asl_locator.Store.prototype.advMkrContent = function() {
  
        var tmpl = (window['asl_tmpl_adv_mkr']) ? window['asl_tmpl_adv_mkr'] : $.templates((window['asl_tmpls'] && window['asl_tmpls']['adv_mkr'] || '#asl_tmpl_adv_mkr'));
  
        window['asl_tmpl_adv_mkr'] = tmpl;
  
        this.props_.target = asl_configuration.target_blank;
  
        //  Create the node
        const content     = document.createElement('div');
        content.className = 'adv-mkr-cont';
        
        //  Add the HTML
        content.innerHTML = tmpl.render(this.props_);
        
        return content;
      };
  
      /**
       * Gets the HTML content for this Store, suitable for use in an InfoWindow.
       * @return {string} a HTML version of this store.
       */
      asl_locator.Store.prototype.getcontent_ = function(store) {
  
        var tmpl = (window['asl_too_tip_tmpl']) ? window['asl_too_tip_tmpl'] : $.templates((window['asl_tmpls'] && window['asl_tmpls']['infobox'] || '#asl_too_tip'));
        window['asl_too_tip_tmpl'] = tmpl;
  
        store.props_.show_categories = asl_configuration.show_categories;
  
        var html_tip     = tmpl.render(store.props_);
        
        return html_tip;
      };
  
      /**
       * [getInfoWindowContent description]
       * @param  {[type]} stores [description]
       * @return {[type]}        [description]
       */
      asl_locator.Store.prototype.getInfoWindowContent = function(stores) {
  
        var html = '<div class="infoWindow" id="style_' + ((asl_configuration.infobox_layout) ? asl_configuration.infobox_layout : '1') + '">';
  
        html += this.getcontent_(this);
  
        html += '</div>';
  
        this.content_ = html;
  
        return this.content_;
      };
  
  
      /**
       * Keep a cache of InfoPanel items (DOM Node), keyed by the store ID.
       * @private
       * @type {Object}
       */
      asl_locator.Store.infoPanelCache_ = {};
  
      /**
       * Gets a HTML element suitable for use in the InfoPanel.
       * @return {Node} a HTML element.
       */
      asl_locator.Store.prototype.getInfoPanelItem = function() {
  
        var store = this;
        var cache = asl_locator.Store.infoPanelCache_;
        var key = store.id_;
  
  
  
        if (!cache[key]) {
          var content = store.getStoreContent();
          cache[key] = content[0];
  
        }
  
        return cache[key];
      };
  
      /**
       * Gets the distance between this Store and a certain location.
       * @param {google.maps.LatLng} point the point to calculate distance to/from.
       * @return {number} the distance from this store to a given point.
       * @license
       *  Latitude/longitude spherical geodesy formulae & scripts
       *  (c) Chris Veness 2002-2010
       *  www.movable-type.co.uk/scripts/latlong.html
       */
      asl_locator.Store.prototype.distanceTo = function(point) {
  
        var R = 6371; // mean radius of earth
        var location = this.getLocation();
        var lat1 = asl_locator.toRad_(location.lat());
        var lon1 = asl_locator.toRad_(location.lng());
        var lat2 = asl_locator.toRad_(point.lat());
        var lon2 = asl_locator.toRad_(point.lng());
        var dLat = lat2 - lat1;
        var dLon = lon2 - lon1;
  
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
          Math.cos(lat1) * Math.cos(lat2) *
          Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var _val = R * c;
  
        return (asl_configuration.distance_unit == 'Miles') ? _val * 0.621371 : _val;
      };
  
  
      ///////////////////////////////STORES END HERE///////////////////////////////
      asl_locator.View = function(map, data, opt_options) {
  
        this.map_ = map;
        this.data_ = data;
  
  
        this.settings_ = $.extend({
          'updateOnPan': true,
          'geolocation': false,
          'features': new asl_locator.FeatureSet
        }, opt_options);
  
        this.init_();
  
        google.maps.event.trigger(this, 'load');
        this.set('featureFilter', new asl_locator.FeatureSet);
  
        //  Active Marker
        if (asl_configuration.active_marker)
          this.active_marker = { m: null, picon: null, icon: new google.maps.MarkerImage(asl_configuration.PLUGIN_URL + "public/icon/active.png", null, null) };
  
        //  Icon Sizing & Label Origin
        if(asl_configuration.icon_size) {
  
          var sizes       = asl_configuration.icon_size.split('x'),
              size_width  = parseInt(sizes[0]), 
              size_height = parseInt(sizes[1]); 
  
  
          var label_origin = null;
  
          //  Label Origin
          if(asl_configuration.label_origin) {
  
            label_origin = asl_configuration.label_origin.split('x');
  
            label_origin[0] = parseInt(label_origin[0]);
            label_origin[1] = parseInt(label_origin[1]);
          }
          else
            label_origin = [size_width/2, ((size_height) / 2) - 10];
  
  
          var anchor = null;
  
          //  Anchor Position
          if(asl_configuration.infowin_anchor) {
  
            anchor = asl_configuration.infowin_anchor.split('x');
  
            anchor[0] = parseInt(anchor[0]);
            anchor[1] = parseInt(anchor[1]);
          }
          else
            anchor = [size_width/2, ((size_height) / 2) + 8];
  
  
          this.icon = {
            scaledSize: new google.maps.Size(size_width, size_height),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(anchor[0], anchor[1] ),
            labelOrigin:  new google.maps.Point(label_origin[0], label_origin[1])
          };
        }
  
  
        //  Content of the tooltip
        this.cat_in_tooltip = (asl_configuration.title_only == '1')? false: true;
      };
  
      asl_locator['View'] = asl_locator.View;
  
  
      asl_locator.View.prototype = new google.maps.MVCObject;
  
  
  
      /**
       * [clear_search Clear the search on Close_BUTTON]
       * @param  {[type]} _input_control [description]
       * @return {[type]}                [description]
       */
      asl_locator.View.prototype.clear_search = function(_input_control) {
  
  
        // dont fetch as it is clear search
        //this.halt_fetch = true;
  
        //  Search by the Name
        if(asl_configuration.search_type == '1') {
          this.filter_text = null;
        }
  
        this.prop_filter  = this._location = null;
        this.reset_measure(_input_control);
  
        //  Reset Map to same location where it was initially
        this.getMap().panTo(new google.maps.LatLng(asl_configuration.default_lat, asl_configuration.default_lng));
        this.getMap().setZoom(parseInt(asl_configuration.zoom));
  
        //  When Category accordion, reset the list as well
        if(asl_configuration.category_accordion) {
          
          var colapse_li = $('.asl-cont #asl-list li.item-state > a:not(.colisiond)');
          
          if(colapse_li[0]) {
  
            colapse_li.addClass('colisiond');
            colapse_li.next().removeClass('in');measure_dista
          }
        }
  
      };
  
      /*
        _no_refresh only works when user_center = 1
      */
      asl_locator.View.prototype.measure_distance = function(_latlng, _nearest, _no_refresh, _place_api) {
  
        var that = this;
  
        //  Clear the infobox
        this.clear_infobox();
  
        //  Delete Marker Cache, when Advanced markers are enabled clear the markers template as well
        if(asl_configuration.adv_mkr) {
  
          that.clearMarkers();
  
          delete that.markerCache_;
          that.markerCache_ = {};
        }
  
        var loc = new google.maps.LatLng(_latlng.lat(), _latlng.lng());
        that._panel.dest_coords = that.dest_coords = loc;
  
        //  disable the sort random
        if(asl_configuration.sort_random)
          asl_configuration.sort_random = false;
  
        //  First Load
        if (asl_configuration.first_load == '5') {
  
          if (!that.display_list) {
  
            $('.asl-cont.storelocator-main').removeClass('sl-search-only');
  
            that.display_list = true;
          }
        }
        else if (asl_configuration.first_load == '3') {
  
          if (!that.display_list) {
  
            $('.asl-cont.storelocator-main').removeClass('map-full');
  
            that.display_list = true;
          }
        }
        else if (asl_configuration.first_load == '4') {
  
          if (!that.list_shown && that.display_list) {
            $('.asl-cont.storelocator-main').removeClass('map-full');
            that.list_shown = true;
          }
  
          if (!that.display_list) {
            that.display_list = true;
          }
        }
  
  
        //var max_distance = asl_configuration.radius_range,
        var max_distance = 100,
          min_distance   = 1000,
          nearest_pin    = null;
  
        var unit_translation = (asl_configuration.distance_unit == 'KM') ? asl_configuration.words.Km : asl_configuration.words.Miles;
  
        //get distance from current
        for (var s in that.data_.stores_) {
  
          if (!that.data_.stores_.hasOwnProperty(s)) continue;
  
          var _distance = that.data_.stores_[s].distanceTo(that.dest_coords);
          that.data_.stores_[s].content_ = null;
  
  
          //change KM TO Miles
          that.data_.stores_[s].props_.distance = _distance;
          that.data_.stores_[s].props_.dist_str = asl_engine.helper.format_count(_distance) + ' ' + unit_translation;
  
  
          //console.log(that.data_.stores_[s].props_.city,'     ',that.data_.stores_[s].props_.state,'   ',that.data_.stores_[s].props_.postal_code);
  
          if (_distance > max_distance)
            max_distance = _distance;
  
          if (_distance < min_distance) {
  
            nearest_pin = that.data_.stores_[s];
            min_distance = _distance;
          }
        }
  
  
        asl_configuration.radius_range = (asl_configuration.fixed_radius) ? parseInt(asl_configuration.fixed_radius) : Math.ceil(max_distance);
  
  
        $('.asl-cont #asl-radius-input').html(asl_configuration.radius_range);
  
        delete asl_locator.Store.infoPanelCache_;
        asl_locator.Store.infoPanelCache_ = {};
  
        //my marker
        if (!that.my_marker) {
  
          that.my_marker = new google.maps.Marker({
            title: asl_configuration.words.your_cur_loc,
            position: loc,
            zIndex: 0,
            animation: google.maps.Animation.DROP,
            draggable: true,
            map: (asl_configuration.geo_marker == '0')? null: that.getMap()
          });
  
          //  Apply the Geo-Marker Image
          var geo_marker_image = asl_configuration.PLUGIN_URL + "public/icon/me-pin.png";
  
          if(asl_configuration.geo_marker_id && asl_markers[asl_configuration.geo_marker_id]) {
            geo_marker_image = asl_configuration.URL + 'icon/' + asl_markers[asl_configuration.geo_marker_id].icon;
          }
  
  
          var marker_icon = new google.maps.MarkerImage(geo_marker_image, null, null, null /*, new google.maps.Size(24,46)*/ );
          that.my_marker.setIcon(marker_icon);
  
          that.my_marker.addListener('dragend', function(_event) {
  
            that.measure_distance(_event.latLng);
          });
        } else
          that.my_marker.setPosition(loc);
  
  
  
        //Show the Nearest Store
        if (_nearest && asl_configuration.search_destin == '1' && nearest_pin) {
  
          loc = nearest_pin.getLocation();
        }
  
        /**
         * [radius_circle Create a Circle]
         * @param  {[type]} _location [description]
         * @return {[type]}           [description]
         */
        function radius_circle(_location) {
  
          var _radius = null;
  
          _radius = ((!asl_configuration.distance_slider || !asl_configuration.advance_filter) && !asl_configuration.fixed_radius) ? 100 : parseInt(asl_configuration.radius_range);
  
          //Change to KM
          _radius = _radius * 1000;
  
          //  Change Miles to KM Unit
          if (asl_configuration.distance_unit != 'KM') {
  
            _radius = _radius * 1.60934;
          }
  
          if (!that.$circle) {
  
            //  Create the Circle Control
            function circleStatus(controlDiv, map) {
  
              // Set CSS for the control border.
              $reset_btn = $('<div class="asl-radius-cnt"><div class="checkbox"><label for="asl-rad-circ"><input id="asl-rad-circ" checked type="checkbox">' + asl_configuration.words.radius_circle + '</label></div></div>');
              controlDiv.appendChild($reset_btn[0]);
  
              // Setup the click event listeners: simply set the map to Chicago.
              $reset_btn.find('#asl-rad-circ')[0].addEventListener('click', function() {
  
                that.$circle.setMap((this.checked) ? that.getMap() : null);
              });
            };
  
            //  Add the Circle Overlay
            that.$circle = new google.maps.Circle({
              strokeColor: asl_configuration.radius_color || '#FFFF00',
              strokeOpacity: 0.7,
              strokeWeight: 2,
              fillColor: asl_configuration.radius_color || '#FFFF00',
              fillOpacity: 0.20,
              map: that.getMap(),
              radius: _radius,
              center: _location
            });
          } 
          else {
  
            //  Change it
            that.$circle.setOptions({ radius: _radius, center: _location });
          }
  
          //  Make the Circle Radius
          that.getMap().fitBounds(that.$circle.getBounds());
        };
  
        //  For the Boundary Box
        if(asl_configuration.boundary_box) {
  
          //  no control needed
        }
        //For Dropdwon
        else if (asl_configuration.distance_slider & asl_configuration.advance_filter && asl_configuration.distance_control == '1') {
  
          var unit_translation = (asl_configuration.distance_unit == 'KM') ? asl_configuration.words.Km : asl_configuration.words.Miles;
          //Intialize Distance Dropdown
          if (!that.$dist_control) {
  
            var dropdown_opts = asl_configuration.dropdown_range.split(','),
              dropdown_html = '';
  
            for (var d = 0; d < dropdown_opts.length; d++) {
  
              var opt_val = dropdown_opts[d],
                selecti = (opt_val && opt_val[0] == '*') ? true : false;
  
              // Remove *
              if (selecti) {
  
                opt_val = opt_val.replace('*', '');
                dropdown_html += '<option selected="selected" value="' + opt_val + '">' + opt_val + ' ' + unit_translation + '</option>';
              } else
                dropdown_html += '<option value="' + opt_val + '">' + opt_val + ' ' + unit_translation + '</option>';
            }
  
  
            $('.asl-cont .range_filter').html('<div class="asl-filter-cntrl"><label class="asl-cntrl-lbl">' + asl_configuration.words.distance + '</label><div class="sl-dropdown-cont">\
              <select class="asl-dist-ddl">' + dropdown_html + '</select></div></div>')
              .removeClass('hide');
  
  
            // Template 1
            if(asl_configuration.template == '1') {
              $('.asl-cont .range_filter').prepend('<label>'+asl_configuration.words.in+'</label>');
            }
  
            that.$dist_control = $('.asl-cont .asl-dist-ddl');
  
            that.$dist_control.multiselect({
              enableFiltering: false,
              nonSelectedText: asl_configuration.words.select_distance,
              numberDisplayed: 1,
              maxHeight: (asl_configuration.ddl_max_height)?parseInt(asl_configuration.ddl_max_height): 250,
              /*onSelectAll: function(event) {
              },*/
              onChange: function(option, checked) {
  
                var rad_val = parseInt(option.val());
  
                if (isNaN(rad_val)) {
  
                  rad_val = 1000;
                  //rad_val = (asl_configuration.fixed_radius)?parseInt(asl_configuration.fixed_radius):1000;
                }
  
                asl_configuration.radius_range = rad_val;
                that.refreshView();
  
                // Change the Circle
                if (that.$circle) {
  
                  radius_circle(that.dest_coords);
                }
              }
            });
  
            asl_configuration.radius_range = that.$dist_control.val();
          } 
          else {
  
            asl_configuration.radius_range = that.$dist_control.val();
  
          }
        }
        //For Slider
        else if (asl_configuration.distance_slider & asl_configuration.advance_filter && asl_configuration.distance_control == '0') {
  
          $('.asl-cont .range_filter').removeClass('hide');
  
          //  Either Slider or Dropdown
          if (!that.$dist_control) {
  
            var slider_current_value = (asl_configuration.slider_val_radius)? parseInt(asl_configuration.slider_val_radius): asl_configuration.radius_range;
  
  
            that.$dist_control = jQuery(".asl-cont #asl-radius-slide")
              .aslSlider({
                value: slider_current_value,
                min: asl_configuration.slider_min_radius? parseInt(asl_configuration.slider_min_radius): 1,
                max: asl_configuration.radius_range
              })
              .on("slide", function(e) {
  
                $('#asl-radius-input').html(e.value);
                asl_configuration.radius_range = e.value;
              })
              .on("slideStop", function(value) {
  
                that.refreshView();
  
                // Change the Circle
                if (that.$circle) {
  
                  radius_circle(that.dest_coords);
                }
              });
  
              //  Current value of the slider
              asl_configuration.radius_range = slider_current_value;
          }
          //Set Max value
          else {
  
            that.$dist_control.aslSlider('setAttribute', 'max', asl_configuration.radius_range);
            that.$dist_control.aslSlider('setValue', asl_configuration.radius_range);
            $('#asl-radius-input').html(asl_configuration.radius_range);
          }
        }
  
        //  Radius Circle
        if (asl_configuration.radius_circle) {
  
          radius_circle(that.dest_coords);
          that.refreshView();
        }
        else if (!_no_refresh) {
  
          //  Bounding Box Search
          if(asl_configuration.search_zoom == '0' && that.bbox) {
  
            if(!that.bbox.isEmpty()) {
              that.getMap().fitBounds(that.bbox);
            }
          }
          else {
  
            that.getMap().setCenter(loc);
            that.getMap().setZoom((asl_configuration.search_zoom) ? parseInt(asl_configuration.search_zoom) : parseInt(asl_configuration.zoom));
            google.maps.event.trigger(that, 'load');
          }
  
          that.refreshView();
        }
  
  
        //  Hide the geoModal
        if(that._panel.geo_modal) {
          that._panel.hideGeoModal();        
        }
  
        return;
      };
  
  
      /**
       * [reset_measure Reset the Measure]
       * @param  {[type]} _input [description]
       * @return {[type]}        [description]
       */
      asl_locator.View.prototype.reset_measure = function(_input) {
  
        var that  = this;
  
        //  Clear the infobox
        this.clear_infobox();
  
        //  Delete Marker Cache, when Advanced markers are enabled clear the markers template as well
        if(asl_configuration.adv_mkr) {
  
          that.clearMarkers();
  
          delete that.markerCache_;
          that.markerCache_ = {};
        }
  
        that.bbox = that._panel.dest_coords = that.dest_coords = null;
  
        var max_distance  = asl_configuration.radius_range,
          min_distance    = 1000,
          nearest_pin     = null;
  
  
        //  Get distance from current
        for (var s in that.data_.stores_) {
  
          if (!that.data_.stores_.hasOwnProperty(s)) continue;
  
          that.data_.stores_[s].content_ = null;
  
          // Change KM TO Miles
          that.data_.stores_[s].props_.dist_str = that.data_.stores_[s].props_.distance = null;
        }
  
        //disable it in case of radius_range fixed
        //asl_configuration.radius_range = Math.round(max_distance);
        asl_configuration.radius_range = (asl_configuration.fixed_radius) ? parseInt(asl_configuration.fixed_radius) : Math.round(max_distance);
  
  
        $('.asl-cont #asl-radius-input').html(asl_configuration.radius_range);
  
        delete asl_locator.Store.infoPanelCache_;
  
  
        asl_locator.Store.infoPanelCache_ = {};
  
        //  Clear the Marker
        if (that.my_marker) {
  
          that.my_marker.setMap(null);
          delete that.my_marker;
          that.my_marker = null;
        }
  
        //  Clear the Circle
        if(that.$circle) {
  
          that.$circle.setMap(null);
          delete that.$circle;
          that.$circle = null;
        }
  
        //For Dropdwon
        if (asl_configuration.distance_slider & asl_configuration.advance_filter && asl_configuration.distance_control == '1') {
  
          //Destroy
          if (that.$dist_control) {
  
            that.$dist_control.multiselect('destroy');
            that.$dist_control = null;
          }
  
          $('.asl-cont .range_filter').addClass('hide');
  
        }
        //For Slider
        else if (asl_configuration.distance_slider & asl_configuration.advance_filter && asl_configuration.distance_control == '0') {
  
          $('.asl-cont .range_filter').addClass('hide');
  
          //Either Slider or Dropdown
          if (that.$dist_control) {
  
            that.$dist_control.aslSlider('destroy');
            that.$dist_control.removeData('aslSlider');
            that.$dist_control = null;
          }
        }
  
  
        that.refreshView();
  
        $(_input).val('');
      };
  
  
      /**
       * [add_search_text add text to search control]
       * @param  {[type]} _text [description]
       * @return {[type]}       [description]
       */
      asl_locator.View.prototype.add_search_text = function(_text) {
  
        var that = this;
  
        //  when we have a control, add text to it
        if(that._panel.search_control) {
  
          that._panel.search_control.value = _text;
  
          //  Show the close button
          $(that._panel.search_control).next().removeClass('hide');
        }
      };
  
  
      /**
       * [geolocate_ GeoLocate]
       * @return {[type]} [description]
       */
      asl_locator.View.prototype.geolocate_ = function() {
  
        var that = this;
  
        if (window.navigator && navigator.geolocation) {
  
          //  todo remove it
          //that.measure_distance(new google.maps.LatLng(51.6262, 1.080647));return;
  
          /**
           * [geo_error Geo-Location Error]
           * @param  {[type]} error [description]
           * @return {[type]}       [description]
           */
          function geo_error(error) {
  
            var $err_msg = $('<div class="alert alert-danger asl-geo-err"></div>');
            
            switch (error.code) {
  
              case 'http':
                $err_msg.html("Error! site is loading with HTTP connection");
              break;
              case error.PERMISSION_DENIED:
                $err_msg.html((asl_configuration.words.geo_location_error || error.message) || "User denied the request for Geolocation.");
                break;
              case error.POSITION_UNAVAILABLE:
                $err_msg.html("Location information is unavailable.");
                break;
              case error.TIMEOUT:
                $err_msg.html("The request to get user location timed out.");
                break;
              case error.UNKNOWN_ERROR:
                $err_msg.html("An unknown error occurred.");
                break;
              default:
                $err_msg.html(error.message);
            }
  
            $err_msg.appendTo('.asl-cont .asl-map');
            window.setTimeout(function() {
              $err_msg.remove();
            }, 5000);
          };
  
          //  Add the Error
          if(window.location.protocol == "http:") {
            geo_error({code: 'http'});return;
          }
  
          navigator.geolocation.getCurrentPosition(function(pos) {
  
              that.measure_distance(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));
  
              //  bind the hook, geolocation
              asl_locator.hook_event({type: 'geolocation', data: pos.coords});
  
              //  Add the text to it
              that.add_search_text(asl_configuration.words.current_location);
  
            },
            geo_error, ({
              maximumAge: 60 * 1000,
              timeout: 10 * 1000
            })
          );
        }
      };
  
  
      /**
       * [geo_service GEO JS IP Free]
       * @return {[type]} [description]
       */
      asl_locator.View.prototype.geo_service = function() {
  
        var that = this;
  
        var ip_geoservice = "https://get.geojs.io/v1/ip/geo.json";
  
        $.ajax({
          url: ip_geoservice,
          type: 'GET',
          dataType: 'json',
          success: function(geo_resp) {
  
            if(geo_resp && geo_resp.latitude && geo_resp.longitude) {
              
              var geo_lat   = parseFloat(geo_resp.latitude),
                  geo_lng   = parseFloat(geo_resp.longitude),
                  geo_coord = new google.maps.LatLng(geo_lat, geo_lng);
  
              that.measure_distance(geo_coord);
              
              //  bind the hook, geolocation
              asl_locator.hook_event({type: 'geolocation', data: geo_coord});
            }
          },
          error: function(e) {
  
            console.warn('Error Store Locator! GeoJS API: ', e);
          }
        });
      };
  
  
      /**
       * [clear_infobox Remove the infobox if visible]
       * @return {[type]} [description]
       */
      asl_locator.View.prototype.clear_infobox = function() {
  
        // Clear the previous infobox if exist
        if(this.get('selectedStore')) {
          this.highlight(null);
        }
      };
  
      /**
       * [fitBound Fit Bound to the Stores, asl_configuration.category_bound]
       * @return {[type]} [description]
       */
      asl_locator.View.prototype.fitBound = function(_stores) {
  
        var stores = _stores || this.get('stores');
  
        if(stores.length) {
  
          var bounds = new google.maps.LatLngBounds();
  
          for (var s in stores) {
  
            if (!stores.hasOwnProperty(s)) continue;
  
            bounds.extend(stores[s].getLocation());
  
          }
  
          //  Set a max bound zoom to avoid zoom in
          var bound_zoom = asl_configuration.max_bound_zoom;
  
          if(bound_zoom) {
            google.maps.event.addListenerOnce(this.getMap(), 'bounds_changed', function() { 
              this.setZoom(Math.min(bound_zoom, this.getZoom())); 
            });
          }
  
          //  Fit bound to those stores
          this.getMap().fitBounds(bounds);
        }
      };
  
  
  
  
      /**
       * [init_ View Init]
       * @return {[type]} [description]
       */
      asl_locator.View.prototype.init_ = function() {
  
        //If geolocation is enabled show distance
        if (this.settings_['geolocation']) {
          this.geolocate_();
        }
  
        this.markerCache_ = {};
  
        var infobox_width = asl_configuration.infobox_width || 320;
  
  
        //var closeURL        = 'https://www.google.com/intl/en_us/mapfiles/close.gif',
        var closeURL        = asl_configuration.PLUGIN_URL + ((asl_configuration.close_white)? "public/img/cross-white.png": "public/img/cross.png"),
            closeBoxMargin  = "11px 10px -27px 0px";
  
        //  For Template 0
        if(asl_configuration.infobox_layout == '2' || (asl_configuration.template == '1' && asl_configuration.infobox_layout == '0')) {
          closeURL = asl_configuration.PLUGIN_URL + "public/img/close-white.svg";
        }
        
  
  
        //closeURL = 'https://www.google.com/intl/en_us/mapfiles/close.gif';
  
        this.infoWindow_ = new InfoBox({
          //disableAutoPan: false,
          //pixelOffset: new google.maps.Size(-65, -140),
          boxStyle: {
            //width: "250px",
            //margin:'0 0 33px -120px'
            width: infobox_width + "px",
            margin: '0 0 '+asl_configuration.marker_height+'px -' + ((infobox_width / 2)) + 'px'
          },
          alignBottom: true,
          pane: false,
          disableAutoPan: true,
          closeBoxMargin: closeBoxMargin,
          closeBoxURL: closeURL,
          infoBoxClearance: new google.maps.Size(1, 1)
        });
  
        //infoBox,0 0 15px 27px
        var that = this;
        var map = this.getMap();
  
        this.set('updateOnPan', this.settings_['updateOnPan']); // true or false
  
        google.maps.event.addListener(this.infoWindow_, 'closeclick', function() {
          that.highlight(null);
        });
  
        google.maps.event.addListener(map, 'click', function() {
          that.highlight(null);
          that.infoWindow_.close();
        });
  
        //  Advanced Markers are enabled!
        if(asl_configuration.adv_mkr) {
  
          //  Cluster not supported
          asl_configuration.marker_label = asl_configuration.do_bounce = asl_configuration.active_marker = asl_configuration.cluster  = false;
          that.active_marker = '';
  
          /**
           * [createMarker Override the create marker function for Advanced Markers]
           * @param  {[type]} store [description]
           * @return {[type]}       [description]
           */
          this.createMarker = function(store) {
            
  
            var adv_marker = new google.maps.marker.AdvancedMarkerElement({
              content: store.advMkrContent(),
              position: store.getLocation(),
              title: store.props_.title
            });
            
            return adv_marker;
          };
  
          //  Missing methods
          google.maps.marker.AdvancedMarkerElement.prototype.getPosition = function() {return this.position;};
          google.maps.marker.AdvancedMarkerElement.prototype.getMap      = function() {return this.map;};
        }
      };
  
  
      /**
       * Adds/remove hooks as appropriate.
       */
      asl_locator.View.prototype.updateOnPan_changed = function() {
  
  
        if (this.updateOnPanListener_) {
          google.maps.event.removeListener(this.updateOnPanListener_);
        }
  
        var that = this;
        //validate the view
  
        if (this.get('updateOnPan') && this.getMap()) {
          var that = this,
            map = this.getMap(),
            updateOnPan_method = function(e) {
  
              if (asl_configuration.reset_button && $('.asl-reset-map')[0]) {
  
                if ($('.asl-reset-map')[0].style != 'block')
                  $('.asl-reset-map')[0].style.display = 'block';
              }
  
              //if(that.showing_direction || asl_configuration.load_all != '1')return;
              //closing this refreshview for testing :: 4-22-2017 :: majin
              
              if(asl_configuration.sort_by_bound) {
  
                that.refreshView();
              }
            };
          this.updateOnPanListener_ = google.maps.event.addListener(map, 'dragend', //idle
            updateOnPan_method
          );
        }
      };
  
      /**
       * Add a store to the map.
       * @param {asl_locator.Store} store the store to add.
       */
      asl_locator.View.prototype.addStoreToMap = function(store) {
  
        var marker = this.getMarker(store);
        store.setMarker(marker);
        var that = this;
  
        //Marker Click or mouse over event
  
        marker.clickListener_ = google.maps.event.addListener(marker, (asl_configuration.mouseover) ? 'mouseover' : 'click',
          function() {
  
            //  Redirect to the website URL instead of showing popup
            if(asl_configuration.click_redirect) {
  
              //  the link
              var link = store.props_[asl_configuration.click_redirect];
  
              //  redirect to that given link
              if(link) {
                window.location.href = link;
              }
              
              return;
            }
  
            //  Marker is clicked
            that.marker_clicked = true;
  
            that.halt_fetch     = true;
            that.marker_center  = marker.getPosition();
            that.highlight(store, false);
  
            if (_asl_map_customize && _asl_map_customize.marker_animations == 1)
              marker.setAnimation(google.maps.Animation.Xp);
          });
  
        if (marker.getMap() != this.getMap()) {
          marker.setMap(this.getMap());
  
          if (_asl_map_customize && _asl_map_customize.marker_animations == 1)
            marker.setAnimation(google.maps.Animation.Xp);
        }
      };
  
      /**
       * Create a marker for a store.
       * @param {asl_locator.Store} store the store to produce a marker for.
       * @this asl_locator.View
       * @return {google.maps.Marker} a new marker.
       * @export
       */
      asl_locator.View.prototype.createMarker = function(store) {
  
        var url             = asl_configuration.PLUGIN_URL + 'public/icon/',
            marker_category = asl_categories[store.categories_[0]];
  
        var marker_index    = 0;
  
        url += 'default.png';
  
        //  Marker title
        var marker_title = (asl_configuration.marker_title)? ((this.cat_in_tooltip && marker_category && marker_category['name'])?  marker_category['name'] + ' | ' + store.props_.title: store.props_.title): null;
  
        //http://image.flaticon.com/icons/svg/252/252025.svg
        //new google.maps.MarkerImage(url, null, null, null, markerSize);
        var marker_param = {
          title: asl_engine.helper.html_entites(marker_title),
          position: store.getLocation(),
          zIndex: asl_configuration.marker_index? marker_index: null,
          animation: (_asl_map_customize && _asl_map_customize.marker_animations == 1) ? google.maps.Animation.BOUNCE : null,
          icon: {
            url: url
          }
        };
  
        //  When we have icon
        if(this.icon) {
  
          //  copy the icon
          marker_param['icon']        = Object.assign({}, this.icon);
  
          //  add the url
          marker_param['icon']['url'] = url;
        }
  
        //  When you have Marker Label
        if(asl_configuration.marker_label && store.props_.label) {
  
          marker_param['label'] = {text: store.props_.label, color: asl_configuration.label_color || "#eb3a44",fontSize: "16px",fontWeight: "bold"};
         
        }
  
        return new google.maps.Marker(marker_param);
      };
  
      /**
       * Get a marker for a store. By default, this caches the value from
       * createMarker(store)
       * @param {asl_locator.Store} store the store to get the marker from.
       * @return {google.maps.Marker} the marker.
       */
      asl_locator.View.prototype.getMarker = function(store) {
  
        var cache = this.markerCache_;
        var key = store.id_;
  
        if (!cache[key]) {
  
          cache[key] = this['createMarker'](store);
        }
  
  
        return cache[key];
      };
  
      /**
       * Get a InfoWindow for a particular store.
       * @param {asl_locator.Store} store the store.
       * @return {google.maps.InfoWindow} the store's InfoWindow.
       */
      asl_locator.View.prototype.getInfoWindow = function(store, stores) {
  
        if (!store) {
          return this.infoWindow_;
        }
  
        var div = $(store.getInfoWindowContent(stores));
  
        this.infoWindow_.setContent(div[0]);
        return this.infoWindow_;
      };
  
      /**
       * Gets all possible features for this View.
       * @return {asl_locator.FeatureSet} All possible features.
       */
      asl_locator.View.prototype.getViewFeatures = function() {
  
        return this.settings_['features'];
      };
  
      /**
       * Gets a feature by its id. Convenience method.
       * @param {string} id the feature's id.
       * @return {asl_locator.Feature|undefined} The feature, if the id is valid.
       * undefined if not.
       */
      asl_locator.View.prototype.getFeatureById = function(id) {
  
        if (!this.featureById_) {
          this.featureById_ = {};
          var feature_list  = this.getViewFeatures().asList();
          
          for (var i = 0, feature; feature = feature_list[i]; i++) {
            this.featureById_[feature.id_] = feature;
          }
        }
        return this.featureById_[id];
      };
  
      /**
       * featureFilter_changed event handler.
       */
      asl_locator.View.prototype.featureFilter_changed = function() {
  
        google.maps.event.trigger(this, 'featureFilter_changed',
          this.get('featureFilter'));
  
        if (this.get('stores')) {
          this.clearMarkers();
        }
      };
  
      /**
       * Clears the visible markers on the map.
       */
      asl_locator.View.prototype.clearMarkers = function() {
  
        for (var marker in this.markerCache_) {
          this.markerCache_[marker].setMap(null);
          var listener = this.markerCache_[marker].clickListener_;
          if (listener) {
            google.maps.event.removeListener(listener);
          }
        }
      };
  
  
      /**
       * [storesWithCategory Get the Stores with the Category ID]
       * @param  {[type]} _category_id [description]
       * @return {[type]}              [description]
       */
      asl_locator.View.prototype.storesWithCategory = function(_category_id) {
  
        var stores = this.get('stores');
  
        var filtered = [];
  
        if (stores) {
  
          for (var i = 0, ii = stores.length; i < ii; i++) {
            
            if(stores[i].hasCategory(_category_id)) {
  
              filtered.push(stores[i]);
            }
          }
        }
  
        return filtered;
      };
  
  
      /**
       * [categoryClearAll Remove all the categories]
       * @return {[type]} [description]
       */
      asl_locator.View.prototype.categoryClearAll = function() {
  
        //  Get all the Stores with the Category ID li_cat_id
        //var stores = view.storesWithCategory(_category_id);
        var featureFilter_inst = this.get('featureFilter');
  
        for (var f in featureFilter_inst.array_) {
          featureFilter_inst.array_.pop();
        }
  
      };
  
      /**
       * [categoryAccFilter Category accordion filter, filter the stores]
       * @param  {[type]} _category_id [description]
       * @return {[type]}              [description]
       */
      asl_locator.View.prototype.categoryAccFilter = function(_category_id, _state) {
  
        //  Get all the Stores with the Category ID li_cat_id
        //var stores = view.storesWithCategory(_category_id);
        var featureFilter_inst = this.get('featureFilter');
  
        //  add the feature
        var feature = (_category_id && _state)? this.getFeatureById(_category_id): null;
  
        if(feature) {
          
          featureFilter_inst.add(feature);
        }
  
        this.set('featureFilter', featureFilter_inst);
        this.refreshView();
        this.fitBound();
      };
  
  
      /**
       * [bounce Bounce the Store Marker]
       * @param  {[type]} store   [description]
       * @param  {[type]} _status [description]
       * @return {[type]}         [description]
       */
      asl_locator.View.prototype.doBounce = function(store, _status) {
  
        //  No bounce with marker label
        if(asl_configuration.marker_label)return;
  
        var _marker = store.getMarker();
  
        _marker.setAnimation(null);
  
        if (_status) {
          //_marker.setZIndex(100);
          _marker.setAnimation(google.maps.Animation.BOUNCE);
        } else {
          //_marker.setZIndex(store.props_.ordr || 0);
        }
  
      };
  
  
      /**
       * [reloadLocator Reload the Store Locator based on the Parameters]
       * @param  {[type]} _params [description]
       * @return {[type]}         [description]
       */
      asl_locator.View.prototype.reloadLocator = function(_params, _callback) {
  
        //  Must be load all
        asl_configuration.load_all = '1';
  
        //   Remove all the markers
        this.clearMarkers();
  
            //  Cluster
        if (asl_configuration.cluster) {
  
          asl_locator.marker_clusters.clearMarkers();
        }
  
        // Reset Stores Immediately
        this.data_.stores_ = [];
  
        //  Clean current ones
        this.refreshView();
  
        delete asl_locator.Store.infoPanelCache_;
        asl_locator.Store.infoPanelCache_ = {};
  
        this.data_.fetch_remote_data(null, _params, _callback);
      };
  
      /**
       * Refresh the map's view. This will fetch new data based on the map's bounds.
       */
      asl_locator.View.prototype.refreshView = function() {
  
        var that = this,
          bounds = this.getMap().getBounds();
  
        console.log('CALLING REFRESH VIEW');
  
        //  1- brand-attr
        var attr_fields = {};
  
        //  When First Load is enabled, there must be distance or search address active
        if (!asl_configuration.first_load || asl_configuration.first_load != '1') {
            
          if(!that.dest_coords && !that.prop_filter) {
  
            //  No search is perform as coordinates are not there
            that.search_performed = false;
  
            that.set('stores', []);
            return;
          }
        }
  
        //  Yes, search is perform with coordinates
        that.search_performed = true;
  
        this.data_.getStores(bounds, (this.get('featureFilter')), function(stores) {
  
          var oldStores = that.get('stores');
  
          //  Remove all old Stores Markers
          if (oldStores) {
  
            for (var i = 0, ii = oldStores.length; i < ii; i++) {
              google.maps.event.removeListener(
                oldStores[i].getMarker().clickListener_);
            }
          }
  
          //  When Range slider is enabled
          var stores_in_range = [],
              priority_stores = [],
              filter_text     = null,
              has_distance    = (stores && stores[0] && stores[0].props_.dist_str)? true: false,
              check_distance  = false,
              bbox_check      = (asl_configuration.boundary_box && that.bbox)? true: false;
  
          //  When Store Radius is Enabled!
          var by_store_radius = false;
  
  
          //Refresh Category Stores Counts
          var c_keys = Object.keys(asl_categories);
  
          //  2- brand-attr
          var fields = asl_configuration.filter_ddl;
  
          for (var c in c_keys) {
  
            if (asl_categories[c_keys[c]])
              asl_categories[c_keys[c]].len = 0;
          }
  
  
          for (var c in c_keys) {
  
            if (asl_categories[c_keys[c]])
              asl_categories[c_keys[c]].len = 0;
          }
  
  
          /////////////////////////
          // Loop for the stores //
          /////////////////////////
          for (var k in stores) {
  
            if (!stores.hasOwnProperty(k)) continue;
  
            //  Temp store instance
            var _store = stores[k].props_;
  
  
            /////////////////////////
            //////Distance Testing //
            /////////////////////////
  
            //  Store Radius validation for each Store
            if(bbox_check) {
  
              if (!that.bbox.contains(stores[k].getLocation())) {
                  continue;
              }
            }
            else if (asl_configuration.advance_filter) {
  
              //If not valid continue
              if (check_distance && _store.distance >= asl_configuration.radius_range) {
                continue;
              }
  
            }
            //When Advance filter is OFF
            else if (asl_configuration.fixed_radius && _store.distance) {
  
              //If in Range
              if (_store.distance >= asl_configuration.fixed_radius) {
                continue;
              }
            }
  
       
            //  Else add into array
            if (_store.ordr > 0)
              priority_stores.push(stores[k]);
            else
              stores_in_range.push(stores[k]);   
          }
  
  
          //  Change the categories count
          if (asl_configuration.template == "2" && asl_configuration.advance_filter) {
  
            var $category_box = $('.asl-cont .asl-categories-list .round-box');
  
            $category_box.each(function(e) {
  
              $(this).attr('data-c', (asl_categories[$(this).attr('data-id')].len));
              this.children[0].children[1].children[0].children[1].innerHTML = '(' + asl_categories[$(this).attr('data-id')].len + ')';
            });
          }
  
          //  IF Priority Stores Exist, Sort them ?
          if (priority_stores.length > 0) {
  
            // Sort by distance first for the Prioirity Stores
            if(that.dest_coords)
              that.data_.sortDistance(that.dest_coords, priority_stores);
  
            //  Sort by the number, as second
            that.data_.sortByDesc('ordr', priority_stores);
          }
  
          
          ///////////////////
          /////// Sortings //
          ///////////////////
          
          if(asl_configuration.sort_random) {
            
            that.data_.sortRandom(stores_in_range);
          }
          else if (asl_configuration.sort_by) {
  
            that.data_.sortBy(asl_configuration.sort_by, stores_in_range);
          }
          //  Sorted by the distance
          else if (stores_in_range && that.dest_coords) {
  
            //  located Position
            that.data_.sortDistance(that.dest_coords, stores_in_range);
          } 
          //  sorted by the bound
          else if (bounds && asl_configuration.sort_by_bound)
            that.data_.sortDistance(bounds.getCenter(), stores_in_range);
  
  
          //  Merge Priority Stores
          if (priority_stores.length > 0) {
  
            // Merge it
            stores_in_range = priority_stores.concat(stores_in_range);
          }
  
  
          //  Apply the Limit
          if (asl_configuration.stores_limit) {
  
            stores_in_range = stores_in_range.slice(0, asl_configuration.stores_limit);
          }
  
          //  If has Marker Label OR Advanced Marker
          if((asl_configuration.adv_mkr ||  asl_configuration.marker_label) && has_distance) {
  
            var max_len = stores_in_range.length;
            //var max_len = (stores_in_range.length < 100)? stores_in_range.length: 100;
  
            for(var s = 0; s < max_len; s++) {
              stores_in_range[s].props_.label = String(s + 1);
            }
  
            //  Clear the marker cache
            delete that.markerCache_;
            that.markerCache_ = {};
          }
          
          //  Show a store when there are no stores within the range
          if(!stores_in_range.length && asl_configuration.default_store) {
  
            var default_store = stores.filter(function(x) {
              return x.props_.id === parseInt(asl_configuration.default_store);
            });
  
            stores_in_range = default_store;
          }
          
          //  Set new stores          
          that.set('stores', stores_in_range);
  
          //  Set the Stores in Panel
          that._panel.set('stores', [true]);
  
          //  Category bound
          if(asl_configuration.select_category && asl_configuration.category_bound) {
              
            //  When there is no default-addr
            if(!asl_configuration['default-addr'])
              that.fitBound(null);
  
            //  remove preselection
            asl_configuration.select_category = null;
          }
        });
      };
  
      /**
       * stores_changed event handler.
       * This will display all new stores on the map.
       * @this asl_locator.View
       */
      asl_locator.View.prototype.stores_changed = function() {
  
  
        var stores = this.get('stores'),
          current_markers = [];
  
        //  Clear old Markers When Cluster is OFF
        if (!asl_configuration.cluster)
          this.clearMarkers();
  
        for (var i = 0, store; store = stores[i]; i++) {
  
          this.addStoreToMap(store);
          current_markers.push(store.marker_);
        }
  
        if (asl_configuration.cluster) {
  
          asl_locator.marker_clusters.clearMarkers();
          asl_locator.marker_clusters.addMarkers(current_markers);
        }
      };
  
      /**
       * Gets the view's Map.
       * @return {google.maps.Map} the view's Map.
       */
      asl_locator.View.prototype.getMap = function() {
        return this.map_;
      };
  
      /**
       * [map_recenter Recenter the Map]
       * @param  {[type]} latlng  [description]
       * @param  {[type]} offsetx [description]
       * @param  {[type]} offsety [description]
       * @return {[type]}         [description]
       */
      asl_locator.View.prototype.map_recenter = function(latlng, offsetx, offsety) {
  
        var map = this.getMap();
        var point1 = map.getProjection().fromLatLngToPoint(
          (latlng instanceof google.maps.LatLng) ? latlng : map.getCenter()
        );
        var point2 = new google.maps.Point(
          ((typeof(offsetx) == 'number' ? offsetx : 0) / Math.pow(2, map.getZoom())) || 0,
          ((typeof(offsety) == 'number' ? offsety : 0) / Math.pow(2, map.getZoom())) || 0
        );
        map.panTo(map.getProjection().fromPointToLatLng(new google.maps.Point(
          point1.x - point2.x,
          point1.y + point2.y
        )));
      };
  
  
      /**
       * [highlightAdvMkr Highlight a Advanced Marker]
       * @param  {[type]} store         [description]
       * @param  {[type]} _item_clicked [description]
       * @return {[type]}               [description]
       */
      asl_locator.View.prototype.highlightAdvMkr = function(store, _item_clicked) {
  
        var _map       = this.getMap(),
            that       = this;
  
        //  Remove the highlight of previous selection
        var prev_selected = this.get('selectedStore');
  
        if(prev_selected) {
  
          //  Advance Marker instance
          var marker = prev_selected.getMarker();
  
          //  Set as Active
          $(marker.targetElement).removeClass('asl-mkr-actv');
  
          //  Reset the zIndex
          marker.zIndex = null;
        }
  
  
        //If valid Store
        if (store) {
  
  
          var stores = this.get('stores');
  
          //  when we have the zoom value
          if(asl_configuration.zoom_li)
            _map.setZoom(parseInt(asl_configuration.zoom_li));
          
          this.map_recenter(store.getLocation(), asl_configuration.info_x_offset, asl_configuration.info_y_offset);
  
          //  bind the hook, store highlight
          asl_locator.hook_event({type: 'select', data: store.props_});
  
          //map.getStreetView().setVisible(true)
          if (_map.getStreetView().getVisible()) {
            _map.getStreetView().setPosition(store.getLocation());
          }
  
          //  Advance Marker instance
          var marker = store.getMarker();
  
          //  Set as Active
          $(marker.targetElement).addClass('asl-mkr-actv');
  
          //  Set on the top
          marker.zIndex = 1;
          
        }
  
        this.set('selectedStore', store);
  
  
        
        //  Highlight the Store, Add Highlight Class
        if (!_item_clicked && store  && !asl_configuration.accordion) {
  
          window.setTimeout(function() {

            //  To avoid the scroll
            if(asl_configuration.disable_scroll)return;
  
            var sl_box = that._panel.mainPanel;
            var ele_   = that._panel.storeList_.find('.sl-item[data-id="' + store.id_ + '"]');          
            
            // Get scroll offset with type safety
            var scrollOffset = parseInt(asl_configuration.scroll_offset_y, 10) || 60; // Force to integer, default to 60

            var top_offset = ele_.position().top;

            top_offset = Math.max(top_offset - scrollOffset, 0);
  
            if (ele_[0])
              sl_box.animate({
                //scrollTop: top_offset + sl_box[0].scrollTop
                scrollTop: top_offset
              }, 'fast');
  
  
          }, 500);
        }
      };
  
      /**
       * Select a particular store.
       * @param {asl_locator.Store} store the store to highlight.
       * @param {boolean} pan if panning to the store on the map is desired.
       */
      asl_locator.View.prototype.highlight = function(store, _item_clicked) {
  
        var infoWindow = null,
            _map       = this.getMap(),
            that       = this;
  
  
  
        //  For Advanced Markers
        if(asl_configuration.adv_mkr) {
          return this.highlightAdvMkr(store, _item_clicked);
        }
  
  
        //If valid Store
        if (store) {
  
          var stores = this.get('stores');
          infoWindow = this.getInfoWindow(store, stores);
  
  
          if (store.getMarker()) {
  
            var _this = this,
              m = store.getMarker();
  
            infoWindow.open(_map, store.getMarker());
  
            //Store viewed
            if (asl_configuration.analytics) {
  
              asl_locator.save_analytics(store, 1);
            }
          }
          else {
            infoWindow.setPosition(store.getLocation());
            infoWindow.open(_map);
          }
  
          
          _map.setZoom(parseInt(asl_configuration.zoom_li));
          this.map_recenter(store.getLocation(), asl_configuration.info_x_offset, asl_configuration.info_y_offset);
  
  
          //  bind the hook, store highlight
          asl_locator.hook_event({type: 'store', data: store.props_});
  
          //map.getStreetView().setVisible(true)
          if (_map.getStreetView().getVisible()) {
            _map.getStreetView().setPosition(store.getLocation());
          }
  
        } 
        else {
          this.getInfoWindow().close();
  
          //  bind the hook, store highlight
          asl_locator.hook_event({type: 'clear', data: null});
        }
  
        this.set('selectedStore', store);
  
        //  Highlight the Store, Add Highlight Class
        if (!_item_clicked && store) {
  
          window.setTimeout(function() {

            //  To avoid the scroll
            if(asl_configuration.disable_scroll)return;
  
            var sl_box = that._panel.mainPanel;
            var ele_   = that._panel.storeList_.find('.sl-item[data-id="' + store.id_ + '"]');          
            
            // Get scroll offset with type safety
            var scrollOffset = parseInt(asl_configuration.scroll_offset_y, 10) || 60; // Force to integer, default to 60

            var top_offset = ele_.position().top;

            top_offset = Math.max(top_offset - scrollOffset, 0);
  
            if (ele_[0])
              sl_box.animate({
                //scrollTop: top_offset + sl_box[0].scrollTop
                scrollTop: top_offset
              }, 'fast');
          }, 500);
        }
  
      };
  
      asl_locator.View.prototype.selectedStore_changed = function() {
        google.maps.event.trigger(this, 'selectedStore_changed', this.get('selectedStore'));
      };
  
      asl_locator.ViewOptions = function() {};
  
      /**
       * Whether the map should update stores in the visible area when the visible
       * area changes. <code>refreshView()</code> will need to be called
       * programatically. Defaults to true.
       * @type boolean
       */
      asl_locator.ViewOptions.prototype.updateOnPan;
  
      /**
       * Whether the store locator should attempt to determine the user's location
       * for the initial view. Defaults to true.
       * @type boolean
       */
      asl_locator.ViewOptions.prototype.geolocation;
  
      /**
       * All available store features. Defaults to empty FeatureSet.
       * @type asl_locator.FeatureSet
       */
      asl_locator.ViewOptions.prototype.features;
  
      /**
       * The icon to use for markers representing stores.
       * @type string|google.maps.MarkerImage
       */
      asl_locator.ViewOptions.prototype.markerIcon;
  
  
      /**
       * Representation of a feature of a store. (e.g. 24 hours, BYO, etc).
       * @example <pre>
       * var feature = new asl_locator.Feature('24hour', 'Open 24 Hours');
       * </pre>
       * @param {string} id unique identifier for this feature.
       * @param {string} name display name of this feature.
       * @constructor
       * @implements asl_locator_Feature
       */
      asl_locator.Feature = function(id, name, img, _s) {
  
        this.id_      = id;
        this.name_    = name;
        this.img_     = img;
        this.order    = _s;
      };
  
      asl_locator['Feature'] = asl_locator.Feature;
  
      /**
       * Gets this Feature's ID.
       * @return {string} this feature's ID.
       */
      asl_locator.Feature.prototype.getId = function() {
        return this.id_;
      };
  
      /**
       * Gets this Feature's display name.
       * @return {string} this feature's display name.
       */
      asl_locator.Feature.prototype.getDisplayName = function() {
        return this.name_;
      };
  
      asl_locator.Feature.prototype.toString = function() {
        return this.getDisplayName();
      };
  
  
  
      asl_locator.FeatureSet = function(var_args) {
  
        /**
         * Stores references to the actual Feature.
         * @private
         * @type {!Array.<asl_locator.Feature>}
         */
        this.array_ = [];
  
        /**
         * Maps from a Feature's id to its array index.
         * @private
         * @type {Object.<string, number>}
         */
        this.hash_ = {};
  
        for (var i = 0, feature; feature = arguments[i]; i++) {
          this.add(feature);
        }
      };
  
      asl_locator['FeatureSet'] = asl_locator.FeatureSet;
  
      /**
       * Adds the given feature to the set, if it doesn't exist in the set already.
       * Else, removes the feature from the set.
       * @param {!asl_locator.Feature} feature the feature to toggle.
       */
      asl_locator.FeatureSet.prototype.toggle = function(feature) {
  
        if (this.hash_[feature.id_]) {
          this.remove(feature);
        } else {
  
          this.add(feature);
        }
  
      };
  
      /**
       * Adds a feature to the set.
       * @param {asl_locator.Feature} feature the feature to add.
       */
      asl_locator.FeatureSet.prototype.add = function(feature) {
  
        if (!feature) {
          return;
        }
  
        this.array_.push(feature);
        this.hash_[feature.id_] = 1;
      };
  
      /**
       * Removes a feature from the set, if it already exists in the set. If it does
       * not already exist in the set, this function is a no op.
       * @param {!asl_locator.Feature} feature the feature to remove.
       */
      asl_locator.FeatureSet.prototype.remove = function(feature) {
  
        var _feature_id = feature.id_;
  
        if (!this.hash_[_feature_id]) {
          return;
        }
  
        delete this.hash_[_feature_id];
        this.array_ = this.array_.filter(function(e) { return e && e.id_ != _feature_id });
      };
  
      /**
       * Get the contents of this set as an Array.
       * @return {Array.<!asl_locator.Feature>} the features in the set, in the order
       * they were inserted.
       */
      asl_locator.FeatureSet.prototype.asList = function() {
  
        var filtered = [];
        for (var i = 0, ii = this.array_.length; i < ii; i++) {
          var elem = this.array_[i];
          if (elem !== null) {
            filtered.push(elem);
          }
        }
        return filtered;
      };
  
      /**
       * Empty feature set.
       * @type asl_locator.FeatureSet
       * @const
       */
      asl_locator.FeatureSet.NONE = new asl_locator.FeatureSet;
  
  
      /**
       * An info panel, to complement the map view.
       * Provides a list of stores, location search, feature filter, and directions.
       * @example <pre>
       * var container = document.getElementById('panel');
       * var panel = new asl_locator.Panel(container, {
       *   view: view,
       *   locationSearchLabel: 'Location:'
       * });
       * google.maps.event.addListener(panel, 'geocode', function(result) {
       *   geocodeMarker.setPosition(result.geometry.location);
       * });
       * </pre>
       * @param {!Node} el the element to contain this panel.
       * @param {asl_locator.PanelOptions} opt_options
       * @constructor
       * @implements asl_locator_Panel
       */
      asl_locator.Panel = function(el, opt_options) {
  
        
        var that = this;
  
        this.el_ = $(el);
  
        this.el_.addClass('asl_locator-panel');
        this.settings_ = $.extend({
          'locationSearch': true,
          'locationSearchLabel': 'Enter Location/ZipCode: ',
          'featureFilter': true,
          'directions': true,
          'view': null
        }, opt_options);
  
        this.directionsRenderer_ = new google.maps.DirectionsRenderer({
          draggable: true
        });
        this.directionsService_ = new google.maps.DirectionsService;
  
  
        //lookhere
        opt_options.view._panel = this; 
  
        this.init_();
  
  
        //  Print button event
        $('.asl-cont .asl-print-btn').bind('click', function(e) {
  
          var print_cont_id = "asl-list";
  
          //  make use of the direction for printing
          if($('#asl-list .panel-inner').css('display') == 'none') {
            
            print_cont_id = 'asl-rendered-dir';
            $('#asl-list').find('.rendered-directions').attr('id', print_cont_id);
          }
  
  
          var print_params = {
            printable: print_cont_id, 
            type: "html",
            css: asl_configuration.PLUGIN_URL + 'public/css/print.css'
          };
  
          //  Add the Header
          if(asl_configuration.print_header) {
            print_params['header'] = asl_configuration.print_header;
          }
  
          printJS(print_params);
        });
      };
  
      asl_locator['Panel'] = asl_locator.Panel;
  
      asl_locator.Panel.prototype = new google.maps.MVCObject;
  
      /**
       * Initialise the info panel
       * @private
       */
      asl_locator.Panel.prototype.init_ = function() {
  
        var that = this;
        this.itemCache_ = {};
  
        if (this.settings_['view']) {
          this.set('view', this.settings_['view']);
        }
  
        this.filter_ = $('.asl-cont .header-search');
  
        // this.el_.parent().prepend(this.filter_);
        var view  = that.get('view');
        var map   = view.getMap();
        
        //When Clusters
        if (asl_configuration.cluster) {
  
          asl_locator.marker_clusters = new MarkerClusterer(map, [], {
            maxZoom: parseInt(asl_configuration.cluster_max_zoom) || 9,
            gridSize: parseInt(asl_configuration.cluster_grid_size) || 90,
            styles: [{width: 30,height: 30,className: "asl-cluster-1"},{width: 40,height: 40,className: "asl-cluster-2"},{width: 50,height: 50,className: "asl-cluster-3"}],
            clusterClass: "asl-cluster"
            //imagePath: asl_configuration.URL + 'icon/m'
          });
  
        }
  
        if (this.settings_['locationSearch']) {
  
          this.locationSearch_ = this.filter_;
       
          if (typeof google.maps.places != 'undefined') {
  
            var _input_txt = $('.asl-cont #auto-complete-search,.asl-cont .asl-search-address')[0];
            //Add search to the stores name
            if (asl_configuration.search_type == '0') {
              this.initAutocomplete_(_input_txt);
            }
  
            //  Add the Geocoder for the Additional Search
            if(asl_configuration.additional_search) {
              this.geoCoder(_input_txt);
            }
          } 
          else {
            this.filter_.submit(function() {
              that.searchPosition($('input', that.locationSearch_).val());
            });
          }
  
          this.filter_.submit(function() {
            return false;
          });
  
          google.maps.event.addListener(this, 'geocode', function(place) {
            //console.log('===> site_script.js ===> GEOCODE IS INVOKED');
            if (that.searchPositionTimeout_) {
              window.clearTimeout(that.searchPositionTimeout_);
            }
            if (!place.geometry) {
              //console.log('===> site_script.js ===> SEARCHING FOR POSITION');
              that.searchPosition(place.name);
              return;
            }
  
            this.directionsFrom_ = place.geometry.location;
  
            if (that.directionsVisible_) {
              that.renderDirections_();
            }
            
            var sl = that.get('view');
            
            sl.highlight(null);
            
            var map = sl.getMap();
            
            if (place.geometry.viewport) {
              map.fitBounds(place.geometry.viewport);
            }
            else {
              map.setCenter(place.geometry.location);
              map.setZoom(parseInt(asl_configuration.zoom_li));
            }
  
            sl.refreshView();
  
            that.listenForStoresUpdate_();
          });
        }
  
  
        //Feature Sets
        if (this.settings_['featureFilter']) {
  
          // TODO(cbro): update this on view_changed
          var _this = this;
          this.featureFilter_ = $('.asl-cont #filter-options');
          this.featureFilter_.show();
  
          //show the filters
          if (!asl_configuration.show_categories) {
  
            $('.asl-cont .sl-category-filter.drop_box_filter').remove();
          }
  
  
          if (asl_configuration.advance_filter)
            $('.asl-cont .asl-advance-filters').removeClass('hide');
  
  
          if (!asl_configuration.radius_range)
            asl_configuration.radius_range = (asl_configuration.fixed_radius) ? parseInt(asl_configuration.fixed_radius) : 1000;
  
  
  
          var tswitch = $('#asl-open-close');
          //time_switch:: asl open close
          if (asl_configuration.time_switch && tswitch[0]) {
            
            tswitch[0].checked = false;
            tswitch.bind('change', function(e) {
  
              asl_configuration.show_opened = this.checked;
              that.get('view').refreshView();
            });
          }
          else
            $('.asl-cont .Status_filter').remove();
  
          
          var allFeatures = this.get('view').getViewFeatures().asList();
          var $inner      = this.featureFilter_.find('.inner-filter');
  
  
  
          this.storeList_ = this.el_.find('.sl-list');
          this.SListCont_ = this.el_.find('.asl-panel-inner');
          this.mainPanel  = this.el_.find('.sl-main-cont-box');
  
  
          //  IF category is enabled
          allFeatures       =  (asl_configuration.cat_sort == 'name_')? asl_engine.helper.sortBy(allFeatures, 'name_', true): asl_engine.helper.sortBy(allFeatures, 'order');
        
        }
  
  
        this.directionsPanel_ = $('.asl-cont #agile-modal-direction');
  
        //Distance Slider
        var $direction_from = this.directionsPanel_.find('.frm-place');
        $direction_from.val('');
  
        if (that.dest_coords)
          that_.directionsFrom_ = that.dest_coords;
  
        var input_search = this.directionsPanel_.find('.frm-place')[0];
        this.input_search = new google.maps.places.Autocomplete(input_search);
  
        //  Apply the restriction
        var fields = ['geometry'];
        this.input_search.setFields(fields);
  
  
        var that_ = this;
        google.maps.event.addListener(this.input_search, 'place_changed',
          function() {
            that_.directionsFrom_ = this.getPlace().geometry.location;
          });
  
        this.directionsPanel_.find('.directions-to').attr('readonly', 'readonly');
        //this.directionsPanel_.hide();
        this.directionsVisible_ = false;
  
        //Direction Submit button
        this.directionsPanel_.find('.btn-submit').click(function(e) {
  
          if (that.dest_coords && $direction_from.val() == asl_configuration.words.current_location)
            that.directionsFrom_ = that.dest_coords || null;
  
          that.renderDirections_();
  
          return false;
        });
  
        if (asl_configuration.distance_unit == 'KM') {
  
          that.distance_type = google.maps.UnitSystem.METRIC;
          that.directionsPanel_.find('#rbtn-km')[0].checked = true;
        } 
        else
          that.distance_type = google.maps.UnitSystem.IMPERIAL;
  
        that.directionsPanel_.find('input[name=dist-type]').change(function() {
  
          that.distance_type = (this.value == 1) ? google.maps.UnitSystem.IMPERIAL : google.maps.UnitSystem.METRIC;
        });
  
  
        // Close button for the Direction panel
        this.el_.find('.directions-cont .close').click(function() {
          
          that.hideDirections();
          $('.asl-cont .count-row').removeClass('hide');
          $('.asl-cont #filter-options').removeClass('hide');
        });
  
  
        //  Close button for the Direction Panel
        this.directionsPanel_.find('.close-directions').click(function() {
          
          that.hideDirections();
          $('.asl-cont .count-row').removeClass('hide');
          $('.asl-cont #filter-options').removeClass('hide');
        });
  
        
        //  bind the hook, init
        asl_locator.hook_event({type: 'init', data: view.data_.stores_});
      };
  
  
  
  
  
      /**
       * Toggles a particular feature on/off in the feature filter.
       * @param {asl_locator.Feature} feature The feature to toggle.
       * @private
       */
      asl_locator.Panel.prototype.toggleFeatureFilter_ = function(feature) {
  
        var featureFilter = this.get('featureFilter');
        featureFilter.toggle(feature);
        
        this.set('featureFilter', featureFilter);
      };
  
      /**
       * Global Geocoder instance, for convenience.
       * @type {google.maps.Geocoder}
       * @private
       */
      asl_locator.geocoder_ = new google.maps.Geocoder;
  
      /**
       * Triggers an update for the store list in the Panel. Will wait for stores
       * to load asynchronously from the data source.
       * @private
       */
      asl_locator.Panel.prototype.listenForStoresUpdate_ = function() {
  
        var that = this;
        var view = (this.get('view'));
        if (this.storesChangedListener_) {
          google.maps.event.removeListener(this.storesChangedListener_);
        }
  
  
        ////console.log('listenForStoresUpdate_');
        /*
        this.storesChangedListener_ = google.maps.event.addListenerOnce(view,
          'stores_changed',
          function() {
            that.set('stores', view.get('stores'));
          });
        */
      };
  
      /**
       * Search and pan to the specified address.
       * @param {string} searchText the address to pan to.
       */
      asl_locator.Panel.prototype.searchPosition = function(searchText) {
  
        var that = this;
        var request = {
          address: searchText,
          bounds: this.get('view').getMap().getBounds()
        };
        asl_locator.geocoder_.geocode(request, function(result, status) {
          if (status != google.maps.GeocoderStatus.OK) {
            //TODO(cbro): proper error handling
            return;
          }
          google.maps.event.trigger(that, 'geocode', result[0]);
        });
      };
  
      /**
       * Sets the associated View.
       * @param {asl_locator.View} view the view to set.
       */
      asl_locator.Panel.prototype.setView = function(view) {
  
        this.set('view', view);
      };
  
      /**
       * view_changed handler.
       * Sets up additional bindings between the info panel and the map view.
       */
      asl_locator.Panel.prototype.view_changed = function() {
  
  
        ////console.log('VIEW CHANGED...........###########');
        var that = this;
        var sl = (this.get('view'));
        this.bindTo('selectedStore', sl);
  
        //window['test_panel'] = that;
  
        if (this.geolocationListener_) {
          google.maps.event.removeListener(this.geolocationListener_);
        }
  
        if (this.zoomListener_) {
          google.maps.event.removeListener(this.zoomListener_);
        }
  
        if (this.idleListener_) {
          google.maps.event.removeListener(this.idleListener_);
        }
  
        var center = sl.getMap().getCenter();
  
        var updateList = function() {
  
          //sl.clearMarkers();
          that.listenForStoresUpdate_();
        };
  
        //TODO(cbro): somehow get the geolocated position and populate the 'from' box.
  
        this.geolocationListener_ = google.maps.event.addListener(sl, 'load', updateList);
  
        this.zoomListener_ = google.maps.event.addListener(sl.getMap(), 'zoom_changed', updateList);
  
  
        this.idleListener_ = google.maps.event.addListener(sl.getMap(),
          'idle',
          function() {
  
            return that.idle_(sl.getMap());
          });
  
        updateList();
        
        this.bindTo('featureFilter', sl);
  
        if (this.autoComplete_) {
          this.autoComplete_.bindTo('bounds', sl.getMap());
        }
      };
  
  
      /**
       * [geoCoder Geocoder method]
       * @param  {[type]} _input    [description]
       * @param  {[type]} _callback [description]
       * @return {[type]}           [description]
       */
      asl_locator.Panel.prototype.geoCoder = function(_input, _callback) {
  
        var that  = this;
        var view  = that.get('view');
  
        var geocoder  = new google.maps.Geocoder(),
            _callback = _callback || function(results, status) {
  
            if (status == 'OK') {
              
              //  Add the search text
              results.search_text = _input.value;
  
              //  Get the bbox
              view.bbox = (results[0].geometry && results[0].geometry.viewport)? results[0].geometry.viewport: null;
              
              //  bind the hook, place api search
              asl_locator.hook_event({type: 'before_search', data: results});
  
              view.measure_distance(results[0].geometry.location, true, null, results);
  
              //  bind the hook, geocoder
              asl_locator.hook_event({type: 'search', data: results});
  
              //trigger Click of scan button
              if (asl_configuration.load_all == '2') {
                $('.asl-reload-map').trigger('click');
              }
  
              //  Show the close button
              $(_input).next().removeClass('hide');
  
            } else {
              console.log('Geocode was not successful for the following reason: ' + status);
            }
          };
  
  
  
        /**
         * [do_geocoding Do a Geocoding Request]
         * @param  {[type]} _addr_value [description]
         * @return {[type]}             [description]
         */
        function do_geocoding(addr_value) {
  
          if (addr_value){
  
            //birmingham
            var search_param = { 'address': addr_value };
  
            //  Restrict the search
            if (asl_configuration.country_restrict) {
            
              var country_restricted = asl_configuration.country_restrict.toLowerCase();
  
              country_restricted = country_restricted.split(',');
  
              search_param['componentRestrictions'] = {
                country: country_restricted[0]
              };
            }
  
            geocoder.geocode(search_param, _callback);
          }
        };
  
        // Clear to Write
        $(_input).bind('click', function(e) {
  
          //  Select existing text on the click
          _input.select();
        });
  
        //Enter Key
        $(_input).bind('keyup', function(e) {
  
          if (e.keyCode == 13) {
  
            var addr_value = $.trim(this.value);
  
            do_geocoding(addr_value);
          }
        });
  
        //  Pre-address, through Search Widget
        if(_input && asl_configuration['default-addr']) {
  
          window.setTimeout(function(){
  
          // req_coords
          if (asl_configuration.req_coords) {
  
            do_geocoding(asl_configuration["default-addr"]);
          }
          else {
  
            //  Geo-coder callback, trigger through search widget coordinates
            that.get('view').measure_distance(new google.maps.LatLng(asl_configuration.default_lat, asl_configuration.default_lng), true, null, null);
  
            //trigger Click of Scan button
            if (asl_configuration.load_all == '2') {
              $('.asl-reload-map').trigger('click');
            }
          }
  
          //  Show the close button
          $(_input).next().removeClass('hide');
  
          }, 800);
        }
  
  
        //Make it Search Button
        $('.asl-cont .icon-search').bind('click', function(e) {
  
          var addr_value = $.trim(_input.value);
  
          if (addr_value)
            geocoder.geocode({ 'address': addr_value }, _callback);
        });
      };
  
      /**
       * Adds autocomplete to the input box.
       * @private
       */
      asl_locator.Panel.prototype.initAutocomplete_ = function(input) {
  
        var that = this;
  
        if (!asl_configuration.geocoding_only) {
  
          var options = {};
  
          if (asl_configuration.google_search_type) {
  
            options['types'] = (asl_configuration.google_search_type == 'cities' || asl_configuration.google_search_type == 'regions') ? ['(' + asl_configuration.google_search_type + ')'] : [asl_configuration.google_search_type];
          }
  
          this.autoComplete_ = new google.maps.places.Autocomplete(input, options);
  
          if (asl_configuration.country_restrict) {
              
            var country_restricted = asl_configuration.country_restrict.toLowerCase();
  
            country_restricted = country_restricted.split(',');
  
            this.autoComplete_.setComponentRestrictions({country: country_restricted });
          }
  
            
          //  Limit the search scope
          var fields = ['geometry'];
  
          if(asl_configuration.filter_address)
            fields.push('address_components');
  
          this.autoComplete_.setFields(fields);
  
  
          if (this.get('view')) {
            this.autoComplete_.bindTo('bounds', this.get('view').getMap());
          }
  
  
          google.maps.event.addListener(this.autoComplete_, 'place_changed',
          function() {
            
            var p = this.getPlace();
  
            if (asl_configuration.analytics) {
              
              //  Add the address when API doesn't provide it
              if(!p.formatted_address) {
                p.formatted_address = input.value;
              }
  
  
              //Send a request and save the log
              asl_locator.save_analytics(p);
            }
           
            // lucia
            if (p.geometry) {
  
              var view = that.get('view');
              
              //  Get the bbox for place api
              view.bbox = (p.geometry && p.geometry.viewport)? p.geometry.viewport: null;
  
              //  bind the hook, place api search
              asl_locator.hook_event({type: 'before_search', data:p});
  
              //  measure the distance
              view.measure_distance(p.geometry.location, true, null, p);
  
              //  bind the hook, place api search
              asl_locator.hook_event({type: 'search', data:p});
  
              //  show the cross button
              $(input).next().removeClass('hide');
  
              //trigger Click of Scan button
              if (asl_configuration.load_all == '2') {
  
                $('.asl-reload-map').trigger('click');
              }
            }
  
            //trigger geocode
            //google.maps.event.trigger(that, 'geocode', p);
          });
  
        }
  
        //  The search control
        that.search_control = input;
        
        //  Entery Key
        that.geoCoder(input);
      };
  
      /**
       * Called on the view's map idle event. Refreshes the store list if the
       * user has navigated far away enough.
       * @param {google.maps.Map} map the current view's map.
       * @private
       */
      asl_locator.Panel.prototype.idle_ = function(map) {
  
        if (!this.center_) {
          this.center_ = map.getCenter();
        } else if (!map.getBounds().contains(this.center_)) {
          this.center_ = map.getCenter();
          this.listenForStoresUpdate_();
        }
      };
  
  
  
      /**
       * [hideGeoModal Hide the Geo Modal]
       * @return {[type]} [description]
       */
      asl_locator.Panel.prototype.hideGeoModal = function() {
  
        var $agile_modal = $('.asl-cont #asl-geolocation-agile-modal');
        
        $agile_modal.removeClass('in');
        
        window.setTimeout(function() {
          
          $agile_modal.css('display', 'none');
  
        }, 300);
  
        //  Visible
        this.geo_modal = false;
      };
  
  
      /**
       * [hideDescModal Hide the description Modal]
       * @return {[type]} [description]
       */
      asl_locator.Panel.prototype.hideDescModal = function() {
  
        var $desc_modal =  $('.asl-cont #asl-desc-agile-modal');
  
        $desc_modal.removeClass('in');
        
        window.setTimeout(function() {
          $desc_modal.css('display', 'none');
        }, 300);
  
        //  visibility
        this.isDescModal = false;
      }
  
      /**
       * [descriptionModal Show a description Modal]
       * @param  {[type]} _store [description]
       * @return {[type]}        [description]
       */
      asl_locator.Panel.prototype.descriptionModal = function(_store) {
  
        var $modal =  $('.asl-cont #asl-desc-agile-modal');
  
        $modal.find('.sl-title').html(_store.props_.title);
  
        var desc_text = '<h5>'+asl_configuration.words.desc_title+ '</h5><p>' +_store.props_.description + '</p>';
  
        if(_store.props_.description_2) {
            
            desc_text += '<br><h5 class="sl-addit-desc">'+asl_configuration.words.add_desc_title+'</h5><p>' + _store.props_.description_2 + '</p>';
        }
        
        $modal.find('.sl-desc').html(desc_text);
  
        $modal.css('display', 'block');
  
        $modal.addClass('in');
  
  
        if (asl_configuration.is_mob) {
  
          $('html, body')
            .stop()
            .animate({
              'scrollTop': $modal.offset().top
            }, 900, 'swing');
        }
  
        //  Modal Visibility
        this.isDescModal = true;
      };
  
  
      /**
       * Handler for stores_changed. Updates the list of stores.
       * @this asl_locator.Panel
       */
      asl_locator.Panel.prototype.stores_changed = function(_store) {
  
        var that = this;
  
        //  Hide the desc Modal if Visible
        if(that.isDescModal) {
          that.hideDescModal();
        }
  
        if (!this.get('stores')) {
          return;
        }
  
        var view = this.get('view');
  
        if (view.showing_direction) return;
  
        ///////////////////////////
        //Once for the Accordion //
        ///////////////////////////
        if (asl_configuration.accordion) {
          if (view.is_updated) return;
        }
  
        view.is_updated = true;
  
  
        var bounds = view && view.getMap().getBounds();
        //var stores = this.get('stores');
        var stores = view.get('stores');
        var selectedStore = this.get('selectedStore');
  
  
        //var stores = this.get('stores');
        //var stores = asl_panel.get('view').get('stores');
  
        //  Highlight the Clicked Element, Make it first
        if (asl_configuration.highlight_first && _store) {
  
          var high_arr = [],
            low_arr = [];
          for (var s = 0; s < stores.length; s++) {
  
            if (stores[s].id_ == _store.id_)
              high_arr.push(stores[s]);
            else
              low_arr.push(stores[s]);
          }
  
          stores = high_arr.concat(low_arr);
        }
  
  
        if (!asl_configuration.accordion)
          this.storeList_.empty();
  
        if (!stores.length) {
  
          $('.asl-cont .Num_of_store .count-result').html('0');
          that.storeList_.html('<div class="asl-overlay-on-item" id="asl-no-item-found"><div class="white"></div><h1 class="h1">' + ((view.search_performed)? asl_configuration.no_item_text: asl_configuration.words.perform_search) + '</h1></div>');
  
          //  When no stores are found
          asl_locator.hook_event({type: 'no_stores', data: {element: that.storeList_}});
        } 
        else {
  
          $('.asl-cont .Num_of_store .count-result').html(stores.length);
        }
  
        /**
         * [clickHandler description]
         * @param  {[type]} e [description]
         * @return {[type]}   [description]
         */
        var clickHandler = function(e) {
  
          var $e = $(e.target);
  
          if ($e.hasClass('s-direction')) {
            e.preventDefault();
            return;
          }
  
          if ($e.hasClass('sl-link')) {
            e.preventDefault();
  
            that.descriptionModal(this['store']);
            return;
          }
  
          if ($e.hasClass('sl-pickup')) {
            
            e.preventDefault();
  
            asl_locator.hook_event({type: ((asl_configuration.ship_from)? 'ship_from': 'pickup'), data: this['store']});
            return;
          }
  
          //  Hide the desc Modal if Visible
          if(that.isDescModal) {
            that.hideDescModal();
          }
          
          //  Hide the geoModal
          if(that.geo_modal) {
            that.hideGeoModal();        
          }
  
          //  Click is disabled
          if (e.target.className == 'A' || asl_configuration.disable_list_click) {
  
            //  bind the hook, store highlight
            asl_locator.hook_event({type: 'highlight', data: this['store']});
            return;
          }
  
          view.noRefreshList = true;
          view.highlight(this['store'], true);
  
          //  Scroll to the Map if Mobile
          if (asl_configuration.is_mob) {
  
            $('html, body')
              .stop()
              .animate({
                'scrollTop': $(view.getMap().getDiv()).offset().top
              }, 900, 'swing');
  
          }
        };
        //  End of the Event
  
  
  
  
        // TODO(cbro): change 10 to a setting/option
        //add the stores and the click event    
        for (var i = 0, ii = stores.length; i < ii; i++) {
  
          var storeLi = stores[i].getInfoPanelItem();
          storeLi['store'] = stores[i];
          var store_inst = stores[i];
  
  
          if (selectedStore && stores[i].id_ == selectedStore.id_) {
            $(storeLi).addClass('highlighted');
          }
  
          /*
          //  Deprecated
          if (!storeLi.clickHandler_) {
            storeLi.clickHandler_ = google.maps.event.addDomListener(storeLi, asl_configuration.list_event, clickHandler);
          }
          */
         
          storeLi.addEventListener(asl_configuration.list_event, clickHandler);
  
          if(asl_configuration.do_bounce) {
  
            storeLi.addEventListener('mouseenter', view.doBounce.bind(view, stores[i], true));
            storeLi.addEventListener('mouseleave', view.doBounce.bind(view, stores[i], false));
          }
          //The direction link on panel
          //direction Click Handler
          $(storeLi).find('.s-direction').click(function(e) {
            var store = $(this).data('_store');
            that.directionsTo_ = store;
            that.showDirections(store);
          })
          .data('_store', stores[i]);
  
          that.storeList_.append(storeLi);
        }
  
  
        /// Open the Category Accordion Markers
        if(asl_configuration.category_accordion) {
  
          //  Bind Event for the Category Li-State Click
          $('.asl-cont li.item-state').bind('click', function(e) {
  
            var $li_target = $(e.target).parent();
  
            //  is it accordion?
            if($li_target.hasClass('item-state')) {
              
              var li_cat_id = $(this).data('id'),
                  li_state  = $(this).children(0).hasClass('colisiond');
  
  
              //  Close the other listings
              asl_view._panel.storeList_.find('.show').colision('hide');
  
              //  Remove existing ones
              asl_view.categoryClearAll();
  
              if(li_cat_id) {
                
                view.categoryAccFilter(String(li_cat_id), li_state);
              }
            }
          });
  
          //  When it is shown, highlight the store
          /*
          that.storeList_.on('shown.bs.colision', function () {
  
          });
          */
        }
  
  
        //  Make sure the storeList_ has offset 0
        if(that.mainPanel.scrollTop() > 0) {
  
          that.mainPanel
              .stop()
              .animate({
                'scrollTop': 0
              }, 100, 'swing');
        }
      };
  
      /**
       * Handler for selectedStore_changed. Highlights the selected store in the
       * store list.
       * @this asl_locator.Panel
       */
      asl_locator.Panel.prototype.selectedStore_changed = function() {
  
        var that  = this;
        var store = this.get('selectedStore');
        var view  = that.get('view');
  
        var $highlighted = $('.highlighted', this.storeList_),
            skip_slideUp = false;
  
        //  is a marker click?
        var is_marker_click = view.marker_clicked;
  
        //  Reset it to false
        view.marker_clicked = false;
  
  
  
        //  Remove the Highlighted Class
        $highlighted.removeClass('highlighted');
  
  
        //  Remove the active_marker active state
        if (!asl_configuration.adv_mkr && view.active_marker && view.active_marker.m) {
  
          view.active_marker.m.setIcon(view.active_marker.picon);
          view.active_marker.m = null;
        }
  
        if (!store) {
          return;
        }
  
  
        // majin icon change here
        var store_marker = store.getMarker();
  
        // Backup Store marker
        if (view.active_marker) {
  
          view.active_marker.picon = store_marker.getIcon();
          view.active_marker.m = store_marker;
          store_marker.setIcon(view.active_marker.icon);
        }
  
  
        this.directionsTo_ = store;
        var store_li = this.storeList_.find('li.sl-item[data-id="' + store.id_ + '"]');
  
        //  Must be single node
        if(store_li.length > 1) {
          store_li = store_li.eq(0);
        }
  
        //  the selected store
        if (store_li) {
  
  
          store_li.addClass('highlighted');
        }
  
        if (this.settings_['directions']) {
          this.directionsPanel_.find('.directions-to')
            .val(store.getDetails().title);
        }
  
        var node = that.get('view').getInfoWindow().getContent();
        var directionsLink = $('<a/>')
          .text(asl_configuration.words.direction)
          .attr('href', 'javascript:void(0)')
          .addClass('action')
          .addClass('directions');
  
        // TODO(cbro): Make these two permanent fixtures in InfoWindow.
        // Move out of Panel.
        var zoomLink = $('<a/>')
          .text(asl_configuration.words.zoom)
          .attr('href', 'javascript:void(0)')
          .addClass('action')
          .addClass('zoomhere');
  
  
        var link = store.props_['link'];
  
  
        directionsLink.click(function() {
  
          that.showDirections();
          return false;
        });
  
        zoomLink.click(function() {
  
          that.get('view').getMap().setOptions({
            center: store.getLocation(),
            zoom: (asl_map.getZoom() + 1)
          });
        });
  
  
        $(node).find('.asl-buttons')
          .append(directionsLink)
          .append(zoomLink);
  
        //  Add Pickup Button
        if(asl_configuration.pickup || asl_configuration.ship_from) {
  
          var $pickup_btn = $('<a/>')
          .text(((asl_configuration.ship_from)? asl_configuration.words.ship_from: asl_configuration.words.pickup))
          .addClass('action')
          .addClass('sl-pickup');
  
          $(node).find('.asl-buttons').append($pickup_btn);
  
          $pickup_btn.click(function(e) {
            asl_locator.hook_event({type: ((asl_configuration.ship_from)? 'ship_from': 'pickup'), data: store});
          });
        }
  
  
        var streetViewLink = $('<a/>').text(asl_configuration.words.detail).addClass('action').addClass('a-website');
        
        //  If have an event hook for it
        if(window['asl_website_click']) {
          streetViewLink.click(function(){
            asl_website_click(store.props_, link);
          });
  
          $(node).find('.asl-buttons').append(streetViewLink);
        }
        //   If have a link for it
        else if (link) {
  
          $(node).find('.asl-buttons').append(streetViewLink);
  
          streetViewLink.attr('href', link);
  
          streetViewLink.attr('target', asl_configuration.target_blank);
        }
  
      };
  
      /**
       * Hides the directions panel.
       */
      asl_locator.Panel.prototype.hideDirections = function() {
  
        this.directionsVisible_ = false;
        this.directionsPanel_.removeClass('in');
        this.el_.find('.directions-cont').addClass('hide');
        //this.featureFilter_.fadeIn();
        this.storeList_.fadeIn();
        this.directionsRenderer_.setMap(null);
  
        var view = this.get('view');
        view.showing_direction = false;
      };
  
      /**
       * Shows directions to the selected store.
       */
      asl_locator.Panel.prototype.showDirections = function(_store) {
  
        var store = _store || this.get('selectedStore');
        if (!store) return;
  
        //  bind the hook, store highlight
        asl_locator.hook_event({type: 'direction', data: store.props_});
  
        //_isMobileDevice()
        if ((asl_configuration.is_mob && asl_configuration.direction_redirect == '1') || asl_configuration.direction_redirect == '2') {
  
          var destin = (asl_configuration.title_in_dir)? [store.props_.title]:[],
              source = '';
  
          destin.push(store.props_.address);
          destin = destin.join(', ').replace(/<\/?[^>]+(>|$)/g," ");
  
          //  Fix the URL for the Direction
          destin = encodeURIComponent( destin );
  
          //  Direction with the Coordinates
          if(asl_configuration.coords_direction) {
            destin = store.location_.lat() + ',' + store.location_.lng();
          }
  
          //https://developers.google.com/maps/documentation/urls/ios-urlscheme
          var _path = "https://www.google.com/maps/dir/?api=1&destination=" + destin;
          //var path   = "https://www.google.com/maps/dir/Current+Location/"+destin+"?hl=en";
          window.open(_path);
          return;
        }
  
        this.directionsPanel_.find('.frm-place').val((this.dest_coords) ? asl_configuration.words.current_location : '');
  
        
        this.directionsPanel_.find('.directions-to').val(store.getDetails().title);
        this.directionsPanel_.addClass('in');
        this.renderDirections_();
  
        //  When the direction box is press in the mobile, show the map
        if(asl_configuration.is_mob) {
  
          //  Scroll to the map
          $('html, body')
              .stop()
              .animate({
                'scrollTop': $(this.get('view').getMap().getDiv()).offset().top
              }, 900, 'swing');
        }
  
        this.directionsVisible_ = true;
      };
  
      /**
       * Renders directions from the location in the input box, to the store that is
       * pre-filled in the 'to' box.
       * @private
       */
      asl_locator.Panel.prototype.renderDirections_ = function() {
  
        var that = this;
  
        if (!this.directionsFrom_ || !this.directionsTo_) {
          return;
        }
  
        this.el_.find('#map-loading').show();
  
        this.el_.find('.directions-cont').removeClass('hide');
        this.storeList_.fadeOut();
        that.directionsPanel_.removeClass('in');
        var rendered = this.el_.find('.rendered-directions').empty();
  
  
        //  Travel Mode
        var travel_mode = google.maps['DirectionsTravelMode'].DRIVING;
  
        //  Override it
        if(asl_configuration.direction_mode) {
  
          var direction_mode = asl_configuration.direction_mode.toUpperCase();
  
          //  Override when we hav a value
          if(google.maps['DirectionsTravelMode'][direction_mode])
            travel_mode = direction_mode;
        }
  
  
  
        this.directionsService_.route({
            origin: this.directionsFrom_,
            destination: this.directionsTo_.getLocation(),
            travelMode: travel_mode,
            unitSystem: that.distance_type
            //TODO(cbro): region biasing, waypoints, travelmode
          },
          function(result, status) {
            that.el_.find('#map-loading').hide();
            if (status != google.maps.DirectionsStatus.OK) {
              // TODO(cbro): better error handling
              return;
            }
  
            $('.asl-cont .count-row').addClass('hide');
            $('.asl-cont #filter-options').addClass('hide');
  
            var view = that.get('view');
            view.showing_direction = true;
  
            //  Hide the infobox
            if(view.infoWindow_.getVisible()) {
              view.infoWindow_.close();
            }
  
            var renderer = that.directionsRenderer_;
            renderer.setPanel(rendered[0]);
            renderer.setMap(that.get('view').getMap());
            renderer.setDirections(result);
          });
  
        //make it null again
        this.directionsFrom_ = null;
      };
  
      /**
       * featureFilter_changed event handler.
       */
      asl_locator.Panel.prototype.featureFilter_changed = function() {
  
        this.listenForStoresUpdate_();
      };
  
  
  
      /**
       * @example see asl_locator.Panel
       * @interface
       */
      asl_locator.PanelOptions = function() {};
  
      /**
       * Whether to show the location search box. Default is true.
       * @type boolean
       */
      asl_locator.prototype.locationSearch;
  
      /**
       * The label to show above the location search box. Default is "Where are you
       * now?".
       * @type string
       */
      asl_locator.PanelOptions.prototype.locationSearchLabel;
  
      /**
       * Whether to show the feature filter picker. Default is true.
       * @type boolean
       */
      asl_locator.PanelOptions.prototype.featureFilter;
  
      /**
       * Whether to provide directions. Deafult is true.
       * @type boolean
       */
      asl_locator.PanelOptions.prototype.directions;
  
      /**
       * The store locator model to bind to.
       * @type asl_locator.View
       */
      asl_locator.PanelOptions.prototype.view;
    })(jQuery);
  
  
    /*Site Script*/
    (function($) {
  
      //  Accent characters normalization
      var charMap = {
        'a': /[àáâ]/gi,
        'c': /[ç]/gi,
        'e': /[èéêë]/gi,
        'i': /[ï]/gi,
        'o': /[ôÓÖ]/gi,
        'oe': /[œ]/gi,
        'u': /[üÚ]/gi
      };
  
      var normalize = function (str) {
        $.each(charMap, function (normalized, regex) {
          str = str.replace(regex, normalized);
        });
        return str;
      };
  
      var queryTokenizer = function (q) {
        var normalized = normalize(q);
        return Bloodhound.tokenizers.whitespace(normalized);
      };
  
  
  
      //Main map instance
      var map = null;
  
      var asl_engine = {
        config: {},
        helper: {}
      };
      window['asl_engine'] = asl_engine;
  
  
  
      if (!window['asl_configuration']) return;
      asl_configuration.category_accordion = (asl_configuration.layout == '2')? true: false;
      asl_configuration.accordion = (asl_configuration.layout == '1' || asl_configuration.category_accordion) ? true : false;
      asl_configuration.analytics = (asl_configuration.analytics == '1') ? true : false;
      asl_configuration.sort_by_bound = (asl_configuration.sort_by_bound == '1') ? true : false;
      asl_configuration.scroll_wheel = (asl_configuration.scroll_wheel == '1') ? true : false;
      asl_configuration.distance_slider = (asl_configuration.distance_slider == '1') ? true : false;
      asl_configuration.show_categories = (asl_configuration.show_categories == '0') ? false : true;
      asl_configuration.time_switch = (asl_configuration.time_switch == '0') ? false : true;
      asl_configuration.category_marker = (asl_configuration.category_marker == '0') ? false : true;
      asl_configuration.advance_filter = (asl_configuration.advance_filter == '0') ? false : true;
      asl_configuration.time_24 = (asl_configuration.time_format == '1') ? true : false;
      //asl_configuration.week_hours = (asl_configuration.week_hours == '1') ? true : false;
      asl_configuration.user_center = (asl_configuration.user_center == '1') ? true : false;
      asl_configuration.distance_unit  = (asl_configuration.distance_unit == 'KM') ? asl_configuration.distance_unit : 'Miles';
      asl_configuration.filter_address = (asl_configuration.filter_address == '1') ? true : false;
      asl_configuration.regex = (asl_configuration.no_regex) ? /#|\./ig : /[^a-z0-9\s]/gi;
      asl_configuration.info_x_offset = (asl_configuration.info_x_offset && !isNaN(asl_configuration.info_x_offset)) ? parseInt(asl_configuration.info_x_offset) : 0;
      asl_configuration.info_y_offset = (asl_configuration.info_y_offset && !isNaN(asl_configuration.info_y_offset)) ? parseInt(asl_configuration.info_y_offset) : 0;
      asl_configuration.enter_key     = true;
      asl_configuration.category_sort = true;
      asl_configuration.stores_limit  = (asl_configuration.stores_limit && !isNaN(asl_configuration.stores_limit)) ? parseInt(asl_configuration.stores_limit) : null;
      asl_configuration.radius_circle = (asl_configuration.radius_circle == '1') ? true : false;
      asl_configuration.marker_height = (asl_configuration.marker_height)? asl_configuration.marker_height: '43';
      asl_configuration.and_filter    = (asl_configuration.and_filter == '1') ? true : false;
      asl_configuration.category_bound= (asl_configuration.category_bound == '1') ? true : false;
      asl_configuration.fit_bound     = (asl_configuration.fit_bound == '1') ? true : false;
      asl_configuration.sort_random   = (asl_configuration.sort_random == '1') ? true : false;
      asl_configuration.filter_ddl    = null;
      asl_configuration.boundary_box  = (asl_configuration.distance_control == '2')? true: false;
      asl_configuration.store_radius  = (asl_configuration.store_radius == '1')? true: false;
      asl_configuration.marker_title  = (asl_configuration.marker_title == '0')? false: true;
      asl_configuration.hide_logo     = (asl_configuration.hide_logo == '1')? true: false;
      asl_configuration.hide_hours    = (asl_configuration.hide_hours == '1')? true: false;
      asl_configuration.do_bounce     = (asl_configuration.do_bounce === '0')? false: true;
      asl_configuration.list_event    = (asl_configuration.mouseover_list === '1')? 'mouseover': 'click';
      asl_configuration.pickup        = (asl_configuration.pickup === '1')? true: false;
      asl_configuration.ship_from     = (asl_configuration.ship_from === '1')? true: false;
      asl_configuration.address_ddl   = false;
      asl_configuration.tabs_layout   = (asl_configuration.tabs_layout === '1')? true: false;
      asl_configuration.ddl_search    = asl_configuration.ddl_search? true: false;
      asl_configuration.target_blank  = (asl_configuration.target_blank == '1')? '_blank':'_self';
      asl_configuration.cluster       = (asl_configuration.cluster == '0')? false: true;
      asl_configuration.display_list  = true;
      asl_configuration.adv_mkr       = (asl_configuration.advanced_marker)? true: false;
  
      // Disable the Circle when distance slider is off
      if(!asl_configuration.distance_slider) {
        asl_configuration.radius_circle = false;
      }
  
      //  Show stores On Category Select 
      asl_configuration.on_select     = true;
      if(asl_configuration.first_load == '6') {
        asl_configuration.first_load = '1';
        asl_configuration.on_select  = false;
      }
  
  
      //  Default location Address, disable Prompt Location
      if(asl_configuration['default-addr'] || asl_configuration['select_category']) {
        asl_configuration.prompt_location = '0';
      }
  
      //  Avoid multiple search names
      if(asl_configuration.search_type == '1' && asl_configuration.template != '2' && asl_configuration.search_2) {
        asl_configuration.search_type = '0';
      }
  
  
      if(asl_configuration.sort_random && asl_configuration.user_center) {
        asl_configuration.user_center = false;
        console.log('Warning! Sort Random disable the default location marker');
      }
  
      if(asl_configuration.search_type == '1' || asl_configuration.search_type == '2') {
        asl_configuration.user_center = false;
      }
  
      if(asl_configuration.first_load != '1') {
        asl_configuration.user_center = false;      
      }
  
  
  
      //  Add the offset
      if(!asl_configuration.info_y_offset) {
  
        //  Default
        asl_configuration.info_y_offset = -100;
  
        if(asl_configuration.template == '2')
          asl_configuration.info_y_offset = -120;
  
        if(asl_configuration.infobox_layout == '1') {
          asl_configuration.info_y_offset = -150;
        }
      }
  
  
      asl_configuration.fixed_radius = (asl_configuration.fixed_radius && !isNaN(asl_configuration.fixed_radius)) ? parseInt(asl_configuration.fixed_radius) : null;
  
      asl_configuration.is_mob = _isMobileDevice();
  
      //  When the device is a mobile
      if(asl_configuration.is_mob) {
        
        //   Zoom for the mobile device
        if(asl_configuration.mobile_zoom)
          asl_configuration.zoom = parseInt(asl_configuration.mobile_zoom);
  
        //   Search Zoom for the mobile device
        if(asl_configuration.mobile_search_zoom)
          asl_configuration.search_zoom = parseInt(asl_configuration.mobile_search_zoom);
  
        //   Search Zoom for the mobile device
        if(asl_configuration.mobile_click_zoom)
          asl_configuration.zoom_li = parseInt(asl_configuration.mobile_click_zoom);
  
        // For mobile devices, don't let it be mouseover
        asl_configuration.list_event = 'click';
        asl_configuration.mouseover  = false;
      }
  
      //  Add a max bound zoom
      asl_configuration.max_bound_zoom = (asl_configuration.max_bound_zoom)? parseInt(asl_configuration.max_bound_zoom): asl_configuration.search_zoom;
      
  
      //  Geocoding Only
      if (asl_configuration.search_type == '3') {
  
        asl_configuration.search_type = '0';
        asl_configuration.geocoding_only = true;
      }
  
      //Fixed
      if (asl_configuration.full_height) {
        //$('#asl-storelocator .asl-map-canv').css('height', '100vh');
        //$('#asl-storelocator .asl-map-canv').css('height', (locator_height) + 'px');
      }
  
      //Only for load all
      /*if(asl_configuration.load_all != "1")
        asl_configuration.search_type = '0';*/
  
      //Set Mobile Load on Bound
      if (asl_configuration.is_mob && asl_configuration.mobile_load_bound) {
  
        asl_configuration.load_all = '2';
        asl_configuration.search_type = '0';
      }
  
      //  Radius only Works with Google Search
      if (asl_configuration.search_type != '0' || asl_configuration.distance_control != '1') {
  
        console.log('Radius Circle Works with Google Search Only and Distance Control.');
        asl_configuration.radius_circle = false;
      }
  
  
      //  Enable the Search by Description
      if(asl_configuration.additional_search) {
  
        asl_configuration.search_type = '2';
      }
  
  
  
  
      //  Set Mobile Stores Limit
      if (asl_configuration.is_mob && asl_configuration.mobile_stores_limit) {
  
        asl_configuration.stores_limit = (asl_configuration.mobile_stores_limit && !isNaN(asl_configuration.mobile_stores_limit)) ? parseInt(asl_configuration.mobile_stores_limit) : null;
      }
  
      if (!asl_configuration.advance_filter)
        $('.asl-cont').addClass('no-asl-filters');
  
      
      asl_configuration.mobile_stores_limit = (asl_configuration.mobile_stores_limit) ? parseInt(asl_configuration.mobile_stores_limit) : 100;
  
  
      // Disable Radius Circle, if Load all = 0
      if(asl_configuration.load_all != '1') {
  
        asl_configuration.cache = asl_configuration.radius_circle = false;
        console.log('Radius Circle Works with load all only');
      }
  
      //NO asl filters
      if (asl_configuration.advance_filter && $('#asl-open-close')[0])
        $('#asl-open-close')[0].checked = true;
  
      /*ALL HELPER METHODS*/
      var asl_lat = (asl_configuration.default_lat) ? parseFloat(asl_configuration.default_lat) : 39.9217698526,
        asl_lng = (asl_configuration.default_lng) ? parseFloat(asl_configuration.default_lng) : -75.5718432,
        categories = {},
        asl_date = new Date();
  
      asl_configuration.default_lat = asl_lat;
      asl_configuration.default_lng = asl_lng;
  
  
      //HARDCODE MODE
      /*
      asl_configuration.show_categories = true; 
      asl_configuration.advance_filter = true;  
      asl_configuration.additional_info = true;
      */
      /*
      asl_configuration.range_slider = true;
      asl_configuration.show_distance = true; 
      asl_configuration.direction = true; 
      asl_configuration.additional_info = true; 
      asl_configuration.distance_unit = true; 
      asl_configuration.cluster = true; 
      */
      asl_configuration.show_opened = false;
  
      $('#asl-dist-unit').html(asl_configuration.distance_unit);
  
  
      var COUNT_FORMATS =
      [
        { // 0 - 999
          letter: '',
          limit: 1e3
        },
        { // 1,000 - 999,999
          letter: 'K',
          limit: 1e6
        }
      ];
  
      /**
       * [html_entites Convert the codes into HTML]
       * @param  {[type]} _string [description]
       * @return {[type]}         [description]
       */
      asl_engine.helper.html_entites = function(_string) {
  
        var tempElement = document.createElement("div");
        tempElement.innerHTML = _string;
        return tempElement.innerText;
      };
  
      /**
       * [is_empty Check if it's empty]
       * @param  {[type]} _string [description]
       * @return {[type]}         [description]
       */
      asl_engine.helper.is_empty = function(obj) {
  
        // Check if the object is null or undefined
        if (obj === null || obj === undefined) {
          return true;
        }
  
        // Check if it's an object and has no own properties
        if (typeof obj !== 'object' || Array.isArray(obj)) {
          return false;
        }
  
        // Check if all properties are either null or undefined
        for (let key in obj) {
          if (obj[key] !== null && obj[key] !== undefined) {
            return false;
          }
        }
  
        return true;
      };
  
      /**
       * [format_count Format the count in K]
       * @param  {[type]} value [description]
       * @return {[type]}       [description]
       */
      asl_engine.helper.format_count = function(value) {
  
        var limit  = 1e6;
  
         if(value < limit && value > 1000) {
          value = (1000 * value / limit);
          value = Math.round(value * 10) / 10; // keep one decimal number, only if needed
          return (value + 'K');
        }
  
        return value.toFixed(2);
      };
  
  
      /**
       * [uniq Get the array of objects of uniq type]
       * @param  {[type]} _list [description]
       * @param  {[type]} _type [description]
       * @return {[type]}       [description]
       */
      asl_engine.helper.uniq = function(_list, _type, _normalize) {
  
        var unique    = [];
        var distinct  = [];
  
        for(var i = 0; i < _list.length; i++) {
  
          if( !unique[_list[i][_type]]) {
  
            distinct.push((_normalize)? { type: _type, title: _list[i][_type] }: { type: _type, title: _list[i][_type], value: normalize(_list[i][_type]) });
            unique[_list[i][_type]] = 1;
          }
        }
  
        return distinct;
      };
  
      /**
       * [merge Merge two array]
       * @param  {[type]} a [description]
       * @param  {[type]} b [description]
       * @return {[type]}   [description]
       */
      asl_engine.helper.merge = function(a, b) {
  
        var hash = {};
        var i;
        
        for (i = 0; i < a.length; i++) {hash[a[i]] = true;}
        for (i = 0; i < b.length; i++) {hash[b[i]] = true;}
        
        return Object.keys(hash);
      };
  
      /**
       * [asl_leadzero description]
       * @param  {[type]} n [description]
       * @return {[type]}   [description]
       */
      asl_engine.helper.asl_leadzero = function(n) {
        return n > 9 ? "" + n : "0" + n;
      };
  
      /**
       * [asl_timeConvert description]
       * @param  {[type]} _str [description]
       * @return {[type]}      [description]
       */
      asl_engine.helper.asl_timeConvert = function(_str) {
  
        if(!_str)return 0;
  
        var time  = $.trim(_str).toUpperCase();
  
        if(/(1[012]|[0-9]):[0-5][0-9]$/.test(time)) {
  
        var hours   = Number(time.match(/^(\d+)/)[1]);
        var minutes = Number(time.match(/:(\d+)/)[1]);
  
        return hours+(minutes / 100);
        }
        else if(/(1[012]|[1-9]):[0-5][0-9][ ]?(AM|PM)/.test(time)) {
          
          var hours   = Number(time.match(/^(\d+)/)[1]);
          var minutes = Number(time.match(/:(\d+)/)[1]);
          var AMPM    = (time.indexOf('PM') != -1)?'PM':'AM';
  
          if (AMPM == "PM" && hours < 12) hours  = hours + 12;
        if (AMPM == "AM" && hours == 12) hours = hours - 12;
  
          return hours+(minutes / 100);
        }
        else
          return 0;
  
      };
  
      /**
       * [between description]
       * @param  {[type]} number [description]
       * @param  {[type]} lower  [description]
       * @param  {[type]} upper  [description]
       * @return {[type]}        [description]
       */
      asl_engine.helper.between = function(number, lower, upper) {
        return number > lower && number < upper;
      };
  
      /**
       * [implode description]
       * @param  {[type]} arr [description]
       * @param  {[type]} sep [description]
       * @return {[type]}     [description]
       */
      asl_engine.helper.implode = function(arr, sep) {
        var parts = [];
        for (var i = 0, ii = arr.length; i < ii; i++) {
          arr[i] && parts.push(arr[i]);
        }
        return parts.join(sep);
      };
  
      /**
       * [toObject_ description]
       * @param  {[type]} headings [description]
       * @param  {[type]} row      [description]
       * @return {[type]}          [description]
       */
      asl_engine.helper.toObject_ = function(headings, row) {
  
        var result = {};
        for (var i = 0, ii = row.length; i < ii; i++) {
          result[headings[i]] = row[i];
        }
        return result;
      };
  
  
      /**
       * [distanceCalc description]
       * @param  {[type]} point [description]
       * @return {[type]}       [description]
       */
      asl_engine.helper.distanceCalc = function(point) {
  
        var R = 6371; // mean radius of earth
        var location = this.getLocation();
        var lat1 = asl_locator.toRad_(location.lat());
        var lon1 = asl_locator.toRad_(location.lng());
        var lat2 = asl_locator.toRad_(point.lat());
        var lon2 = asl_locator.toRad_(point.lng());
        var dLat = lat2 - lat1;
        var dLon = lon2 - lon1;
  
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
          Math.cos(lat1) * Math.cos(lat2) *
          Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
      };
  
  
      /**
       * [multi_sort description]
       * @param  {[type]} _items   [description]
       * @param  {[type]} _param_1 [description]
       * @param  {[type]} _param_2 [description]
       * @return {[type]}          [description]
       */
      asl_engine.helper.multi_sort =  function(_items, _param_1, _param_2) {
  
        _items.sort(function(a, b) {
  
          var aVal = a[_param_1];
          var bVal = b[_param_1];
          var aNum = a[_param_2];
          var bNum = b[_param_2];
  
          if (aVal == bVal) {
  
            return (aNum < bNum) ? -1 : (aNum > bNum) ? 1 : 0;
          } else {
            return (aVal < bVal) ? -1 : 1;
          }
        });
      };
  
  
      /**
       * [sortBy Sort Method for Integer & Strings]
       * @param  {[type]} a [description]
       * @param  {[type]} b [description]
       * @return {[type]}   [description]
       */
      asl_engine.helper.sortBy = function(_items, _type, _string) {
  
  
        var sort_method = null;
  
        //  For Strings
        if(_string) {
  
          if(asl_configuration.sort_order == 'desc') {
  
            //  For string to with localecompare
            sort_method = function(a, b) {
              
              return (a[_type] && b[_type])? -(a[_type].localeCompare(b[_type])): 0;
            };
          }
          else {
  
            //  For string to with localecompare
            sort_method = function(a, b) {
              
              return (a[_type] && b[_type])? a[_type].localeCompare(b[_type]): 0;
            };
          }
        }
        //  Integers
        else {
  
          if(asl_configuration.sort_order == 'desc') {
  
            sort_method = function(a, b) {
              return parseInt(b[_type]) - parseInt(a[_type]);
            };
          }
          else
            sort_method = function(a, b) {
              return parseInt(a[_type]) - parseInt(b[_type]);
            };  
        }
  
        return _items.sort(sort_method);
      };
  
      /*HELPER ENDS HERE*/
  
  
      /*All the DATAFEED*/
      asl_engine.dataSource = function() {
  
        this.stores_ = [];
        this.remote_url = ASL_REMOTE.ajax_url;
      };
  
      /* Accordion STARTS*/
      asl_engine.dataSource.prototype.getCountriesStateCities = function(_objects) {
  
        var countries = {};
  
        for (var i = 0; i < _objects.length; i++) {
  
          //country add
          if (!countries[_objects[i].props_.country]) {
            countries[_objects[i].props_.country] = {};
          }
  
          //state add
          if (!countries[_objects[i].props_.country][_objects[i].props_.state]) {
            countries[_objects[i].props_.country][_objects[i].props_.state] = [];
          }
  
          //add the suburb if not there
          if (countries[_objects[i].props_.country][_objects[i].props_.state].indexOf(_objects[i].props_.city) == -1) {
            countries[_objects[i].props_.country][_objects[i].props_.state].push(_objects[i].props_.city);
          }
        }
  
        return countries;
      };
  
  
      asl_engine.dataSource.prototype.getStateCities = function(_objects) {
  
        var states_n_cities = {};
  
        for (var i = 0; i < _objects.length; i++) {
  
          //state add
          if (!states_n_cities[_objects[i].props_.state]) {
            states_n_cities[_objects[i].props_.state] = [];
          }
  
          //add the suburb if not there
          if (states_n_cities[_objects[i].props_.state].indexOf(_objects[i].props_.city) == -1) {
            states_n_cities[_objects[i].props_.state].push(_objects[i].props_.city);
          }
        }
  
        return states_n_cities;
      };
  
      /**
       * [sortDistance description]
       * @param  {[type]} latLng [description]
       * @param  {[type]} stores [description]
       * @return {[type]}        [description]
       */
      asl_engine.dataSource.prototype.sortDistance = function(latLng, stores) {
  
        stores = stores.sort(function(a, b) {
          return a.distanceTo(latLng) - b.distanceTo(latLng);
        });
      };
  
  
      /**
       * [sortRandom Sort by random]
       * @param  {[type]} stores [description]
       * @return {[type]}        [description]
       */
      asl_engine.dataSource.prototype.sortRandom  = function(stores) {
  
          stores = stores.sort(function(a, b) {
            return Math.random() - 0.5;
          });
      };
  
      /**
       * [sortBy Sort By Function]
       * @param  {[type]} _type  [description]
       * @param  {[type]} stores [description]
       * @return {[type]}        [description]
       */
      asl_engine.dataSource.prototype.sortBy = function(_type, stores) {
  
        var sort_method = null;
  
  
        //  There are no stores
        if(!stores || !stores.length) {
          return;
        }
  
        //  when search term is integer
        var is_integer = (typeof stores[0].props_[_type] == 'number')? true: false;
  
        if (_type == 'cat') {
            
          /**
           * [sort_method Sort for the categries]
           * @param  {[type]} a [description]
           * @param  {[type]} b [description]
           * @return {[type]}   [description]
           */
          sort_method = function(a, b) {
  
            var aVal = a.props_['cat'];
            var bVal = b.props_['cat'];
            var aDist = a.props_['distance'];
            var bDist = b.props_['distance'];
  
            if (aVal == bVal) {
  
              return (aDist < bDist) ? -1 : (aDist > bDist) ? 1 : 0;
            }
            else {
              return (aVal < bVal) ? -1 : 1;
            }
          };
        }
        //When integer
        else if(is_integer) {
  
          /**
           * [sort_method description]
           * @param  {[type]} a [description]
           * @param  {[type]} b [description]
           * @return {[type]}   [description]
           */
          sort_method = function(a, b) {
            return (a.props_[_type] > b.props_[_type]) ? 1 : ((b.props_[_type] > a.props_[_type]) ? -1 : 0);
          }
        }
        else if(asl_configuration.sort_order == 'desc') {
          
          /**
           * [sort_method description]
           * @param  {[type]} a [description]
           * @param  {[type]} b [description]
           * @return {[type]}   [description]
           */
          sort_method = function(a, b) {
            return (a.props_[_type].toLowerCase() < b.props_[_type].toLowerCase()) ? 1 : ((b.props_[_type].toLowerCase() < a.props_[_type].toLowerCase()) ? -1 : 0);
          };
        }
        else {
            
          /**
           * [sort_method description]
           * @param  {[type]} a [description]
           * @param  {[type]} b [description]
           * @return {[type]}   [description]
           */
          sort_method = function(a, b) {
            return (a.props_[_type].toLowerCase() > b.props_[_type].toLowerCase()) ? 1 : ((b.props_[_type].toLowerCase() > a.props_[_type].toLowerCase()) ? -1 : 0);
          }
        }
        
        stores.sort(sort_method);
      };
  
  
      /**
       * [sortByDesc Sort by Desc]
       * @param  {[type]} _type  [description]
       * @param  {[type]} stores [description]
       * @return {[type]}        [description]
       */
      asl_engine.dataSource.prototype.sortByDesc = function(_type, stores) {
  
        stores.sort(function(a, b) {
  
          return (a.props_[_type] < b.props_[_type]) ? 1 : ((b.props_[_type] < a.props_[_type]) ? -1 : 0);
        });
      };
  
  
  
      /**
       * [load_kml Load a KML]
       * @param  {[type]} _map [description]
       */
      asl_engine.dataSource.prototype.load_kml = function(_map) {
  
        //  All the KML files
        var kml_files = asl_configuration.kml_files;
  
        for(var k in kml_files) {
  
          if (!kml_files.hasOwnProperty(k)) continue;
  
  
          var src_file = asl_configuration.URL + '/kml/' + kml_files[k];
  
          //  Create a layer
          var kmlLayer = new google.maps.KmlLayer(src_file, {
              //suppressInfoWindows: false,
              preserveViewport: true,
              map: _map
            });
          
          /*kmlLayer.addListener('click', function(event) {
            var content = event.featureData.infoWindowHtml;
            var testimonial = document.getElementById('capture');
            testimonial.innerHTML = content;
          });*/
        }
      };
  
  
      var not_initial_load = false,
        asl_view = null,
        asl_panel = null;
  
  
      /**
       * [fetch_remote_data Fetch the remote Data]
       * @param  {[type]} _position [description]
       * @return {[type]}           [description]
       */
      asl_engine.dataSource.prototype.fetch_remote_data = function(_position, _params, _fetch_callback) {
  
        var that = this;
  
        $('.asl-cont .asl-overlay').show();
  
        // Old _params for GDPR
        if(!_params && asl_configuration.reload_params) {
          
          //  Give it to _params
          _params = asl_configuration.reload_params;
  
          // reset it to null
          asl_configuration.reload_params = null;
        }
  
        var params = {
          action: 'asl_load_stores',
          nonce: ASL_REMOTE.nonce,
          //asl_lang: asl_configuration.lang,
          load_all: asl_configuration.load_all,
          layout: (asl_configuration.layout) ? 1 : 0
        };
  
        //  Merge with the original parameters
        if(_params) {
          params = Object.assign(params, _params);
        }
  
  
        //limited markers
        if (asl_configuration.stores) {
          params['stores'] = asl_configuration.stores;
        }
  
        //Not Load all
        if (asl_configuration.load_all != '1') {
  
          var bounds = map.getBounds(),
            ne = bounds.getNorthEast(), // LatLng of the north-east corner
            sw = bounds.getSouthWest(), // LatLng of the south-west corder
            center = bounds.getCenter();
  
          params['lat'] = center.lat(),
            params['lng'] = center.lng(),
            params['nw'] = [ne.lat(), sw.lng()],
            params['se'] = [sw.lat(), ne.lng()];
        }
  
  
        //  specific category
        if (asl_configuration.category) {
          params['category'] = asl_configuration.category;
        }
  
        //  specific special
        if (asl_configuration.special) {
          params['special'] = asl_configuration.special;
        }
  
        //  specific brand
        if (asl_configuration.brand) {
          params['brand'] = asl_configuration.brand;
        }
  
        /*
        if(that.on_call)
          return;
        */
        that.on_call = true;
  
        var the_url = ASL_REMOTE.ajax_url;
        
        $.ajax({
          url: the_url,
          data: params,
          type: 'GET',
          dataType: 'json',
          success: function(_data) {
  
            that.stores_ = that.parseData(_data);
  
            var _input_txt = $('.asl-cont #auto-complete-search,.asl-cont .asl-search-address');
  
            //  Remove spinner
            if (asl_configuration.load_all == '2') {
              $('.asl-reload-map').find('i').removeClass('animate-spin');
            }
            
            
            //  Add Clear button for Search Type 0 and 3
            if (!not_initial_load) {
              asl_locator.add_clear_button(_input_txt);
            }
  
  
            //  Add search location end
            var all_stores = that.stores_;
  
            var country = (all_stores[0]) ? all_stores[0].props_.country : null;
            that.countries = false;
  
  
            for (var j = 0; j < all_stores.length; j++) {
  
  
              if (country != all_stores[j].props_.country) {
                that.countries = true;
                break;
              }
            }
  
            
  
            // Only Once loaded
            if (!not_initial_load) {
  
              not_initial_load = true;
  
              //that.load_locator(all_stores[0],that.countries);
              asl_view = new asl_locator.View(map, that, {
                geolocation: false,
                features: that.getDSFeatures()
              });
  
  
              asl_panel = new asl_locator.Panel(document.getElementById('asl-panel'), {
                view: asl_view
              });
  
              window['asl_view'] = asl_view;
  
              
              //  when we have the kml files, include them
              if(asl_configuration.kml_files) {
  
                that.load_kml(map);
              }
  
  
  
              //  Desc Modal Close Event
              $('.asl-cont #asl-desc-agile-modal').find('.sl-close').bind('click', asl_panel.hideDescModal.bind(asl_panel));
  
              //  Geo Modal
              var $agile_modal = jQuery('.asl-cont #asl-geolocation-agile-modal');
  
              //  Close Dailog Event GeoModal
              $agile_modal.find('.sl-close').bind('click', asl_panel.hideGeoModal.bind(asl_panel));
  
               /**
               * [_promptLocResRender Will remove the dailog and execute measure_distance]
               * @param  {[type]} geo_place [description]
               * @return {[type]}           [description]
               */
              function _promptLocResRender(geo_place) {
  
                asl_view.measure_distance(geo_place.geometry.location, true, null, geo_place);
  
                $agile_modal.removeClass('in').css('display', 'none');
  
                if (asl_configuration.analytics) {
                  //asl_locator.save_analytics(p);
                }
              };
  
              
  
              //  Auto Without Prompt
              if (asl_configuration.prompt_location == '3') {
                asl_view.geolocate_();
              }
              else if (asl_configuration.prompt_location == '4') {
                asl_view.geo_service();
              }
              //  Prompt using the Dailog
              else if (asl_configuration.prompt_location != '0') {
  
                $agile_modal.css('display', 'block');
  
                window.setTimeout(function() {
  
                  $agile_modal.addClass('in');
  
                }, 300);
  
                //IF PROMPT is ON
                $('.asl-cont #asl-btn-geolocation').bind('click', function() {
                  asl_view.geolocate_();
                  $agile_modal.removeClass('in').css('display', 'none');
                });
  
                //  Geo Modal is enabled
                asl_panel.geo_modal = true;
              }
  
              // For GEO Locate Prompt Dailog
              if (asl_configuration.prompt_location == "2") {
  
                var geo_place = null;
  
                var geocoder = new google.maps.Geocoder(),
                  _callback = _callback || function(results, status) {
  
                    if (status == 'OK') {
  
                      _promptLocResRender(results[0]);
  
                    } else {
                      console.log('Geocode was not successful for the following reason: ' + status);
                    }
                  };
  
  
                // Enter Key
                $('#asl-current-loc').bind('keyup', function(e) {
  
                  if (e.keyCode == 13) {
  
                    var addr_value = $.trim(this.value);
  
                    if (addr_value) {
  
                      //birmingham
                      var search_param = { 'address': addr_value };
  
                      //  Restrict the search
                      if (asl_configuration.country_restrict) {
                      
                        var country_restricted = asl_configuration.country_restrict.toLowerCase();
  
                        country_restricted = country_restricted.split(',');
  
                        search_param['componentRestrictions'] = {
                          country: country_restricted[0]
                        };
                      }
  
                      geocoder.geocode(search_param, _callback);
                    }
                  }
                });
  
                //Event of geolocate 
                $('#asl-btn-locate').click(function(e) {
  
  
                  // When we have it through Place API
                  if (geo_place) {
                    
                    _promptLocResRender(geo_place);
                    return;
                  }
  
  
                  // For simple button click when no data
                  var addr_value = $.trim($('#asl-current-loc').val());
  
                    if (addr_value)
                      geocoder.geocode({ 'address': addr_value }, _callback);
                });
  
  
                var input = $('.asl-cont #asl-current-loc')[0];
  
                var options = {};
  
                if (asl_configuration.google_search_type) {
                  options['types'] = ['(' + asl_configuration.google_search_type + ')'];
                }
  
                /*if (asl_configuration.country_restrict) {
                  options['componentRestrictions'] = { country: asl_configuration.country_restrict.toLowerCase() }
                }*/
  
  
                var autoComplete_ = new google.maps.places.Autocomplete(input, options);
  
                //  Restrict the country
                if (asl_configuration.country_restrict) {
              
                  var country_restricted = asl_configuration.country_restrict.toLowerCase();
  
                  country_restricted = country_restricted.split(',');
  
                  autoComplete_.setComponentRestrictions({country: country_restricted });
                }
  
                //  Limit the search scope
                var fields = ['geometry'];
  
                if(asl_configuration.filter_address)
                  fields.push('address_components');
  
                autoComplete_.setFields(fields);
  
                google.maps.event.addListener(autoComplete_, 'place_changed',
  
                  function() {
                    var p = this.getPlace();
  
                    geo_place = p;
                  });
              }
  
              //GEO Locate Current Location
              $('.asl-cont .asl-geo.icon-direction-outline').bind('click', function(e) {
                asl_view.geolocate_();
              });
  
  
              //for users who want fixed lat/lng for home location
              if (asl_configuration.user_center)
                asl_view.measure_distance(new google.maps.LatLng(asl_configuration.default_lat, asl_configuration.default_lng));
            }
            // Only loaded once end
  
            //  Calculate distance for Ajax
            if (asl_configuration.load_all != '1' && asl_view.dest_coords) {
              asl_view.measure_distance(asl_view.dest_coords, null, true);
            }
  
  
            //  First Refresh of the View
            asl_view.refreshView();
  
            $('.asl-cont .asl-overlay').hide();
            
            //  Pan to a given Position
            if (_position) {
              map.panTo(_position);
            }
  
  
            that.on_call = false;
  
            //  Call the asl_loaded event
            if(window['asl_loaded'] && typeof window['asl_loaded']  == 'function') {
              asl_loaded.call(this);
            }
  
  
            //  When it is Load All
            if(asl_configuration.load_all == '1') {
  
              //  Highlight a Store supported by Load all
              var hash_store_id = window.location.hash.replace('#', '');
  
              //  Validate, it should be a number
              if(hash_store_id && !isNaN(hash_store_id)) {
  
                //  Timeout for the Hash Method
                window.setTimeout(function() {
                    
                  var searched_store = asl_view.data_.findStore(hash_store_id);
  
                  asl_view.highlight(searched_store);
  
                }, 500);
              }
            }
  
  
            //  All data is loaded
            asl_locator.hook_event({type: 'load', data: null});
  
            //  Add a callback function
            if(_fetch_callback && typeof _fetch_callback == 'function') {
              _fetch_callback.call(this);
            }
  
          },
          error: function() {
            that.on_call = false;
          },
          dataType: 'json'
        });
  
  
        /*Save that pos*/
        that.pos = center;
      };
  
  
  
      /**
       * [load_locator Load the Locator]
       * @return {[type]} [description]
       */
      asl_engine.dataSource.prototype.load_locator = function() { /* _store,_morecountries */
  
        var that = this;
  
        //Only if found
        if (!document.getElementById('asl-map-canv'))
          return false;
  
  
        var maps_params = {
          center: new google.maps.LatLng(asl_lat, asl_lng),
          zoom: parseInt(asl_configuration.zoom),
          scrollwheel: asl_configuration.scroll_wheel,
          gestureHandling: asl_configuration.gesture_handling || 'cooperative', //cooperative,greedy
          mapTypeId: asl_configuration.map_type
        };
  
        //  Advanced Marker
        if(asl_configuration.advanced_marker) {
          maps_params['mapId'] = asl_configuration.map_id || "asl-maps-id-tag";
        }
  
        maps_params.zoomControl     = (asl_configuration.zoomcontrol == 'true')? true: false;
        maps_params.mapTypeControl  = (asl_configuration.maptypecontrol == 'true')? true: false;
        if (asl_configuration.scalecontrol == 'false') maps_params.scaleControl = false;
        if (asl_configuration.rotatecontrol == 'false') maps_params.rotateControl = false;
        if (asl_configuration.fullscreencontrol == 'false') maps_params.fullscreenControl = false;
        if (asl_configuration.streetviewcontrol == 'false') maps_params.streetViewControl = false;
        if (asl_configuration.cameracontrol == 'false') maps_params.cameraControl = false;
  
        maps_params['fullscreenControlOptions'] = {
          position: google.maps.ControlPosition.RIGHT_CENTER
        };
  
  
        // Map Type Positions
        if(asl_configuration.position_maptype) {
          maps_params['mapTypeControlOptions'] = {position: parseInt(asl_configuration.position_maptype)};
        }
  
        // FULL SCREEN Positions
        if(asl_configuration.position_fullscreen) {
          maps_params['fullscreenControlOptions'] = {position: parseInt(asl_configuration.position_fullscreen)};
        }
  
        // ZOOM Positions
        if(asl_configuration.position_zoom) {
          maps_params['zoomControlOptions'] = {position: parseInt(asl_configuration.position_zoom)};
        }
  
        // STREETVIEW Positions
        if(asl_configuration.position_streetview) {
          maps_params['streetViewControlOptions'] = {position: parseInt(asl_configuration.position_streetview)};
        }
  
        if (asl_configuration.maxzoom && !isNaN(asl_configuration.maxzoom)) {
          maps_params['maxZoom'] = parseInt(asl_configuration.maxzoom);
        }
  
        if (asl_configuration.minzoom && !isNaN(asl_configuration.minzoom)) {
          maps_params['minZoom'] = parseInt(asl_configuration.minzoom);
        }
  
        map = new google.maps.Map(document.getElementById('asl-map-canv'), maps_params);
  
        window['asl_map'] = map;
  
  
        ///////Add a Map Reset Button////////////
        asl_configuration.reset_button = true;
  
        if (asl_configuration.reset_button) {
  
          var $reset_btn = null;
  
          function ASLResetMAP(controlDiv, map) {
  
            // Set CSS for the control border.
            $reset_btn = $('<span class="asl-reset-map" style="display:none">' + asl_configuration.words.reset_map + '</span>');
            controlDiv.appendChild($reset_btn[0]);
  
            // Setup the click event listeners: simply set the map to Chicago.
            $reset_btn[0].addEventListener('click', function() {
  
              map.panTo(new google.maps.LatLng(asl_lat, asl_lng));
              map.setZoom(parseInt(asl_configuration.zoom));
  
              $reset_btn[0].style.display = 'none';
  
              //Reset the Category
              if (asl_view._panel.$category_ddl) {
  
                var selected_cats = asl_view._panel.$category_ddl.val();
                if (selected_cats && selected_cats.length > 0) {
                  asl_view._panel.$category_ddl.multiselect('deselect', asl_view._panel.$category_ddl.val(), true);
                }
              }
  
              //  When the direction box is press in the mobile, show the map
              if(asl_configuration.is_mob) {
  
                //  Scroll to the Map
                $('html, body')
                    .stop()
                    .animate({
                      'scrollTop': $('.asl-cont.storelocator-main').offset().top
                    }, 900, 'swing');
              }
  
              asl_view.highlight(null);
  
            });
          }
  
          var centerControlDiv = document.createElement('div');
          var centerControl = new ASLResetMAP(centerControlDiv, map);
  
          centerControlDiv.index = 1;
          map.controls[google.maps.ControlPosition.TOP_RIGHT].push(centerControlDiv);
        }
        /////////////////
  
  
        if (asl_configuration.map_layout) {
          var map_style = eval('(' + asl_configuration.map_layout + ')');
          map.set('styles', map_style);
        }
  
  
        if (window['_asl_map_customize']) {
  
          _asl_map_customize = JSON.parse(_asl_map_customize);
  
          //  When Advanced Marker is enabled disable the animations
          if(asl_configuration.advanced_marker) {
            _asl_map_customize.marker_animations = false;
          }
  
          //ADd trafice layer
          if (_asl_map_customize.trafic_layer && _asl_map_customize.trafic_layer == 1) {
  
            trafic_layer = new google.maps.TrafficLayer();
            trafic_layer.setMap(map);
          }
  
          //ADd bike layer
          if (_asl_map_customize.bike_layer && _asl_map_customize.bike_layer == 1) {
  
            bike_layer = new google.maps.BicyclingLayer();
            bike_layer.setMap(map);
          }
  
          //ADd transit layer
          if (_asl_map_customize.transit_layer && _asl_map_customize.transit_layer == 1) {
  
            transit_layer = new google.maps.TransitLayer();
            transit_layer.setMap(map);
          }
  
  
          ///Load the DATA
          if (_asl_map_customize.drawing) {
  
            asl_drawing.loadData(_asl_map_customize.drawing, map);
          }
        }
  
        ///////////////////////////////////////////////////////////////////////
        var _features = [];
  
        /*Add all categories*/
  
        for (var i in asl_categories) {
          var cat = asl_categories[i];
          that.FEATURES_.add(new asl_locator.Feature(cat.id, cat.name, cat.icon, ((cat.ordr && !isNaN(cat.ordr))? parseInt(cat.ordr): 0)));
        }
  
        if (asl_configuration.load_all == '1') {
  
          that.fetch_remote_data();
        } 
        else if (asl_configuration.load_all == '2') {
  
          var $reload_btn = null,
            _set_position = null,
            first_loaded = false;
  
          function ASLReloadMAP(controlDiv, map) {
  
            // Set CSS for the control border.
            $reload_btn = $('<span class="asl-reload-map" display="none"><i class="icon-arrows-cw"></i>' + asl_configuration.words.reload_map + '</span>');
            controlDiv.appendChild($reload_btn[0]);
  
            // Setup the click event listeners: simply set the map to Chicago.
            $reload_btn[0].addEventListener('click', function() {
  
              $reload_btn.find('i').addClass('animate-spin');
              that.fetch_remote_data(_set_position);
            });
          }
  
          var centerControlDiv = document.createElement('div');
          var centerControl = new ASLReloadMAP(centerControlDiv, map);
  
          centerControlDiv.index = 1;
          map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);
  
          //the load event
          google.maps.event.addListener(map, 'idle', function() {
  
            if (asl_view && asl_view.halt_fetch) {
  
              _set_position = asl_view.marker_center;
              asl_view.halt_fetch = false;
            }
  
            $reload_btn[0].style.display = 'block';
  
            //first load
            if (!first_loaded) {
              first_loaded = true;
              that.fetch_remote_data();
            }
          });
        }
        //  load_all == '0'
        else
          google.maps.event.addListener(map, 'idle', function() {
  
            //  fix over here
            var _set_position = null;
  
            if (asl_view && asl_view.halt_fetch) {
  
              _set_position = asl_view.marker_center;
              asl_view.halt_fetch = false;
              return;
            }
  
  
            that.fetch_remote_data(_set_position);
          });
  
      };
      /**
       * @const
       * @type {!asl_locator.FeatureSet}
       * @private
       */
      asl_engine.dataSource.prototype.FEATURES_ = new asl_locator.FeatureSet();
  
  
      /**
       * @return {!asl_locator.FeatureSet}
       */
      asl_engine.dataSource.prototype.getDSFeatures = function() {
        return this.FEATURES_;
      };
  
  
      /**
       * [parseData Parse Data]
       * @param  {[type]} rows [description]
       * @return {[type]}      [description]
       */
      asl_engine.dataSource.prototype.parseData = function(rows) {
  
        var stores = [];
  
        var current_hr_min = asl_date.getHours() + (asl_date.getMinutes() / 100),
          current_day      = asl_date.getDay();
  
  
        var _asl_categories = asl_categories;
        asl_categories      = {};
        var c_keys = Object.keys(_asl_categories);
  
  
        for (var c in c_keys) {
  
          if (typeof(_asl_categories[c_keys[c]]) == "object") {
  
            asl_categories[String(c_keys[c])] = _asl_categories[c_keys[c]];
            asl_categories[c_keys[c]].len = 0;
          }
        }
  
        //Calculate Open Hours
        var days      = { '1': 'mon', '2': 'tue', '3': 'wed', '4': 'thu', '5': 'fri', '6': 'sat', '0': 'sun' },
          day_keys    = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
          current_day = days[current_day];
  
        var time_now   = new Date;
        var days_number  = { 
          'sun': 0,
          'mon': 1,
          'tue': 2,
          'wed': 3,
          'thu': 4,
          'fri': 5,
          'sat': 6
        };
  
  
        var grouped_hours = (asl_configuration.week_hours == '2')? true: false;
  
        /*
        var cust_lbls = [
        asl_configuration.words.carry_out,
        asl_configuration.words.dine_in,
        asl_configuration.words.delivery
        ];
        */
  
        /**
         * [sl_slot description]
         * @return {[type]} [description]
         */
        function sl_slot() {
  
          this.start = null;
          this.end   = null;
          this.hours = [];
        }
  
        //  Use to Get All Hours
        function getAllHours(row) {
  
          //Close and Open Timing Hourse
          if (row.open_hours) {
  
  
            row.open        = false;
            row.days        = [];
            row.open_hours  = JSON.parse(row.open_hours);
            var days_words  = asl_configuration.days;
            var all_days_hours = (grouped_hours)? {}: [];
  
            for (var _kd in day_keys) {
  
              var k_day   = day_keys[_kd],
                hours     = row.open_hours[k_day],
                is_today  = (k_day == current_day) ? true : false;
  
               //Loop and Match Open Hours
                if(hours == '1') {
                    
                    if(is_today)
                        row.open =  true;
  
                    //  Push the open day
                    row.days.push(days_words[k_day]);
                }
                else if(hours == '0') {
                    
                    if(is_today)
                        row.open =  false;
                }
                else if(hours) {
                    
                    //  console.log(row.title, '===> ' + k_day + ' ===> ', hours);
                    //  Push the open day
                    row.days.push(days_words[k_day]);
  
                    var day_hours   = [];
  
  
                    for(var h in hours) {
  
                        if(!hours.hasOwnProperty(h))continue;
  
                        var hr_span = hours[h].split(' - ');
  
                        //time per day hack
                        var start_time = hr_span[0],
                            end_time   = hr_span[1];
  
                        var _start = (start_time != 0)?asl_engine.helper.asl_timeConvert(start_time):0,
                            _end   = (end_time != 0)?asl_engine.helper.asl_timeConvert(end_time):24;
  
                        //hack for end
                        if(_end == 0)_end = 24;
  
  
                        /**
                         *
                         * Timing 
                         * "10:00 PM" - "1:00 AM"
                         *  22 duration 3
                         *
                         * "4:00 PM", "2:00 AM"
                         *  16 duration
                         * 
                         */
                        
                        //  Do all the testing when open is false
                        if(!row.open) {
  
                            //  Simple Checking, Normal timing
                            if(_start < _end) {
  
                                // Is Today is used as only Today timing will be opened and they will be considered only and avoid other days, also once open saved
                                if(!row.open && is_today) {
  
                                    row.open = (_start && _end)? asl_engine.helper.between(current_hr_min, _start, _end): false;
                                }
                            }
                            //  Complex Checking
                            else {
  
                                var duration =  24 - _start  + _end;
  
                                // Convert the times in the Dates
                                var from_date = new Date();
                                from_date.setHours(Math.floor(_start));
                                from_date.setMinutes(getDecimal(_start));
  
                                // Validate the Correct day
                                if(time_now.getDay() != days_number[k_day]) {
                                    from_date.subDays(1);
                                }
  
  
                                var to_date = new Date(from_date.getTime());
                                to_date.addHours(duration);
                                to_date.setMinutes(getDecimal(_end));
  
                                //  Validate if it is in asl_engine.helper.between the timing, set Open is true
                                if(time_now > from_date && time_now < to_date) {
                                    row.open = true;
                                }
  
                            }
                        }
                        
                        //  Change the Format if 24 hours setting
                        if(asl_configuration.time_24) {
  
                            
                            _start += 0.01;
                            _start  = parseFloat(_start).toFixed(2);
                            var _s_time = String(_start).split('.');
                            
                            _s_time[0]  = asl_engine.helper.asl_leadzero(parseInt(_s_time[0]));
                            _s_time[1]  = asl_engine.helper.asl_leadzero((parseInt(_s_time[1]) - 1));
                            start_time = _s_time.join(':');
  
  
                            _end += 0.01;
                            _end = parseFloat(_end).toFixed(2);
  
                            var _e_time = String(_end).split('.');
                            _e_time[0]  = asl_engine.helper.asl_leadzero(parseInt(_e_time[0]));
                            _e_time[1]  = asl_engine.helper.asl_leadzero((parseInt(_e_time[1]) - 1));
                            end_time    = _e_time.join(':');
                        }
                        else {
  
                            var str_s_time = start_time.split(':'),
                                str_e_time = end_time.split(':');
                            
  
                            if(str_s_time[0]) {
                                str_s_time[0] = asl_engine.helper.asl_leadzero(parseInt(str_s_time[0]));
                            } 
  
                            start_time = str_s_time.join(':');
  
                            if(str_e_time[0]) {
                                str_e_time[0] = asl_engine.helper.asl_leadzero(parseInt(str_e_time[0]));
                            }
  
                            end_time = str_e_time.join(':');
                        }
  
                        day_hours.push(start_time+' - '+end_time);
                    }
  
                    if(grouped_hours)
                      all_days_hours[k_day] = day_hours;
                    else
                      all_days_hours.push('<span><span class="asl-day-lbl">'+days_words[k_day]+':</span><span class="asl-time-hrs">'+ day_hours.map(function(d) { return '<span>' + d + '</span>';}) + '</span></span>');
                }
            }
  
            //  Grouped
            if(grouped_hours) {
  
              // End of For loop
              var slots        = [],
                  tmp_slot     = null;
  
              // Loop to merge dates
              for(var d = 0; d < day_keys.length; d++) {
  
                //  A new slot for the times
                if(!tmp_slot || JSON.stringify(tmp_slot.hours) != JSON.stringify(all_days_hours[day_keys[d]])) {
                    
                  //  make sure the times exist
                  if(all_days_hours[day_keys[d]]) {
                    
                    tmp_slot = new sl_slot();
  
                    tmp_slot.start = day_keys[d];
                    tmp_slot.hours = all_days_hours[day_keys[d]];
  
                    //  add the slot
                    slots.push(tmp_slot);
                  }
                }
                else {
                  tmp_slot.end = day_keys[d];
                }
              }
  
              //  If we have the slots
              if(slots.length > 0) {
  
                var slot_html = '';
  
                //  For each slots
                slots.forEach(function(item) {
                  slot_html += '<span class="asl-group-slots"><span class="asl-day-lbl">'+days_words[item.start] + ((item.end)? (' - ' + days_words[item.end]): '') +':</span><span class="asl-time-hrs">'+ item.hours.map(function(h) { return '<span>' + h + '</span>'}).join('') + '</span></span>';
                });              
              }
  
              row.open_hours = slot_html;
            }
            //  Simple
            else {
  
              row.open_hours = (all_days_hours.length > 0)?('<span class="asl-week-hrs">'+all_days_hours.join('')+'</span>'):null;            
            }
  
            row.days = row.days.join(', ');
  
  
          }
          else row.open =  true;
        };
  
  
        // Use to Get Today Hours
        function getTodayHours(row) {
  
  
          //Close and Open Timing Hourse
          if (row.open_hours) {
  
            row.open       = false;
            
            row.open_hours = JSON.parse(row.open_hours);
  
            // Hours of the current day
            var hours = row.open_hours[current_day];
  
            row.open_hours = null;
  
            //Loop and Match Open Hours
            if(hours == '1') {
                
                row.open        =  true;
                row.open_hours  = null;
            }
            else if(hours == '0') {
                
                row.open        =  false;
                row.open_hours  = null;
            }
            else if(hours) {
                
                row.open_hours = [];
  
                for(var h in hours) {
  
                    if(!hours.hasOwnProperty(h))continue;
  
                    var hr_span = hours[h].split(' - ');
  
                    //time per day hack
                    var start_time = hr_span[0];
                    var end_time   = hr_span[1];
  
  
                    
                    var _start = (start_time != 0)?asl_engine.helper.asl_timeConvert(start_time):0,
                        _end   = (end_time != 0)?asl_engine.helper.asl_timeConvert(end_time):24;
  
                    //hack for end
                    if(_end == 0)_end = 24;
                  
  
                    //  Do all the testing when open is false
                    if(!row.open) {
  
                        //  Simple Checking, Normal timing
                        if(_start < _end) {
  
                            // Is Today is used as only Today timing will be opened and they will be considered only and avoid other days, also once open saved
                            if(!row.open) {
  
                                row.open = (_start && _end)? asl_engine.helper.between(current_hr_min, _start, _end): false;
                            }
                        }
                        //  Complex Checking
                        else {
  
                            var duration =  24 - _start  + _end;
  
                            // Convert the times in the Dates
                            var from_date = new Date();
                            from_date.setHours(Math.floor(_start));
                            from_date.setMinutes(getDecimal(_start));
  
                            // Validate the Correct day
                            if(time_now.getDay() != days_number[current_day]) {
                                from_date.subDays(1);
                            }
  
  
                            var to_date = new Date(from_date.getTime());
                            to_date.addHours(duration);
                            to_date.setMinutes(getDecimal(_end));
  
                            //  Validate if it is in asl_engine.helper.between the timing, set Open is true
                            if(time_now > from_date && time_now < to_date) {
                                row.open = true;
                            }
                        }
                    }
  
                    
                    //Change the format if 24 hours setting
                    if(asl_configuration.time_24) {
  
                        
                        _start += 0.01;
                        _start  = parseFloat(_start).toFixed(2);
                        var _s_time = String(_start).split('.');
                        
                        _s_time[0]  = asl_engine.helper.asl_leadzero(parseInt(_s_time[0]));
                        _s_time[1]  = asl_engine.helper.asl_leadzero((parseInt(_s_time[1]) - 1));
                        start_time = _s_time.join(':');
  
  
                        _end += 0.01;
                        _end = parseFloat(_end).toFixed(2);
  
                        var _e_time = String(_end).split('.');
                        _e_time[0]  = asl_engine.helper.asl_leadzero(parseInt(_e_time[0]));
                        _e_time[1]  = asl_engine.helper.asl_leadzero((parseInt(_e_time[1]) - 1));
                        end_time = _e_time.join(':');
                    }
                    else {
  
                        var str_s_time = start_time.split(':'),
                            str_e_time = end_time.split(':');
                        
  
                        if(str_s_time[0]) {
                            str_s_time[0] = asl_engine.helper.asl_leadzero(parseInt(str_s_time[0]));
                        } 
  
                        start_time = str_s_time.join(':');
  
                        if(str_e_time[0]) {
                            str_e_time[0] = asl_engine.helper.asl_leadzero(parseInt(str_e_time[0]));
                        } 
  
                        end_time = str_e_time.join(':');
                    }
  
  
                    row.open_hours.push(start_time+' - '+end_time);
                }
  
                row.open_hours = (row.open_hours.length > 0)?row.open_hours.join(' <br> '):null;
            }
  
          }
          else row.open =  true;
        };
  
        var show_categories = asl_configuration.show_categories || asl_configuration.category_accordion;
  
        //  Show the link
        var have_link       = (asl_configuration.slug_link == '1')? true: false,
            slug_url        = asl_configuration.rewrite_slug + '/',
            is_slug_url     = (asl_configuration.link_type == '1' && asl_configuration.rewrite_slug)? true: false;
        
        var has_desc        = (asl_configuration.additional_info == '0')? false: true,
            has_desc_link   = (asl_configuration.additional_info == '2')? true: false;
  
        var no_locality_street = (asl_categories.template != '1')? true: false;
  
        //  Loop over the stores
        for (var i = 0; i < rows.length; i++) {
  
          var row = rows[i];
  
          row.id = parseInt(row.id);
          row.ordr = (!row.ordr || isNaN(row.ordr)) ? 0 : parseInt(row.ordr);
          row.lat = parseFloat(row.lat);
          row.lng = parseFloat(row.lng);
          row.logo_id = (row.logo_id && !isNaN(row.logo_id)) ? parseInt(row.logo_id) : row.logo_id;
  
          var position = new google.maps.LatLng(row.lat, row.lng);
  
          //When Open Hours
          row.open_hours = (row.open_hours) ? (row.open_hours) : null;
  
  
          if (!row.state) row.state = '';
  
          var locality = asl_engine.helper.implode([row.city, row.state, row.postal_code], ', ');
          var addr     = [row.street, locality];
  
          row.address       = asl_engine.helper.implode(addr, '<br>');
          var _categories   = row.categories ? row.categories.split(",") : [],
            row_categories  = [];
  
  
          //If categories are enabled
          if (show_categories) {
  
            var c_names = [];
            var c_ids   = [];
  
            for (var k in _categories) {
  
              var _cat_id = _categories[k].toString();
  
              if (asl_categories[_cat_id]) {
  
                asl_categories[_cat_id].len++;
                //Add the Features
                row_categories.push(asl_categories[_cat_id]);
                c_names.push(asl_categories[_cat_id].name);
                c_ids.push(asl_categories[_cat_id].id);
  
              } else
                delete _categories[k];
            }
  
  
            //  Sort by Category
            row.cat = (c_names && c_names[0])? c_names[0]: '';
            
  
            row.c_ids   = c_ids;
            row.c_names = asl_engine.helper.implode(c_names, ', ');
  
            //julie category sort by __order
            row.categories = row_categories;
          }
  
         
  
          row.city    = $.trim(row.city) /*.replace(asl_configuration.regex, '')*/ ;
          row.country = $.trim(row.country);
  
          if (!row.state)
            row.state = '';
  
  
          row.marker_id = (row.marker_id) ? row.marker_id.toString() : "";
  
          //  Hide the hours
          if(asl_configuration.hide_hours) {
            row.open_hours = null;
          }
          else {
            (asl_configuration.week_hours == '0')? getTodayHours(row): getAllHours(row);
          }
  
          //  Show/Hide Logo
          if(asl_configuration.hide_logo) {
            row.path = null;          
          }
  
          //  Description
          if(has_desc) {
            row.desc_link = has_desc_link;
          }
          else
            row.description = null;
  
          //  Link URL
          if(have_link) {
            row.link = (is_slug_url)? (slug_url + row.slug + '/') : row.website;
          }
  
          //Saving all the stores
          var store = new asl_locator.Store(row.id, position, _categories, row);
          stores.push(store);
        }
  
        return stores;
      };
  
  
      /*INITIALIZE THE Data Source*/
      var data_source = new asl_engine.dataSource();
  
      /**
       * [getStores Get Stores on Each Pan]
       * @param  {[type]}   bounds          [description]
       * @param  {[type]}   active_features [description]
       * @param  {Function} callback        [description]
       * @return {[type]}                   [description]
       */
      data_source.getStores = function(bounds, active_features, callback) {
  
        var that    = this,
            stores  = [];
  
        var func_name = (asl_configuration.and_filter)? 'hasAllCategory': 'hasAnyCategory';
  
        //  Has any Filter Active
        for (var i = 0, store; store = this.stores_[i]; i++) {
          if (store[func_name](active_features)) {
            stores.push(store);
          }
        }
  
        callback(stores);
      };
  
  
      /**
       * [findStore Find a Store Having ID]
       * @param  {[type]} _store_id [description]
       * @return {[type]}           [description]
       */
      data_source.findStore = function(_store_id) {
  
        _store_id  = parseInt(_store_id);
  
        for (var i = 0, store; store = this.stores_[i]; i++) {
          
          if(store.id_ == _store_id)
            return store;
        }
  
        return null;
      };
  
      /**
       * [getStores Get Stores on Each Pan]
       * @param  {[type]}   bounds          [description]
       * @param  {[type]}   active_features [description]
       * @param  {Function} callback        [description]
       * @param  {[type]}   _no_ajax        [description]
       * @return {[type]}                   [description]
       */
      data_source.allStores = function() {
  
        return this.stores_;
      };
  
  
      //  Download the Advanced Marker library first
      if(asl_configuration.advanced_marker) {
  
        google.maps.importLibrary("marker").then(function(){
  
            data_source.load_locator();
        });
      }
      //  Simple Markers
      else
        data_source.load_locator();
  
    })(jQuery);
  };
  
  
  jQuery(document).ready(asl_store_locator);