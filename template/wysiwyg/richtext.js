// Cross-Browser Rich Text Editor
// http://www.kevinroth.com/rte/demo.htm
// Written by Kevin Roth (kevin@NOSPAMkevinroth.com - remove NOSPAM)

//init variables
var isRichText = false;
var rng;
var currentRTE;
var allRTEs = "";

var isIE;
var isGecko;
var isSafari;

var imagesPath;
var includesPath;
var cssFile;

function getRTE(name){
	//contributed by Bob Hutzel (thanks Bob!)
	if (document.all) {
		return frames[rte].document;
	} else {
		return document.getElementById(rte).contentWindow.document;
	}
}

function initRTE(imgPath, incPath, css) {
	//set browser vars
	var ua = navigator.userAgent.toLowerCase();
	isIE = ((ua.indexOf("msie") != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1)); 
	isGecko = (ua.indexOf("gecko") != -1);
	isSafari = (ua.indexOf("safari") != -1);

	//check to see if designMode mode is available
	if (document.getElementById && document.designMode && !isSafari) {
		isRichText = true;
	}

	//set paths vars
	imagesPath = imgPath;
	includesPath = incPath;
	cssFile = css;

	//for testing standard textarea, uncomment the following line
	//isRichText = false;
}

function writeRichText(rte, html, width, height, buttons, readOnly) {
	if (isRichText) {
		if (allRTEs.length > 0) allRTEs += ";";
		allRTEs += rte;
		writeRTE(rte, html, width, height, buttons, readOnly);
	} else {
		writeDefault(rte, html, width, height, buttons, readOnly);
	}
}

function writeDefault(rte, html, width, height, buttons, readOnly) {
	if (!readOnly) {
		document.writeln('<textarea name="' + rte + '" id="' + rte + '" style="width: ' + width + '; height: ' + height + 'px;">' + html + '</textarea>');
	} else {
		document.writeln('<textarea name="' + rte + '" id="' + rte + '" style="width: ' + width + '; height: ' + height + 'px;" readonly>' + html + '</textarea>');
	}
}

