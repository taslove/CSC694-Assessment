function planAlert()
{
	alert("I am not working yet!");
}

function planValidation()
{
	alert("Validation, I am not working yet!");
}


function printPage()
{
	var numDivs = 20;
	for(i=1;i<numDivs;i++)
	{
		if(document.all['divNoPrint'+i])
		{
			document.all['divNoPrint'+i].style.display = 'none';
		}
	}
	
	window.print();
	for(i=1;i<numDivs;i++)
	{
		if(document.all['divNoPrint'+i])
		{
			document.all['divNoPrint'+i].style.display = '';
		}
	}
}