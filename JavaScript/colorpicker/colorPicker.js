/*
Requirement: Internet Explorer or Mozilla
Author: Jean-Luc Antoine
Submitted: 26/03/2005
Category: 4K
Взято с:
URL: http://www.interclasse.com/scripts/colorpicker.php

Changed: Pavel Alexeev
*/

//Init
var total=1657;
var X=Y=j=RG=B=0;
var aR=new Array(total);
var aG=new Array(total);
var aB=new Array(total);
	for (var i=0;i<256;i++){
	aR[i+510]=aR[i+765]=aG[i+1020]=aG[i+5*255]=aB[i]=aB[i+255]=0;
	aR[510-i]=aR[i+1020]=aG[i]=aG[1020-i]=aB[i+510]=aB[1530-i]=i;
	aR[i]=aR[1530-i]=aG[i+255]=aG[i+510]=aB[i+765]=aB[i+1020]=255;
		if(i<255){aR[i/2+1530]=127;aG[i/2+1530]=127;aB[i/2+1530]=127;}
	}

var hexbase=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
var i=0;
var jl=new Array();
	for(x=0;x<16;x++)
		for(y=0;y<16;y++)jl[i++]=hexbase[x]+hexbase[y];

function ColorPicker(){//CONSTRUCTOR
	if (this.ColorsTable) return this.ColorsTable;

this.ColorsTable='<'+'table border="0" cellspacing="0" cellpadding="0" onMouseover="ColorPicker.t(event)" onClick="ColorPicker.choose()">';
var H=W=63;
	for (Y=0;Y<=H;Y++){
	s='<'+'tr>';
	j=Math.round(Y*(510/(H+1))-255);
		for (X=0;X<=W;X++){
		i=Math.round(X*(total/W));
		R=aR[i]-j;
			if(R<0)R=0;
			if(R>255||isNaN(R))R=255;
		G=aG[i]-j;
			if(G<0)G=0;
			if(G>255||isNaN(G))G=255;
		B=aB[i]-j;
			if(B<0)B=0;
			if(B>255||isNaN(B))B=255;
		s=s+'<'+'td id=sz bgcolor=#'+jl[R]+jl[G]+jl[B]+'><'+'/td>';
		}
	this.ColorsTable+=s+'<'+'/tr>';
	}
this.ColorsTable+='<'+'/table>';
//return this.ColorsTable;

//Настройки, можно перезаписывать
this.settings = {direct: 'ld'};
//direct - направление рисования. ld-leftDown, ul-UpLeft
}//f ColorPicker

var ColorPicker = new ColorPicker();

//Основная инициализация
document.write('<'+'style>'+ '#sz {width: 4; height: 3}' + '</' + 'style>');
document.write('<'+'table'+' id="ColorPicker" style="position:absolute; visibility: hidden; top: 0; left: 0; z-index: 255; border: 6px ridge green; background-color:#d1ffc7"><tr><'+'td>'+ColorPicker.ColorsTable+'<'+'td id=temoin width=30><'+'/td><'+'/tr><'+'tr><'+'th colspan=2 id=choix height=30>#ffffff</th></tr><tr><th colspan=2><input type=button value="Ok" onclick="ColorPicker.Ok()"><input type=button value="Cancel" onclick="ColorPicker.Cancel()"></th></table>');
//\Основная инициализация

ColorPicker.choose = function (){
	var jla=document.getElementById('choix');
	jla.innerHTML=artabus;
	jla.style.backgroundColor=artabus;
	this.lastColor = artabus;
}

//ColorPicker_Ok для переопределения и использования результатов ColorPicker_Cancel
ColorPicker.Ok = function (){
	this.hide();
}

ColorPicker.Cancel = function (){
	this.hide();
}

ColorPicker.hide = function(){
	document.getElementById('ColorPicker').style.visibility = 'hidden';
}

ColorPicker.show = function(evnt, curName, x, y){
this.CurrentName = curName;//Для того чтобы можно было идентифицировать кто где что!
CPobj=document.getElementById('ColorPicker');
//(window.pageXOffset || document.body.scrollLeft) ???
	if (this.settings.direct == 'ld'){
	CPobj.style.left =x? x : ((window.pageXOffset? window.pageXOffset : document.body.scrollLeft) + evnt.clientX);
	CPobj.style.top = y? y : ((window.pageYOffset? window.pageYOffset : document.body.scrollTop ) + evnt.clientY);
	}
	else{//'ul'
	CPobj.style.left =x? x : ((window.pageXOffset? window.pageXOffset : document.body.scrollLeft) + evnt.clientX - CPobj.offsetWidth);
	CPobj.style.top = y? y : ((window.pageYOffset? window.pageYOffset : document.body.scrollTop ) + evnt.clientY - CPobj.offsetHeight);
	}
CPobj.style.visibility = 'visible';
}

var ns6=document.getElementById&&!document.all;
var ie=document.all;
var artabus='';

ColorPicker.t = function(e){
source=ie?event.srcElement:e.target;
	if(source.tagName=="TABLE") return;
	while(source.tagName!="TD" && source.tagName!="HTML") source=ns6?source.parentNode:source.parentElement;
document.getElementById('temoin').style.backgroundColor=artabus=source.bgColor;
}