function writeRTE(rte, html, width, height, buttons, readOnly) {
	if (readOnly) buttons = false;
	if (buttons == true) {
		document.writeln('<style type="text/css">');
		document.writeln('.btnImage {cursor: pointer; cursor: hand;}');
		document.writeln('</style>');
		document.writeln('<table id="Buttons1_' + rte + '">');
		document.writeln('	<tr>');
		document.writeln('		<td>');
		document.writeln('			<select id="formatblock_' + rte + '" onchange="Select(\'' + rte + '\', this.id);">');
		document.writeln('				<option value="<p>">Normal</option>');
		document.writeln('				<option value="<p>">Paragraph</option>');
		document.writeln('				<option value="<h1>">Heading 1 <h1></option>');
		document.writeln('				<option value="<h2>">Heading 2 <h2></option>');
		document.writeln('				<option value="<h3>">Heading 3 <h3></option>');
		document.writeln('				<option value="<h4>">Heading 4 <h4></option>');
		document.writeln('				<option value="<h5>">Heading 5 <h5></option>');
		document.writeln('				<option value="<h6>">Heading 6 <h6></option>');
		document.writeln('				<option value="<address>">Address <ADDR></option>');
		document.writeln('				<option value="<pre>">Formatted <pre></option>');
		document.writeln('			</select>');
		document.writeln('		</td>');
		document.writeln('		<td>');
		document.writeln('			<select id="fontname_' + rte + '" onchange="Select(\'' + rte + '\', this.id)">');
		document.writeln('				<option value="Font" selected>Font</option>');
		document.writeln('				<option value="Arial, Helvetica, sans-serif">Arial</option>');
		document.writeln('				<option value="Courier New, Courier, mono">Courier New</option>');
		document.writeln('				<option value="Comic Sans MS">Comic Sans MS</option>');
		document.writeln('				<option value="Script MT Bold">Script MT Bold</option>');
		document.writeln('				<option value="Times New Roman, Times, serif">Times New Roman</option>');
		document.writeln('				<option value="Verdana, Arial, Helvetica, sans-serif">Verdana</option>');
		document.writeln('			</select>');
		document.writeln('		</td>');
		document.writeln('		<td>');
		document.writeln('			<select unselectable="on" id="fontsize_' + rte + '" onchange="Select(\'' + rte + '\', this.id);">');
		document.writeln('				<option value="Size">Size</option>');
		document.writeln('				<option value="1">1</option>');
		document.writeln('				<option value="2">2</option>');
		document.writeln('				<option value="3">3</option>');
		document.writeln('				<option value="4">4</option>');
		document.writeln('				<option value="5">5</option>');
		document.writeln('				<option value="6">6</option>');
		document.writeln('				<option value="7">7</option>');
		document.writeln('			</select>');
		document.writeln('		</td>');
		document.writeln('	</tr>');
		document.writeln('</table>');
		document.writeln('<table id="Buttons2_' + rte + '" cellpadding="1" cellspacing="0">');
		document.writeln('	<tr>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'bold.gif" width="25" height="24" alt="Bold" title="Bold" onClick="FormatText(\'' + rte + '\', \'bold\', \'\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'italic.gif" width="25" height="24" alt="Italic" title="Italic" onClick="FormatText(\'' + rte + '\', \'italic\', \'\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'underline.gif" width="25" height="24" alt="Underline" title="Underline" onClick="FormatText(\'' + rte + '\', \'underline\', \'\')"></td>');
		document.writeln('		<td>&nbsp;</td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'left_just.gif" width="25" height="24" alt="Align Left" title="Align Left" onClick="FormatText(\'' + rte + '\', \'justifyleft\', \'\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'centre.gif" width="25" height="24" alt="Center" title="Center" onClick="FormatText(\'' + rte + '\', \'justifycenter\', \'\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'right_just.gif" width="25" height="24" alt="Align Right" title="Align Right" onClick="FormatText(\'' + rte + '\', \'justifyright\', \'\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'justifyfull.gif" width="25" height="24" alt="Justify Full" title="Justify Full" onclick="FormatText(\'' + rte + '\', \'justifyfull\', \'\')"></td>');
		document.writeln('		<td>&nbsp;</td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'hr.gif" width="25" height="24" alt="Horizontal Rule" title="Horizontal Rule" onClick="FormatText(\'' + rte + '\', \'inserthorizontalrule\', \'\')"></td>');
		document.writeln('		<td>&nbsp;</td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'numbered_list.gif" width="25" height="24" alt="Ordered List" title="Ordered List" onClick="FormatText(\'' + rte + '\', \'insertorderedlist\', \'\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'list.gif" width="25" height="24" alt="Unordered List" title="Unordered List" onClick="FormatText(\'' + rte + '\', \'insertunorderedlist\', \'\')"></td>');
		document.writeln('		<td>&nbsp;</td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'outdent.gif" width="25" height="24" alt="Outdent" title="Outdent" onClick="FormatText(\'' + rte + '\', \'outdent\', \'\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'indent.gif" width="25" height="24" alt="Indent" title="Indent" onClick="FormatText(\'' + rte + '\', \'indent\', \'\')"></td>');
		document.writeln('		<td><div id="forecolor_' + rte + '"><img class="btnImage" src="' + imagesPath + 'textcolor.gif" width="25" height="24" alt="Text Color" title="Text Color" onClick="FormatText(\'' + rte + '\', \'forecolor\', \'\')"></div></td>');
		document.writeln('		<td><div id="hilitecolor_' + rte + '"><img class="btnImage" src="' + imagesPath + 'bgcolor.gif" width="25" height="24" alt="Background Color" title="Background Color" onClick="FormatText(\'' + rte + '\', \'hilitecolor\', \'\')"></div></td>');
		document.writeln('		<td>&nbsp;</td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'hyperlink.gif" width="25" height="24" alt="Insert Link" title="Insert Link" onClick="FormatText(\'' + rte + '\', \'createlink\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'image.gif" width="25" height="24" alt="Add Image" title="Add Image" onClick="AddImage(\'' + rte + '\')"></td>');
		if (isIE) document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'spellcheck.gif" width="25" height="24" alt="Spell Check" title="Spell Check" onClick="checkspell()"></td>');
//		document.writeln('		<td>&nbsp;</td>');
//		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'cut.gif" width="25" height="24" alt="Cut" title="Cut" onClick="FormatText(\'' + rte + '\', \'cut\')"></td>');
//		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'copy.gif" width="25" height="24" alt="Copy" title="Copy" onClick="FormatText(\'' + rte + '\', \'copy\')"></td>');
//		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'paste.gif" width="25" height="24" alt="Paste" title="Paste" onClick="FormatText(\'' + rte + '\', \'paste\')"></td>');
		document.writeln('		<td>&nbsp;</td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'undo.gif" width="25" height="24" alt="Undo" title="Undo" onClick="FormatText(\'' + rte + '\', \'undo\')"></td>');
		document.writeln('		<td><img class="btnImage" src="' + imagesPath + 'redo.gif" width="25" height="24" alt="Redo" title="Redo" onClick="FormatText(\'' + rte + '\', \'redo\')"></td>');
		document.writeln('	</tr>');
		document.writeln('</table>');
	}
	document.writeln('<iframe id="' + rte + '" name="' + rte + '" width="' + width + '" height="' + height + '" frameborder=1 style="border: 3 ridge"></iframe>');
	if (!readOnly) document.writeln('<br /><input type="checkbox" id="chkSrc' + rte + '" onclick="toggleHTMLSrc(\'' + rte + '\');" />&nbsp;View Source');
	document.writeln('<iframe width="254" height="174" id="cp' + rte + '" src="' + includesPath + 'palette.htm" marginwidth="0" marginheight="0" scrolling="no" style="visibility:hidden; display: none; position: absolute;"></iframe>');
	document.writeln('<input type="hidden" id="hdn' + rte + '" name="' + rte + '" value="">');
	document.getElementById('hdn' + rte).value = html;
	enableDesignMode(rte, html, readOnly);
	//my
//	setTimeout("enableDesignMode('" + rte + "', '" + html + "');", 1500);
}

function enableDesignMode(rte, html, readOnly) {
	var frameHtml = "<html id=\"" + rte + "\">\n";
	frameHtml += "<head>\n";
	//to reference your stylesheet, set href property below to your stylesheet path and uncomment
	if (cssFile.length > 0) {
		frameHtml += "<link media=\"all\" type=\"text/css\" href=\"" + cssFile + "\" rel=\"stylesheet\">\n";
	}
	frameHtml += "<style>\n";
	frameHtml += "body {\n";
	frameHtml += "	background: #FFFFFF;\n";
	frameHtml += "	margin: 0px;\n";
	frameHtml += "	padding: 0px;\n";
	frameHtml += "}\n";
	frameHtml += "</style>\n";
	frameHtml += "</head>\n";
	frameHtml += "<body>\n";
	frameHtml += html + "\n";
	frameHtml += "</body>\n";
	frameHtml += "</html>";

	if (document.all) {
		var oRTE = frames[rte].document;
		oRTE.open();
		oRTE.write(frameHtml);
		oRTE.close();
		oRTE.onpaste = onPasteCleanWordMesh;
		if (!readOnly) oRTE.designMode = "On";
	} else {
		try {
			if (!readOnly) setTimeout("document.getElementById('" + rte + "').contentDocument.designMode = 'on';", 1000);
			try {
				var oRTE = document.getElementById(rte).contentWindow.document;
				oRTE.open();
				oRTE.write(frameHtml);
				oRTE.close();
				//oRTE.addEventListener("blur", updateRTE(rte), true);
				if (isGecko && !readOnly) {
					//attach a keyboard handler for gecko browsers to make keyboard shortcuts work
					oRTE.addEventListener("keypress", kb_handler, true);
					oRTE.addEventListener("paste", onPasteCleanWordMesh, true);
				}
			} catch (e) {
				alert("Error preloading content.");
			}
		} catch (e) {
			//gecko may take some time to enable design mode.
			//Keep looping until able to set.
			if (isGecko) {
				setTimeout("enableDesignMode('" + rte + "', '" + html + "');", 10);
			} else {
				return false;
			}
		}
	}
}

function updateRTEs() {
	var vRTEs = allRTEs.split(";");
	for (var i = 0; i < vRTEs.length; i++) {
		updateRTE(vRTEs[i]);
	}
}

function updateRTE(rte) {
	//set message value
	var oHdnMessage = document.getElementById('hdn' + rte);
	var oRTE = document.getElementById(rte);
	var readOnly = false;

	//check for readOnly mode
	if (document.all) {
		if (frames[rte].document.designMode != "On") readOnly = true;
	} else {
		if (document.getElementById(rte).contentDocument.designMode != "on") readOnly = true;
	}

	if (isRichText && !readOnly) {
		//if viewing source, switch back to design view
		if (document.getElementById("chkSrc" + rte).checked) {
			document.getElementById("chkSrc" + rte).checked = false;
			toggleHTMLSrc(rte);
		}

		if (oHdnMessage.value == null) oHdnMessage.value = "";
		if (document.all) {
			oHdnMessage.value = finalHTML(frames[rte].document.body.innerHTML);
		} else {
			oHdnMessage.value = finalHTML(oRTE.contentWindow.document.body.innerHTML);
		}
	}
}

function toggleHTMLSrc(rte) {
	var oRTE = getRTE(rte);

	if (document.getElementById("chkSrc" + rte).checked) {//В Source
		document.getElementById("Buttons1_" + rte).style.visibility = "hidden";
		document.getElementById("Buttons2_" + rte).style.visibility = "hidden";
		if (document.all) {
			oRTE.body.innerText = oRTE.body.innerHTML;
			oRTE.body.innerHTML = toSource(oRTE.body.innerHTML);//Конвертяем
		} else {
			oRTE.body.textContent = oRTE.body.innerHTML;
			oRTE.body.innerHTML = toSource(oRTE.body.innerHTML);//Конвертяем
		}
	} else {// в HTML
		document.getElementById("Buttons1_" + rte).style.visibility = "visible";
		document.getElementById("Buttons2_" + rte).style.visibility = "visible";
		oRTE.body.innerHTML = toHTML(oRTE.body.innerHTML);
	}
}

//Функция для конвертации и сохранения форматирования исходников
function toSource(src){
//alert(src);
src = src.replace(/&lt;nn&gt;/gmi, "<br n>");
////Пробелы
src = src.replace(/&lt;sps\sn=["]*(\d+)["]*&gt;&lt;\/sps&gt;\s?/gmi
	,function (spaces, amount){
	var str = '';
		while (str.length < amount*6) str = '&nbsp;' + str;
	return '<sps n=' + amount + '>' + str + '</sps>';
	}
);
src = src.replace(/&amp;#39;/gmi, String.fromCharCode(39));//Это коррекция одинарных кавычек. Функцией для сжатия.
src = src.replace(/\&lt\;\/nn\&gt\;/gmi, '');//Мусор в виде </nn>, вставляемый браузером автоматически - удаляем
//Непонятно откуда взявшиеся <br> тоже чистим
src = src.replace(/<br>/gmi, '');
//alert(src);
return src;
}//f to_source

function toHTML(src){
//alert(src);
////Заменяем "началы строк"
src = src.replace(/<br>\n?/gmi, '<nn>');//Заменяем новые "началы строк"
	if (isIE){
	//Заменяем пустые строки
	src = src.replace(/<\/P>\r\n<P>&nbsp;<\/P>/gmi, '<nn>');
	src = src.replace(/<P>&nbsp;<\/P>\r\n<P>/gmi, '<nn>');

	src = src.replace(/<\/p>[\r\n]+<p>/gmi, '<nn>');//Заменяем новые "началы строк"

	src = src.replace(/<p>/gmi, '');//Автомусор <p>
	src = src.replace(/<\/p>/gmi, '');//и </p>
	}
src = src.replace(/\r?\n/gmi, ' ');//Мусор
src = src.replace(/\<br\sn[=""]*>/gmi, '<nn>'); // Заменяем старые ("" вместо " только для подсветки синтаксиса правильного)
////Заменяем пробелы
src = src.replace(/\&nbsp;/gmi, ' ');//Сначала заменим все &nbsp; на пробелы чтобы привести к общему виду строку "&nbsp;&nbsp; "
src = src.replace(/<sps.*?>/gmi, '');//Теперь удаляем все уже существующие
src = src.replace(/<\/sps>/gmi, '');// теги <sps n=\d> </sps>
// **1** Ниже именно new RegExp, пробел должен быть в кавычках!!! ИЗ-ЗА сжатия скрипта проблема эта!
src = src.replace(new RegExp(' {2,}', 'gmi')
	,function (spaces){ //теперь общая замена...
	return '<sps n=' + spaces.length + '></sps> ';
	}
);
// **1** См. выше
src = src.replace(new RegExp('<nn> ', 'gmi'), '<nn><sps n=1></sps> ');//Пробел в начале строки почему-то игнорируется, заменяем отдельно
src = src.replace(/<(?!(nn)|(br)|(\/?sps)).*?>/gmi, ''); //BUG BUG BUG IE по автоматической конвертации ссылок, и вообще все автоматические теги НАФИГ
////Ну и приводим HTML в соответствие
src = src.replace(/\&lt\;/gmi, '<');
src = src.replace(/\&gt\;/gmi, '>');
src = src.replace(/\&amp\;/gmi, '&');

	if (isIE){//Если <script...> первый он в ИЕ исчезает
	src = src.replace(/(&nbsp;)*<script/gmi, '&nbsp;<script');
	}
//alert(src);
return src;
}//f toHTML

//Конечная обработка для отправки, все подчищаем
function finalHTML(src){
//alert(src);
src = src.replace(/\r?\n/gmi, ' ');//Это для ИЕ нужно, от некоторых "косяков" неучтенных
src = src.replace(/<nn>/gmi, '\n');
src = src.replace(/<\/nn>/gmi, '');
////Пробелы
src = src.replace(/<sps\sn=["]*(\d+)["]*><\/sps>/gmi
	,function (spaces, amount){
	var str = '';
		while (str.length < amount-1) str = ' ' + str;
	return str;
	}
);
//alert(src);
return src;
}//f finalHTML

//Function to format text in the text box
function FormatText(rte, command, option) {
//alert('FormatText()');
	var oRTE;
	if (document.all) {
		oRTE = frames[rte];

		//get current selected range
		var selection = oRTE.document.selection; 
		if (selection != null) {
			rng = selection.createRange();
		}
	} else {
		oRTE = document.getElementById(rte).contentWindow;

		//get currently selected range
		var selection = oRTE.getSelection();
		rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
	}

	try {
		if ((command == "forecolor") || (command == "hilitecolor")) {
			//save current values
			parent.command = command;
			currentRTE = rte;

			//position and show color palette
			buttonElement = document.getElementById(command + '_' + rte);
			document.getElementById('cp' + rte).style.left = getOffsetLeft(buttonElement) + "px";
			document.getElementById('cp' + rte).style.top = (getOffsetTop(buttonElement) + buttonElement.offsetHeight) + "px";
			if (document.getElementById('cp' + rte).style.visibility == "hidden") {
				document.getElementById('cp' + rte).style.visibility = "visible";
				document.getElementById('cp' + rte).style.display = "inline";
			} else {
				document.getElementById('cp' + rte).style.visibility = "hidden";
				document.getElementById('cp' + rte).style.display = "none";
			}
		} else if (command == "createlink") {
			var szURL = prompt("Enter a URL:", "");
			try {
				//ignore error for blank urls
				oRTE.document.execCommand("Unlink", false, null);
				oRTE.document.execCommand("CreateLink", false, szURL);
			} catch (e) {
				//do nothing
			}
		} else {
			oRTE.focus();
			oRTE.document.execCommand(command, false, option);
			oRTE.focus();
		}
	} catch (e) {
		alert(e);
	}
}

//Function to set color
function setColor(color) {
	var rte = currentRTE;
	var oRTE;
	if (document.all) {
		oRTE = frames[rte];
	} else {
		oRTE = document.getElementById(rte).contentWindow;
	}

	var parentCommand = parent.command;
	if (document.all) {
		//retrieve selected range
		var sel = oRTE.document.selection; 
		if (parentCommand == "hilitecolor") parentCommand = "backcolor";
		if (sel != null) {
			var newRng = sel.createRange();
			newRng = rng;
			newRng.select();
		}
	} else {
		//oRTE.focus();
	}
	oRTE.document.execCommand(parentCommand, false, color);
	//oRTE.focus();
	document.getElementById('cp' + rte).style.visibility = "hidden";
	document.getElementById('cp' + rte).style.display = "none";
}

//Function to add image
function AddImage(rte) {
	var oRTE;
	if (document.all) {
		oRTE = frames[rte];

		//get current selected range
		var selection = oRTE.document.selection; 
		if (selection != null) {
			rng = selection.createRange();
		}
	} else {
		oRTE = document.getElementById(rte).contentWindow;

		//get currently selected range
		var selection = oRTE.getSelection();
		rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
	}

	imagePath = prompt('Enter Image URL:', 'http://');
	if ((imagePath != null) && (imagePath != "")) {
		//oRTE.focus();
		oRTE.document.execCommand('InsertImage', false, imagePath);
	}
	//oRTE.focus();
}

//function to perform spell check
function checkspell() {
	try {
		var tmpis = new ActiveXObject("ieSpell.ieSpellExtension");
		tmpis.CheckAllLinkedDocuments(document);
	}
	catch(exception) {
		if(exception.number==-2146827859) {
			if (confirm("ieSpell not detected.  Click Ok to go to download page."))
				window.open("http://www.iespell.com/download.php","DownLoad");
		} else {
			alert("Error Loading ieSpell: Exception " + exception.number);
		}
	}
}

function getOffsetTop(elm) {
	var mOffsetTop = elm.offsetTop;
	var mOffsetParent = elm.offsetParent;

	while(mOffsetParent){
		mOffsetTop += mOffsetParent.offsetTop;
		mOffsetParent = mOffsetParent.offsetParent;
	}

	return mOffsetTop;
}

function getOffsetLeft(elm) {
	var mOffsetLeft = elm.offsetLeft;
	var mOffsetParent = elm.offsetParent;

	while(mOffsetParent) {
		mOffsetLeft += mOffsetParent.offsetLeft;
		mOffsetParent = mOffsetParent.offsetParent;
	}

	return mOffsetLeft;
}

function Select(rte, selectname) {
	var oRTE;
	if (document.all) {
		oRTE = frames[rte];

		//get current selected range
		var selection = oRTE.document.selection; 
		if (selection != null) {
			rng = selection.createRange();
		}
	} else {
		oRTE = document.getElementById(rte).contentWindow;

		//get currently selected range
		var selection = oRTE.getSelection();
		rng = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
	}
	
	var idx = document.getElementById(selectname).selectedIndex;
	// First one is always a label
	if (idx != 0) {
		var selected = document.getElementById(selectname).options[idx].value;
		var cmd = selectname.replace('_' + rte, '');
		oRTE.document.execCommand(cmd, false, selected);
		document.getElementById(selectname).selectedIndex = 0;
	}
	//oRTE.focus();
}

function kb_handler(evt) {
	var rte = evt.target.id;

	//contributed by Anti Veeranna (thanks Anti!)
	if (evt.ctrlKey) {
		var key = String.fromCharCode(evt.charCode).toLowerCase();
		var cmd = '';
		switch (key) {
			case 'b': cmd = "bold"; break;
			case 'i': cmd = "italic"; break;
			case 'u': cmd = "underline"; break;
		};

		if (cmd) {
			FormatText(rte, cmd, true);
			//evt.target.ownerDocument.execCommand(cmd, false, true);
			// stop the event bubble
			evt.preventDefault();
			evt.stopPropagation();
		}
 	}
}

/**
* Strip most ugly word mesh.
* Base implementation got from MCE editor
*
* @author Pavel Alexeev aka Pahan-Hubbitus
* @copyright 2009
* @license GPLv2+
**/
function msWordNastyClean(h){
//alert('msWordNastyClean()');
	each = function(o, cb, s){
	var n, l;

		if (!o){return 0;}

		s = s || o;

		if (typeof(o.length) != 'undefined') {
			// Indexed arrays, needed for Safari
			for (n=0, l = o.length; n<l; n++) {
				if (cb.call(s, o[n], n, o) === false){return 0;}
			}
		} else {
			// Hashtables
			for (n in o) {
				if (o.hasOwnProperty(n)) {
					if (cb.call(s, o[n], n, o) === false){return 0;}
				}
			}
		}

		return 1;
	}//f each

	function process(items){
		each(items, function(v) {
			// Remove or replace
			if (v.constructor == RegExp){h = h.replace(v, '');}
			else{h = h.replace(v[0], v[1]);}
		});
	}

	// Process away some basic content
	process([
		/^\s*(&nbsp;)+/g,			// nbsp entities at the start of contents
		/(&nbsp;|<br[^>]*>)+\s*$/g,	// nbsp entities at the end of contents

		[/<!--\[if !supportLists\]-->/gi, '$&__MCE_ITEM__'],		// Convert supportLists to a list item marker
		[/(<span[^>]+:\s*symbol[^>]+>)/gi, '$1__MCE_ITEM__'],		// Convert symbol spans to list items
		[/(<span[^>]+mso-list:[^>]+>)/gi, '$1__MCE_ITEM__'],		// Convert mso-list to item marker

		/<!--[\s\S]+?-->/gi,								// Word comments
		/<\/?(img|font|meta|link|style|div|v:\w+)[^>]*>/gi,		// Remove some tags including VML content
		/<\\?\?xml[^>]*>/gi,								// XML namespace declarations
		/<\/?o:[^>]*>/gi,									// MS namespaced elements <o:tag>
		/ (id|name|language|type|on\w+|v:\w+)=\"([^\"]*)\"/gi,		// on.., class, style and language attributes with quotes
		/ (id|name|language|type|on\w+|v:\w+)=(\w+)/gi,			// on.., class, style and language attributes without quotes (IE)
		[/<(\/?)s>/gi, '<$1strike>'],							// Convert <s> into <strike> for line-though
		/<script[^>]+>[\s\S]*?<\/script>/gi,					// All scripts elements for msoShowComment for example
		[/&nbsp;/g, '\u00a0'],								// Replace nsbp entites to char since it's easier to handle
	// Strip class-attributes
		/ class=\"([^\"]*)\"/gi,								// class attributes with quotes
		/ class=(\w+)/gi,									// class attributes without quotes (IE)

		/<\S+:\S+( .+?>|>)/gi,								// All tags with namespaces, like: <st1:metricconverter productid="98 г" w:st="on">
		/ style=""/gi,										// Empty styles:
		/<([^>\/]+)[^>]*?>\s*?<\/$1>/gi						// Any empty tags like: <span> </span>
	]);

//	process([
//		/<\/?(span)[^>]*>/gi
//	]);

//	// Remove named anchors or TOC links
//	each(dom.select('a', o.node), function(a) {
//		if (!a.href || a.href.indexOf('#_Toc') != -1)
//			dom.remove(a, 1);
//	});

return h;
}

/**
* onAfterPaste ewent would be more desirablem but it is not still awailable in FireFox.
* So, in implementation we use hack ( http://www.thefutureoftheweb.com/blog/onafterpaste ) - dealyng all changes after 1 millisecond, after content arrived.
**/
function onPasteCleanWordMesh(evnt){
//alert('onPasteCleanWordMesh()');
	if (evnt) { var event = evnt; } // In IE window.event

setTimeout(
	function(){
//	alert('real processing:' + event);
	event.target.ownerDocument.body.innerHTML = msWordNastyClean(event.target.ownerDocument.body.innerHTML);
	}
	,1000 // 10 not anought, so 1000 for enshurance
);

event.returnValue=true;
return true;
}