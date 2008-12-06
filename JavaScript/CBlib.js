//My Cross Browser Java-Script Library

/* Не помню откуда взял и переделал
xoopsGetElementById
*/
function CBgetElementById(id){//CrossBrowser getElementById
	if (document.getElementById){
	return document.getElementById(id);
	}
	else if (document.all){
	return document.all[id];
	}
	else if (document.layers && document.layers[id]){
	return (document.layers[id]);
	}
	else return false;
}//f CBgetElementById

//Пример: addEventHandler(window.onload, 'myFunc'){
function addEventHandler(obj, ev, handl){
//alert('obj=' + obj);
//alert('ev=' + ev);
//alert('obj[ev]=' + obj[ev]);
//alert('handl=' + handl);
/*
	var oldHundl = obj_ev;
	obj_ev = function (){
	setTimeout("handl();", 1);
		if (oldHandl) oldHandl();
	}
*/
	window['oldHundl' + handl] = obj[ev];
	obj[ev] = function (){
		if (window['oldHundl' + handl]) var ret = window['oldHundl' + handl]();
	return ret && eval(handl + "();");
	}
//alert('Nobj=' + obj);
//alert('Nev=' + ev);
//alert('Nobj[ev]=' + obj[ev]);
}