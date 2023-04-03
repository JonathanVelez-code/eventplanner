const urlBase = 'http://eventsplanneruni.com/';
const extension = 'php';

// Init the userId and first and last name to prevent issues
let userId = 0;
let firstName = "";
let lastName = "";
let contactId = 0;


// Login in function that performs the login verification.
function doLogin()
{
    //window.location.href = "index.html";
	// init to 0 to prevent bugs.
	userId = 0;
	firstName = "";
	lastName = "";

	// Grabbing the text from those fileds inputted by the user (from HTML)
	let userID = document.getElementById("user").value;
	let password = document.getElementById("pass").value;


	// Set the login result to blank to reset any previous messages.
	document.getElementById("loginResult").innerHTML = "";

	// the data set that gets sent to the API (php file)
	let tmp = {username:username,password:password};

	// 	Converting to JSON
	let jsonPayload = JSON.stringify( tmp );
	// This just assembles teh URL to allow this JS file to be used with differnet
	// API endpoints.
	let url = urlBase + 'LAMPAPI/login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
        //window.location.href = "index.html";
		xhr.onreadystatechange = function()
		{
            
			if (this.readyState == 4 && this.status == 200)
			{
                
				let jsonObject = JSON.parse( xhr.responseText );
				// userId is the key that corresponds to each of our registered users
				userId = jsonObject.id;

				// If the API returns < 1, the combo either doesnt exist or is wrong!
				if( userId < 1 )
				{
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}



				// breaks down the JSON we recieved and sets them to our variables.
				username = jsonObject.username;
				password = jsonObject.password;

				saveCookie();
				// take us to the landing page! We are Logged in!
				window.location.href = "index.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}
