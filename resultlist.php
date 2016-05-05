<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link href="style.css" media="screen" rel="stylesheet" type="text/css"/>
	<title>Databas för LEGO-satser</title>
	<script src="java.js"></script>
</head>
<body onload="clock()">
	<div id="meny">
		<a href="intedex.html">
			<span id="home">
			<img id="logo" src="logo2.jpg" alt="logo">
			</span>
		</a>
		<div id="menyinfo">
			<h1>DATABASE - SETS</h1>		<!--Fonten kräver CAPS, litet t = gubbe-->
		</div>
	</div>
	<div id="container">
		<div id="containerLeft">
			<form action="resultlist.php" method="GET">
				<div id="search" class="search">
					<p class="searchText">SEARCH SET</p>
					<br>
					<input class="searchBox" type="text" name="text" value="<?php echo $_GET['text']; ?>">
					<br>
				</div>
				<div id="years" class="search">
					<p class="searchText">YEAR</p>
					<br>
					<input class="numberBox" type="number" name="firstYear" min="1930" max="2016" step="1" value="<?php echo $_GET['firstYear']; ?>">
					<input class="numberBox" type="number" name="secondYear" min="1930" max="2016" step="1" value="<?php echo $_GET['secondYear']; ?>">
					<br>			
				</div>
				<div id="categories" class="search">		
					<p class="searchText">CATEGORIES</p>
					<br>
					<input class="searchBox" type="text" name="categories" value="<?php echo $_GET['categories']; ?>"> 
					<br>
				</div>
				<div id="go" class="search">
					<input id="postButton" type="submit" value="GO">
				</div>
			</form>
			<button id="help" onclick="myFunction()">HELP</button>
		</div>
		<div id="containerRight">

	<!--Searchfunction-->
			<?php
				// Koppla upp mot databasen
				mysql_connect("mysql.itn.liu.se", "lego", "");
				mysql_select_db("lego");
				
				$searchtext			=mysql_real_escape_string(isset($_GET['text']) ? $_GET['text'] : ' ');
				$searchfirstyear	=mysql_real_escape_string(isset($_GET['firstYear']) ? $_GET['firstYear'] : ' ');
				$searchsecondyear	=mysql_real_escape_string(isset($_GET['secondYear']) ? $_GET['secondYear'] : ' ');
				$searchcategories	=mysql_real_escape_string(isset($_GET['categories']) ? $_GET['categories'] : ' ');
				
				$searchresult = mysql_query("SELECT sets.Setname, sets.SetID, sets.Year
				FROM sets, categories
				WHERE sets.CatID=categories.CatID
				AND sets.Year >='$searchfirstyear'		
				AND sets.Year <='$searchsecondyear'
				AND categories.Categoryname LIKE '%{$searchcategories}%' 
				AND (sets.SetID LIKE '%{$searchtext}%' OR sets.Setname LIKE '%{$searchtext}%')
				ORDER BY sets.Setname") or die ("Error in database table:" .mysql_error());			
				
				//antal resultat
				$nrOfResult = mysql_num_rows($searchresult);
				
				//Återtal ska stämma, year 1<2
				if($searchfirstyear <= $searchsecondyear)
				{
					//Search 
					if ( $searchtext != NULL && $searchcategories != NULL)  // söker i "search" och "categories" 
					{
						print ("<h1 class=setName>Search results for: '<em>".$searchtext." / ".$searchcategories." (".$searchfirstyear."-".$searchsecondyear.") </em>'</h1><h6 class=setName>Number of matches: ".$nrOfResult." sets</h6>");
					}
					else if  ( $searchtext != NULL && $searchcategories == NULL)  // söker bara i "search"
					{
						print ("<h1 class=setName>Search results for: '<em>".$searchtext." (".$searchfirstyear."-".$searchsecondyear.") </em>'</h1><h6 class=setName>Number of matches: ".$nrOfResult." sets</h6>");
					}	
					else if ( $searchtext == NULL && $searchcategories != NULL)  // söker bara i "categories"
					{
						print ("<h1 class=setName>Search results for: '<em>".$searchcategories." (".$searchfirstyear."-".$searchsecondyear.") </em>'</h1><h6 class=setName>Number of matches: ".$nrOfResult." sets</h6>");
					}	
					else  // skriver inte i sökrutorna 
					{
						print ("<h1 class=setName>Search results for: '<em>(".$searchfirstyear."-".$searchsecondyear.") </em>'</h1><h6 class=setName>Number of matches: ".$nrOfResult." sets</h6>");
					}
				}
				else
				{
					print("<h1 class=setName>Invalid input : years</h1>");
				}
				
				//Tabellmeny
				print("
				<table>
					<tr>
						<th class='th1'>SetID</th>
						<th class='th2'>Setname</th>
						<th>Year</th>
					</tr>
					");
				
				$counter = 0;
				do
				{
					$row = mysql_fetch_array($searchresult);
					
					if(count($row['SetID']) != 0) // Om det finns SetID (det vill säga sets) så visa dessa
					{
						$tempsetid = $row['SetID'];
						$showsetname = $row['Setname'];
						$showsetyear = $row['Year'];
						//$linkinfo=\'setinfo.php?SetID=' .$tempsetid. '&text=' .$searchtext. '&firstYear=' .$searchfirstyear. '&secondYear=' .$searchsecondyear. '&categories='. $searchcategories.\;
						print ("		
							<tr>
								<td class='tableID'>".$tempsetid."</td>
								<td class='tableName'><a href=\"setinfo.php?SetID=$tempsetid&text=$searchtext&firstYear=$searchfirstyear&secondYear=$searchsecondyear&categories=$searchcategories\">$showsetname</a></td>
								<td>".$showsetyear."</td>
							</tr>
						");
						
						$counter++;
					}
					else if ($counter != 0) // Finns det inga, men om loopen har körts innan (dvs kommit till slutet av loopen)
					{
						break;
					}
					else //Annars om det inte fanns några till att börja med
					{
						print("
						<tr>
						<td></td>
						<td><h6>The search yielded no results</h6></td>
						<td></td>
						</tr>");
						break;
					}
				} while(true); //Kör loopen sålänge det finns "Setname":s att tillgå
				
				print("</table>");
				
				
			?> 
		</div>
	</div>
	<div id="footer">
		<div id="creator"><p>© Elon Olsson, Jennifer Bedhammar - March, 2016</p></div>
		<div id="txt"></div> <!--Datum + klocka, javascript-->
	</div>
</body>
</html>