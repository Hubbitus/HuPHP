function logo_initialize(){
	for (i=0; i<LOGO.length; i++){
	cs = dd.elements[LOGO[i].gname + "cropScreen"];
	//Инициализируем свойствами нужных, для прямого обращения дальше. короткие алисы еще
	cs.gname = LOGO[i].gname;//one_imgResizer, one_cropScreen...
	cs.phoneW = LOGO[i].phoneW;
	cs.phoneH = LOGO[i].phoneH;
	cs.IWidth = LOGO[i].IWidth;
	cs.IHeight = LOGO[i].IHeight;
	cs.resized = rsd = dd.elements[cs.gname + "imgResized"];
	cs.preimg = pre = dd.elements[cs.gname + "PreImg"];
	cs.resizer = rsr = dd.elements[cs.gname + "imgResizer"];

		if (typeof(dd.elements[cs.gname + "thumb"]) != "undefined"){
		cs.thumb = thm = dd.elements[cs.gname + "thumb"];
		cs.track = trk = dd.elements[cs.gname + "track"];

		//Сдвинем на половину один
		trk.moveTo( trk.defx-Math.round(thm.w/2), trk.y);
//???		trk.defx = trk.defx-Math.round(thm.w/2);
		//И на половину другой
		thm.moveTo( (trk.x+Math.round(trk.w/2 - thm.w/2)), trk.y);
		trk.addChild(thm);
		thm.defx = thm.x + Math.round(thm.w/2);
		thm.csobj = cs;
//		thm.setZ(trk.z+1);
		thm.setDragFunc(zoomInOut);
		}
	//Перемещаем на картинку, заодно корректируем начальные позиции, раз переместили
	cs.moveTo(cs.defx = rsd.x, cs.defy = rsd.y);
	//Показываем после инициализации
	cs.show();
	//Устанавливаем свои, отдельные обработчики
	rsr.setPickFunc(mymy_PickFunc);
	cs.setDropFunc(mymy_DropFunc);
//+	Для CS отдельные обработчики - создаем превью
	cs.setDragFunc(setFormValues);
	cs.setResizeFunc(setFormValues);
//\+
	cs.setOpacity(0.3);
	//dd.elements.imgResizer.setOpacity(0.4);
	rsd.setZ(1);
	cs.setZ(2);
	rsr.setZ(3);
	//Записываем, чтобы потом не считать каждый раз в setFormValues
	pre.my_wratio = cs.phoneW * cs.IWidth;
	pre.my_hratio = cs.phoneH * cs.IHeight;
	setFormValues(cs);
		if (!dd.op) cs.setBgColor('white');
		else{
		dd.obj = cs;
		mymy_DropFunc();//Это ресайзер сдвинуть в Опере
		}
	LOGO[i] = cs;
	}

	if (window.logoforminit) return logoforminit();//Если надо, еще формочку иним
	else return true;
}//f logo_init
//addEventHandler(window, "onload", "logo_init");

window.oldLoad = window.onload;
window.onload = function (){
	if (window.oldLoad) {window.oldLoad();}
logo_initialize();
}
