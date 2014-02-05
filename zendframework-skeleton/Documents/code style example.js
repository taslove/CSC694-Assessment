var validExtensions = [".pdf", ".jpeg"];


/*  Nice description of this function
*  
*/

function validate(form) {
    // store all html input elements in an array
    var inputsArray = form.getElementsByTagName("input");
    
    // cycle though all html input elements
    for (var i = 0; i < inputsArray.length; i++) {
        var input = inputsArray[i];
		
		// if the input element is for files, get its name
		if (input.type == "file") {
			var fileName = input.value;
			console.log("File name is " + fileName);
			if (fileName.length > 0) {
				var isValid = false;
			}
			else {
				console.log("Didn't select a file");
				return false;
			}
			
			//Nice description of this block of code
			for (var j = 0; j < validExtensions.length; j++) {
				var fileExtension = validExtensions[j];
				if (fileName.substr(fileName.length - fileExtension.length,
						fileExtension.length).toLowerCase() == fileExtension.toLowerCase()) {
					isValid = true;
					break;
				}
				
			}
			if (isValid == false) {
			// change the instructions element
                document.getElementById("instructions").innerHTML="That isn't a PDF.";
				return false;
            }
			else {
				alert("Awesome! " + fileName + " is a PDF.");
			}
		}		
	}
}
