/* my_PickFunc IS AUTOMATICALLY CALLED WHEN AN ITEM STARTS TO BE DRAGGED.
The following objects/properties are accessible from here:

- dd.e: current mouse event
- dd.e.property: access to a property of the current mouse event.
  Mostly requested properties:
  - dd.e.x: document-related x co-ordinate
  - dd.e.y: document-related y co-ord
  - dd.e.src: target of mouse event (not identical with the drag drop object itself).
  - dd.e.button: currently pressed mouse button. Left button: dd.e.button <= 1

- dd.obj: reference to currently dragged item.
- dd.obj.property: access to any property of that item.
- dd.obj.method(): for example dd.obj.resizeTo() or dd.obj.swapImage() .
  Mostly requested properties:
	- dd.obj.name: image name or layer ID passed to SET_DHTML();
	- dd.obj.x and dd.obj.y: co-ordinates;
	- dd.obj.w and dd.obj.h: size;
	- dd.obj.is_dragged: 1 while item is dragged, else 0;
	- dd.obj.is_resized: 1 while item is resized, i.e. if <ctrl> or <shift> is pressed, else 0

For more properties and details, visit the API documentation
at http://www.walterzorn.com/dragdrop/api_e.htm (english) or
http://www.walterzorn.de/dragdrop/api.htm (german)    */
	function mymy_PickFunc(){
	dd.obj.hide();//Спрячем "ресайзитель"
	dd.obj = dd.obj.parent; //cropScreen
	//Это пришлось вынести из DRAG, т.к. подменяю объект выше, оно не учитывается
	dd.whratio = dd.obj.scalable ? dd.obj.defw/dd.obj.defh : 0;
//	dd.e.modifKey = true;
	dd.setMovHdl(RESIZE);
//	dd.moveFunc = RESIZE;//НЕОБХОДИМО, чтобы ИЕ не зациклился!
	dd.moveFunc = null;
//?	dd.reszTo(dd.obj.w, dd.obj.h);
//	return true;
	}//f mymy_PickFunc

	function mymy_DropFunc(){
	dd.obj.show();//Покажем обратно
	//Ставим в нижний правый угол
	dd.obj.resizer.moveTo( (dd.obj.x + dd.obj.w - dd.obj.resizer.w - 2), (dd.obj.y + dd.obj.h - dd.obj.resizer.h - 2));
	}//f mymy_DropFunc

/*
//	function my_DragFunc(){
	function mymy_DragFunc(){
	setFormValues(dd.obj);
	}//f my_DragFunc

//	function my_ResizeFunc(){
	function mymy_ResizeFunc(){
	setFormValues(dd.obj);
	}//f my_ResizeFunc
*/

	//Занимается собсвенно СОЗДАНИЕМ УМЕНЬШЕННОГО ПРЕВЬЮ!!
	function setFormValues(csobj){
		if (typeof csobj == "undefined") csobj = dd.obj;
	obj = csobj.preimg;
	rsdobj = csobj.resized;

		if (csobj.w != csobj.div.clientWidth){//FF как минимум. У него размеры по внешней рамке, если она есть!
		csobj.realw = csobj.div.clientWidth;
		csobj.realdefw = csobj.defw - (csobj.w - csobj.realw);
		csobj.realh = csobj.div.clientHeight;
		csobj.realdefh = csobj.defh - (csobj.h - csobj.realh);
		} else{
		csobj.realw = csobj.w;
		csobj.realdefw = csobj.defw;
		csobj.realh = csobj.h;
		csobj.realdefh = csobj.defh;
		}

	obj.resizeTo( Math.round(obj.my_wratio / csobj.realw), Math.round(obj.my_hratio / csobj.realh));
	var ttX = Math.round(obj.defx + (csobj.defx - csobj.x )*csobj.realdefw/csobj.realw);
	var ttY = Math.round(obj.defy + (csobj.defy - csobj.y)*csobj.realdefh/csobj.realh);
	obj.moveTo( ttX, ttY );

	var screenTop = Math.round((csobj.y - csobj.defy)*csobj.realdefh/csobj.realh);
	var screenLeft = Math.round((csobj.x - csobj.defx)*csobj.realdefw/csobj.realw);
//	var screenRight = screenLeft + csobj.phoneW + 1;
//	var screenBottom = screenTop + csobj.phoneH;
	var screenRight = screenLeft + obj.defw + 1;
	var screenBottom = screenTop + obj.defh;


	obj.css.clip = "rect(" + screenTop + "px " + screenRight + "px " + screenBottom + "px " + screenLeft + "px)";
//	window.status += "rect(" + screenTop + "px " + screenRight + "px " + screenBottom + "px " + screenLeft + "px)";
	}//f setFormValues

	//Переключения режима масштабирования изображения
	function toggleScalable(gnm){
	var i = 0;
		//Ищем нужный
		while (LOGO[i].gname != gnm) i++;
		if (LOGO[i].scalable) LOGO[i].setResizable(true);
		else LOGO[i].setScalable(true);
	return true;
	}//f toggleScalable

//////////////////////////////////////////////////////////////////
////////////////////Вспомогательное НЕ необходимое////////////////
//////////////////////////////////////////////////////////////////
	//Подготовка формы, заполнение всех необходимых параметров!
	//Должна вешаться эта функция на onsubmit формы!
	function picReady(){
	var params = CBgetElementById('picparams');
	params.value = '';
	var realName = '';
		for (i=0; i<LOGO.length; i++){
		realName = LOGO[i].gname.substr(1);//Обрезаем обратно, добавленную "l"
		params.value += 'pic[' + realName + ']' + '[left]=' + (LOGO[i].x - LOGO[i].resized.x) + '&';
		params.value += 'pic[' + realName + ']' + '[top]=' + (LOGO[i].y - LOGO[i].resized.y) + '&';
		params.value += 'pic[' + realName + ']' + '[width]=' + LOGO[i].realw + '&';
		params.value += 'pic[' + realName + ']' + '[height]=' + LOGO[i].realh + '&';
		params.value += 'pic[' + realName + ']' + '[PWidth]=' + LOGO[i].phoneW + '&';
		params.value += 'pic[' + realName + ']' + '[PHeight]=' + LOGO[i].phoneH + '&';
		params.value += 'pic[' + realName + ']' + '[pswidth]=' + LOGO[i].resized.w + '&';//PictureSourceWidth
		params.value += 'pic[' + realName + ']' + '[psheight]=' + LOGO[i].resized.h + '&';//PictureSourceHeight
		params.value += 'pic[' + realName + ']' + '[src]=' + LOGO[i].resized.src + '&';
			if (LOGO[i].preimg.fillColor) params.value += 'pic[' + realName + ']' + '[fillColor]=' + LOGO[i].preimg.fillColor + '&';
		}
//	params.parentNode.submit();
//alert(params.value); return false;
	return true;
	}//f picReady

	function zoomInOut(){
	thumb = dd.obj;
	zoomedObj = thumb.csobj.resized;
	preObj = thumb.csobj.preimg;
	var zLimit = (thumb.parent.w - thumb.w)/2 - 2;
	var zratio = 1 -(thumb.defx-thumb.x-Math.round(thumb.w/2)) / zLimit;
	zoomedObj.resizeTo( dd.Int(zratio * zoomedObj.defw), dd.Int(zratio * zoomedObj.defh) );
	preObj.my_wratio = thumb.csobj.phoneW * zoomedObj.w;
	preObj.my_hratio = thumb.csobj.phoneH * zoomedObj.h;
	//чтобы превью пересчиталось
	setFormValues(thumb.csobj);
	}
