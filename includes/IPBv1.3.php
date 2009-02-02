<?php


$wrapheader='';




$jsfooter = '<!--
// YouTube in Posts By Beta
if(location.href.match(/(showtopic=\d+|Msg(&CODE=(0)?8)?)/i)) {
var TD = document.getElementsByTagName("TD");
for(var a = 0; a < TD.length; a ++) {
 if(TD[a].className.match(new RegExp("post(1|2)")) && TD[a].innerHTML.match(new RegExp("\\\\[%1$s=.*?\\\\]", "gi"))) {
 
var newform=document.createElement("form");
 
 newform.setAttribute("name","ATPloginform"+a);
 newform.setAttribute("action","%2$s?view=atp");
 newform.setAttribute("method","post");
 newform.setAttribute("target","IF_"+a);
 
 newform.innerHTML = 
 TD[a].innerHTML.replace(new RegExp("\\\\[%1$s=(.*?)\\\\]", "gi"), \'<input type="hidden" name="institute" value="%1$s"><input type="hidden" name="password" value="$1"><input type="hidden" name="action" value="login"><input type="submit" value="Refresh" > </form>\');
 
 TD[a].innerHTML = \'<center><iframe name="IF_\'+a+\'" style="display:block;height:%3$dpx;width:%4$dpx;"></iframe>\';
 
 TD[a].appendChild(newform);
 newform.submit();

}
}
}
//-->';

$wrapfooter = sprintf($jsfooter,$bankname,$siteURL,$inputPanelHeight,$inputPanelWidth);



$bbcode = '';


?>