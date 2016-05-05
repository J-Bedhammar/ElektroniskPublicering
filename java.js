function clock () {					//Klockfunktion
	var today = new Date ();
	var d = today.getDate ();
	var y = today.getFullYear(); 
	var month = today.getMonth();
	var h = today.getHours();
	var m = today.getMinutes();
	var s = today.getSeconds();
	m = checkTime(m);
	s = checkTime(s);
	month = checkMonth(month);

	document.getElementById('txt').innerHTML = month + "/" + d + "/"  + y + "  " + h + ":" + m + ":" + s;
	var t = setTimeout(clock, 500);
}

function checkTime(i) {					
	
	if (i < 10) {
	i = "0" + i //add zero in front of number, if <10
	};
	
	return i;
}

function checkMonth(n){			//Hämtar månader 0-11, lägger på 1 för att få 1-12 
	n += 1;
	if (n<10) {
	n = "0" + n;
	};
	
	return n;
}

function myFunction() {					//Hjälpknappen, alert, informerande text
    alert("HOW TO USE THE DATABASE\n\
	\nSEARCH:\n\
	\n1. Search Set: Enter a sets id-number or name(part or whole). The search result will consist of sets matching your search.\n \
	\n2. Year: Choose a time interval. The search results will be sets that were released in the given time frame.\n \
	\n3. Categories: Search for categories. The search results will be sets in the given category.\n \
	\n4. Press GO to search.\n\
	\nRESULT: \n \
	\nThe result of your search will be a list which contains the id, name and year of release of the set.\
    \nClick on the name and you will see the parts that belongs to the set.\
	\n \
	\nPS. \n\
	Click the lego logo to go back to the homepage." );
}