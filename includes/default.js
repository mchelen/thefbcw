

function switchclass(id,classname1,classname2)
{
    obj=document.getElementById(id);
    
    if (obj.className==classname1)
    {
        obj.className=classname2;
    }
    else { obj.className=classname1;}
}


function doCheckBox(inputid)
{

	var myinput = document.getElementById(inputid);

	myinput.checked = true;

}


function removeChildrenFromNode(node)
{
while(node.firstChild){node.removeChild(node.firstChild);}
}


function loadBatch(inputtextID,outputdivID)
	{
	
	
	
	var inputText = document.getElementById(inputtextID).value;
	var outputdiv = document.getElementById(outputdivID);

	
	
	var inputTransfers = inputText.split("\n");
	
	var allTransfers = new Array();
		
		
		
	removeChildrenFromNode(outputdiv);
		
		
	for (i=0;i<inputTransfers.length;i++)
		{
		
		var thisTransfer=inputTransfers[i].split("\t");
		allTransfers[i] = new Array();
		allTransfers[i][0] = thisTransfer[0];
		allTransfers[i][1] = thisTransfer[1];
		allTransfers[i][2] = thisTransfer[2];
		if (allTransfers[i][0])
			{
			addTransferForm(outputdiv, allTransfers[i][0], allTransfers[i][1], allTransfers[i][2], i);
			}
		}
	
		



	}


function addTransferForm(divOutput,accountName,amountBalance,memoText,i)
	{
	
	

	var newRecipientEntry=document.createElement("input");
	var newAmountEntry=document.createElement("input");
	var newMemoEntry=document.createElement("textarea");
	var spacer=document.createElement("br");
	
	var newRecipientSpan=document.createElement("span");
	var newAmountSpan=document.createElement("span");
	var newMemoSpan=document.createElement("span");
	
	
	newRecipientEntry.setAttribute("type","text");
	newRecipientEntry.setAttribute("id","recname"+i);
	newRecipientEntry.setAttribute("name","inputRecipientName"+i);
	newRecipientEntry.setAttribute("value",accountName);
	newRecipientEntry.setAttribute("class","recipientEntryForm");

	
	
	newAmountEntry.setAttribute("type","text");
	newAmountEntry.setAttribute("id","transamt"+i);
	newAmountEntry.setAttribute("name","inputAmount"+i);
	newAmountEntry.setAttribute("value",amountBalance);
	newAmountEntry.setAttribute("class","amountEntryForm");
	
	
	newMemoEntry.setAttribute("id","transmemo"+i);
	newMemoEntry.setAttribute("name","inputMemo"+i);
	newMemoEntry.innerHTML= memoText;	
	newMemoEntry.setAttribute("maxlength",255);
	newMemoEntry.setAttribute("cols",40);
	newMemoEntry.setAttribute("rows",6);
	newMemoEntry.setAttribute("wrap","virtual");
	newMemoEntry.setAttribute("class","memoEntryForm");
	

	newRecipientSpan.setAttribute("class","importantinput");
	newRecipientSpan.setAttribute("name","Receiving Account #"+(i+1)+":");
	newAmountSpan.setAttribute("class","importantinput");
	newAmountSpan.setAttribute("name","Transfer Amount #"+(i+1)+":");
	newMemoSpan.setAttribute("class","importantinput");
	newMemoSpan.setAttribute("name","Memo #"+(i+1)+":");



	newRecipientSpan.appendChild(newRecipientEntry);
	newAmountSpan.appendChild(newAmountEntry);
	newMemoSpan.appendChild(newMemoEntry);
	


	if(!document.getElementById("recname"+i))
	{
	
		divOutput.appendChild(newRecipientSpan);
		divOutput.appendChild(newAmountSpan);
		divOutput.appendChild(newMemoSpan);
		divOutput.appendChild(spacer);
	}
	


	}





