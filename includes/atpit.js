

<script LANGUAGE="JavaScript">




if(location.href.match(/(showtopic=\d+|Msg(&CODE=(0)?8)?)/i))
	{
	var TD = document.getElementsByTagName("TD");
	for(var a = 0; a < TD.length; a ++)
		{
 		if(TD[a].className.match(new RegExp("post(1|2)")) && TD[a].innerHTML.match(new RegExp("\\[FBCC=.*?\\]", "gi")))
 			{
 
			var newform=document.createElement("form");
 
 			newform.setAttribute("name","ATPloginform"+a);
 			newform.setAttribute("action","https://www.metaversus.org/bank/fbcc/?view=atp");
 			newform.setAttribute("method","post");
 			newform.setAttribute("target","IF_"+a);
 
 			newform.innerHTML = 
 			TD[a].innerHTML.replace(new RegExp("\\[FBCC=(.*?)\\]", "gi"), '<center><input type="hidden" name="institute" value="FBCC"><input type="hidden" name="password" value="$1"><input type="hidden" name="action" value="login"><input type="submit" value="Refresh" > </form><br /></center>');
 
 			TD[a].innerHTML = '<center><iframe name="IF_'+a+'" style="display:block;height:600px;width:728px;"></iframe>';
 
			TD[a].appendChild(newform);
 			newform.submit();
			}
		}
	}


</script>
