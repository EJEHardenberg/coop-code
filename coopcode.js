/* Let's chat to the backend! 
 * There's two access points,
 * /api/messages.php
 * /api/questions.php
*/


function CC(options){
	if( options == null || typeof options != "object" )
		throw new Error('Initialization Failure of library. Please include an object to your call')

	if( options.host == null )
		this.host = "//www.coopcode.com/api"
	else
		this.host = options.host


	this.get = function( endpoint, callback ){
		/* Endpoints should be /something */
		var xmlHttp = null
		var ref = this;
    	xmlHttp = new XMLHttpRequest()
    	xmlHttp.onreadystatechange = function(){
    		if( xmlHttp.readyState == 4 )
    			callback(xmlHttp)
    	}
    	//Change false to true for a cool visual
    	xmlHttp.open( "GET", this.host + endpoint , true )
    	xmlHttp.send( null )
	}

	this.post = function( endpoint , data , callback ){
		var xmlHttp = null;
		var ref = this
		xmlHttp = new XMLHttpRequest()
		xmlHttp.onreadystatechange = function(){
    		if( xmlHttp.readyState == 4 )
    			callback(xmlHttp)
    	}
    	//Change false to true for a cool visual
    	xmlHttp.open( "POST", this.host + endpoint , true )
    	xmlHttp.send( JSON.stringify( data ) )
	}

	/* Give a callback that will recieve the questions object
	*/
	this.getQuestions = function(callback, doCall, xmlHttp){
		if(doCall == null || doCall == false)
			this.get("/questions.php",function(_xmlHttp){ this.getQuestions(callback, true, _xmlHttp) })
		if(doCall){
			/* Retrieved results */
			if(xmlHttp.status == 200){
				try{
					var questions = JSON.parse(xmlHttp.response);
					callback(questions)
				}catch(err){
					console.log(err)
				}
			}else{
				console.log(xmlHttp)
			}
		}
	}

	this.sendMessage = function(callback, to, from, msg, doCall, xmlHttp){
		if(doCall == null || doCall == false)
			this.post("/messages.php", {to: to, from: from, msg: msg}, function(_xmlHttp){ this.sendMessage(callback, null, null, null, true, _xmlHttp) })
		if(doCall){
			if(xmlHttp.status == 200){
				try{
					var response = JSON.parse(xmlHttp.response)
					callback(response)
				}catch(err){
					console.log(err)
				}
			}else{
				console.log(xmlHttp)
			}
		}
	}

	this.getMessages = function(callback, to, doCall, xmlHttp){
		if(doCall == null || doCall == false)
			this.get("/messages.php?to=" + to, function(_xmlHttp){this.getMessages(callback, to, true, _xmlHttp)})
		if(doCall){
			if(xmlHttp.status == 200){
				try{
					var response = JSON.parse(xmlHttp.response)
					callback(response)
				}catch(err){
					console.log(err)
				}
			}else{
				console.log(xmlHttp)
			}
		}
	}

	return this;	
}

var cc =  CC({})
cc.getQuestions(function(q){console.log(q)})
cc.sendMessage(function(q){console.log(q)}, "ALL","Ethan",JSON.stringify({}))
cc.getMessages(function(q){console.log(q)}, "Ethan")