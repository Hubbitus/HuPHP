<style type="text/css">

.dhtmltooltip{
position: absolute;
width: 150px;
border: 2px solid black;
-moz-border-radius:8px;
padding: 2px;
background-color: lightyellow;
visibility: hidden;
z-index: 100;
/*Remove below line to remove shadow. Below line should always appear last within this CSS*/
filter: progid:DXImageTransform.Microsoft.Shadow(color=gray,direction=135);
}

/* From Scorpion.ru
#tooltip {
background:#FFFFFF;
border:2px solid #ADC1CE;
font:0.7em Verdana,Geneva,Arial,Helvetica,sans-serif;
FONT-SIZE:11px;
margin:0px;
-moz-border-radius:8px;
padding:3px 5px;
position:absolute;
visibility:hidden;z-index:100;
}
*/
</style>

<script type="text/javascript">

/***********************************************
Взял отсюдова: http://www.dynamicdrive.com/dynamicindex5/dhtmltooltip.htm
* Cool DHTML tooltip script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/
var offsetxpoint=-60 //Customize x offset of tooltip
var offsetypoint=20 //Customize y offset of tooltip
var ie=document.all
var ns6=document.getElementById && !document.all
var enabletip=false

function tooltipinit(id){
	if (typeof id =="undefined") id = "dhtmltooltip";
	window.tipobj=document.all? document.all[id] : document.getElementById? document.getElementById(id) : ""
}

function ietruebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function ddrivetip(thetext, thecolor, thewidth, theheight){
	if (ns6||ie){
		if (typeof thewidth !="undefined") window.tipobj.style.width=thewidth;
		if (typeof theheight!="undefined") window.tipobj.style.height=theheight;
		if (typeof thecolor !="undefined" && thecolor!="") window.tipobj.style.backgroundColor = thecolor;

		if (typeof thetext!="undefined" && thetext!="") window.tipobj.innerHTML=thetext;
	enabletip=true
	return false
	}
}

function positiontip(e){
	if (enabletip){
	var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
	var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;
	//Find out how close the mouse is to the corner of the window
	var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
	var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

	var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

		//if the horizontal distance isn't enough to accomodate the width of the context menu
		if (rightedge < window.tipobj.offsetWidth)
		//move the horizontal position of the menu to the left by it's width
		window.tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-window.tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-window.tipobj.offsetWidth+"px"
		else if (curX<leftedge)
		window.tipobj.style.left="5px"
		else
		//position the horizontal position of the menu where the mouse is positioned
		window.tipobj.style.left=curX+offsetxpoint+"px"

		//same concept with the vertical position
		if (bottomedge<window.tipobj.offsetHeight)
		window.tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-window.tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-window.tipobj.offsetHeight-offsetypoint+"px"
		else
		window.tipobj.style.top=curY+offsetypoint+"px"
	window.tipobj.style.visibility="visible"
	}
}

function hideddrivetip(){
	if (ns6||ie){
	enabletip=false
	window.tipobj.style.visibility="hidden"
	window.tipobj.style.left="-1000px"
	window.tipobj.style.backgroundColor=''
	window.tipobj.style.width=''
	}
}

document.onmousemove=positiontip
</script>