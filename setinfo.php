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
			<h1>DATABASE - SETS</h1> <!-- Fonten kräver CAPS, litet t= gubbe-->
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
				$searchresult = mysql_query("SELECT sets.Setname FROM sets, categories
				WHERE sets.CatID=categories.CatID
				AND sets.Year >='$searchfirstyear'		
				AND sets.Year <='$searchsecondyear'
				AND categories.Categoryname LIKE '%{$searchcategories}%' 
				AND (sets.SetID LIKE '%{$searchtext}%' OR sets.Setname LIKE '%{$searchtext}%')
				ORDER BY sets.Setname") or die ("Error in database table:" .mysql_error());
				
				//Hämta SetID från länken och sparar i variabel
				$thissetsid = $_GET["SetID"];
				
				//Hämta Setnames
				$searchSetname = mysql_query ("SELECT sets.Setname
				FROM sets			
				WHERE sets.SetID = '$thissetsid'");
				//Slå ihop dessa? + quantity?
				//Hämtar categoryname
				$Catname = mysql_query ("SELECT categories.Categoryname
				FROM sets, categories
				WHERE sets.CatID = categories.CatID 
				AND sets.SetID = '$thissetsid'");			
				
				//Hämtar minifigs, bildinfo, quantity
				$storeminifigures = mysql_query("SELECT minifigs.MinifigID, minifigs.Minifigname, inventory.Quantity, inventory.colorID,
				images.itemTypeID, images.itemID
				FROM inventory, images, minifigs
				WHERE inventory.SetID = '$thissetsid'	
				AND inventory.ItemID = images.itemID
				AND inventory.ItemID = minifigs.MinifigID
				AND images.colorID = inventory.ColorID");
				
				//Hämtar partname, bildinfo, quantity
				$searchParts = mysql_query ("SELECT DISTINCT parts.Partname, inventory.Quantity, inventory.colorID,
				images.itemTypeID, images.itemID		
				FROM inventory, images, parts
				WHERE inventory.SetID = '$thissetsid'
				AND inventory.ColorID = images.ColorID
				AND inventory.ItemID = parts.PartID
				AND inventory.ItemID = images.itemID 
				ORDER BY parts.Partname");
				
				//antal resultat
				$nrOfMinifigs = mysql_num_rows($storeminifigures);
				$nrOfItems = mysql_num_rows($searchParts);
				$nrTotal = $nrOfMinifigs + $nrOfItems;
				
				//sql-fråga för att få rätt info till bilden för settet 															<--- Hann inte lägga till pga bilddatabasen ligger nere, kunde inte se resultaten, om de stämmer eller dyker upp
				// $getsetimage = mysql_query("SELECT images.itemTypeID, images.itemID
				// FROM inventory, images
				// WHERE inventory.SetID ='$thissetsid'
				// AND inventory.ItemID = images.itemID
				// AND images.itemTypeID = 'S'	");
				
				//Display setname and category
				$namerow = mysql_fetch_row($searchSetname);
				$Setname = $namerow[0];
				
				$catrow = mysql_fetch_row($Catname);
				$thisCatname = $catrow[0];
				print ("<h1 class='setName'>$Setname\n</h1><h6 class='catName'> This sets category is: ".$thisCatname."</h6><h6 class='catName'> Number of different parts: ".$nrTotal."</h6>");
				
				// om setbilden ska med, så ska den ligga här.															<----Samma som ovan, fast display ^
				// while($thissetrow = mysql_fetch_array($getsetimage))
				// {				
					// $itemtypeID = $thissetrow['itemTypeID'];
					// $itemID =$thissetrow['itemID'];
					// $imagelink = 'http://www.itn.liu.se/~stegu/img.bricklink.com' ."/" .$itemtypeID ."/" .$itemID ;
					// print("<img src=".$imagelink.">");
					
				// }
				
				//Tabellmeny
				print("
				<h3>Minifigures</h3>
				<table>
					<tr>
						<th id='tableImage'>Image</th>
						<th class='tableID'>PartID</th>
						<th class='tableName'>Partname</th>
						<th>Quantity</th>
					</tr>
				");
				
				$counter = 0;
				
				//display minifigures
				do
				{
					$row_minifig = mysql_fetch_array($storeminifigures);
					
					if(count($row_minifig['Minifigname']) != 0)			// Om det finns minifigs så visa dessa
					{
						$showminifigname = $row_minifig ['Minifigname'];
						$quantity		 = $row_minifig['Quantity'];
						
						$itemtypeID		=$row_minifig['itemTypeID'];
						$itemID			=$row_minifig['itemID'];
						$imagelink='http://www.itn.liu.se/~stegu/img.bricklink.com' ."/" .$itemtypeID ."/" .$itemID ;				// Länk till bilder
						
						print("
						<tr>
							<td class='tableImage'><img src=".$imagelink." alt='No Image' class=imagelink/></td>
							<td class='tableID'>".$itemID."</td>
							<td class='tableName'>".$showminifigname."</td>
							<td>".$quantity."</td>
						</tr>
						");

						$counter += $quantity;
					}
					else if ($counter != 0)			// Finns det inga, men om loopen har körts innan (dvs kommit till slutet av loopen)
					{
						break;
					}
					else 					//Annars om det inte fanns några till att börja med
					{
						print("
						<tr>
						<td class='tableImage'></td>
						<td class='tableID'></td>
						<td class='tableName'>This set contains no Minifigures</td>
						<td></td>
						</tr>");
						break;
					}
					
				} while(true); 		//Kör loopen sålänge det finns Minifigs att tillgå
				
				print("</table>");
				
				
			
				//meny för legobitarna
				print("
				<h3>Parts</h3>
				<table>
					<tr>
						<th class='tableImage'>Image</th>
						<th class='tableID'>PartID</th>
						<th class='tableName'>Partname</th>
						<th>Quantity</th>
					</tr>
				");
				
				$counter2 = 0;				
				//display partname och partinfo
				do
				{ 
					$row_parts = mysql_fetch_array($searchParts);
					
					if(count($row_parts['Partname']) != 0)			// Om det finns parts så visa dessa
					{
						$showpartname = $row_parts['Partname'];
						$quantity	  = $row_parts['Quantity'];
						
						$itemtypeID		=$row_parts['itemTypeID'];
						$colorID		=$row_parts['colorID'];
						$itemID			=$row_parts['itemID'];
						
						
						$imagelink		='http://www.itn.liu.se/~stegu/img.bricklink.com' ."/" .$itemtypeID ."/" .$colorID ."/" .$itemID ;		// Länk till bilder
						
						print("
						<tr>
							<td class='tableImage'><img src=".$imagelink." alt='No image' class=imagelink/></td>
							<td class='tableID'>".$itemID."</td>
							<td class='tableName'>".$showpartname."</td>
							<td>".$quantity."</td>
						</tr>
						");
						
						$counter2 +=$quantity;
					}
					else if ($counter2 != 0)			// Finns det inga, men om loopen har körts innan (dvs kommit till slutet av loopen)
					{
						break;
					}
					else			//Annars om det inte fanns några till att börja med
					{
						print("
						<tr>
						<td class='tableImage'></td>
						<td class='tableID'></td>
						<td class='tableName'>This set contains no parts</td>
						<td></td>
						</tr>");
						break;
					}
				} while(true);				//Kör loopen sålänge det finns Minifigs att tillgå
				print("</table>");		//Avsluta tabell
				
				$totalCount = $counter + $counter2;				

				print("
				<div>
				<h6 class='catName'> Total quantity: " .$totalCount." </h6>
				</div>");
				
			?> 
		</div>
	</div>
	<div id="footer">
		<div id="creator"><p>© Elon Olsson, Jennifer Bedhammar - March, 2016</p></div>
		<div id="txt" class="TIME"></div>
	</div>
	
	</body>
</html>