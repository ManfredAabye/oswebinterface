<?php

// Globale Variablen verwenden
global $osVorname, $osNachname, $osEMail, $osHash, $osSalt, $benutzeruuid, $inventoryuuid, $neuparentFolderID, $neuHauptFolderID, $osDatum, $pdo;

//$pdo = new PDO("mysql:host=$CONF_db_server;dbname=$CONF_db_database", $CONF_db_user, $CONF_db_pass);
$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);


// Avatar eintragen
$neuer_user = array();
$neuer_user['PrincipalID'] = $benutzeruuid;
$neuer_user['ScopeID'] = '00000000-0000-0000-0000-000000000000';
$neuer_user['FirstName'] = $osVorname;
$neuer_user['LastName'] = $osNachname;
$neuer_user['Email'] = $osEMail;
$neuer_user['ServiceURLs'] = 'HomeURI= InventoryServerURI= AssetServerURI=';
$neuer_user['Created'] = $osDatum;
$neuer_user['UserLevel'] = '0';
$neuer_user['UserFlags'] = '0';
$neuer_user['UserTitle'] = '';
$neuer_user['active'] = '1';

 
// $statement = $pdo->prepare("INSERT INTO UserAccounts (email, vorname, nachname) VALUES (:email, :vorname, :nachname)");
$statement = $pdo->prepare("INSERT INTO UserAccounts (PrincipalID, ScopeID, FirstName, LastName, Email, ServiceURLs, Created, UserLevel, UserFlags, UserTitle, active) VALUES (:PrincipalID, :ScopeID, :FirstName, :LastName, :Email, :ServiceURLs, :Created, :UserLevel, :UserFlags, :UserTitle, :active)");
$statement->execute($neuer_user);  
 

// UUID, passwordHash, passwordSalt, webLoginKey, accountType
$neues_passwd = array();
$neues_passwd['UUID']         = $benutzeruuid;
$neues_passwd['passwordHash'] = $osHash;
$neues_passwd['passwordSalt'] = $osSalt;
$neues_passwd['webLoginKey'] = '00000000-0000-0000-0000-000000000000';
$neues_passwd['accountType'] = 'UserAccount';

 
$statement = $pdo->prepare("INSERT INTO auth (UUID, passwordHash, passwordSalt, webLoginKey, accountType) VALUES (:UUID, :passwordHash, :passwordSalt, :webLoginKey, :accountType)");
$statement->execute($neues_passwd);

// Das nachfolgende eintragen in der GridUser Spalte
$neuer_GridUser = array();
$neuer_GridUser['UserID']         = $benutzeruuid;
$neuer_GridUser['HomeRegionID'] = '00000000-0000-0000-0000-000000000000';
$neuer_GridUser['HomePosition'] = '<0,0,0>';
$neuer_GridUser['LastRegionID'] = '00000000-0000-0000-0000-000000000000';
$neuer_GridUser['LastPosition'] = '<0,0,0>';

 
$statement = $pdo->prepare("INSERT INTO GridUser (UserID, HomeRegionID, HomePosition, LastRegionID, LastPosition) VALUES (:UserID, :HomeRegionID, :HomePosition, :LastRegionID, :LastPosition)");
$statement->execute($neuer_GridUser);

// Inventarverzeichnisse erstellen

// Ordner Textures
$Texturesuuid = generateUUID();

$verzeichnistextur = array();
$verzeichnistextur['folderName'] = 'Textures';
$verzeichnistextur['type'] = '0';
$verzeichnistextur['version'] = '1';
$verzeichnistextur['folderID'] = $Texturesuuid;
$verzeichnistextur['agentID'] = $benutzeruuid;
$verzeichnistextur['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnistextur);

// Ordner Sounds
$Soundsuuid = generateUUID();

$verzeichnisSounds = array();
$verzeichnisSounds['folderName'] = 'Sounds';
$verzeichnisSounds['type'] = '1';
$verzeichnisSounds['version'] = '1';
$verzeichnisSounds['folderID'] = $Soundsuuid;
$verzeichnisSounds['agentID'] = $benutzeruuid;
$verzeichnisSounds['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisSounds);

// Ordner Calling Cards
$CallingCardsuuid = generateUUID();

$verzeichnisCallingCards = array();
$verzeichnisCallingCards['folderName'] = 'Calling Cards';
$verzeichnisCallingCards['type'] = '2';
$verzeichnisCallingCards['version'] = '2';
$verzeichnisCallingCards['folderID'] = $CallingCardsuuid;
$verzeichnisCallingCards['agentID'] = $benutzeruuid;
$verzeichnisCallingCards['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisCallingCards);

// Ordner Landmarks
$Landmarksuuid = generateUUID();

$verzeichnisLandmarks = array();
$verzeichnisLandmarks['folderName'] = 'Landmarks';
$verzeichnisLandmarks['type'] = '3';
$verzeichnisLandmarks['version'] = '1';
$verzeichnisLandmarks['folderID'] = $Landmarksuuid;
$verzeichnisLandmarks['agentID'] = $benutzeruuid;
$verzeichnisLandmarks['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisLandmarks);

// Ordner My Inventory
$MyInventoryuuid = generateUUID();

$verzeichnisMyInventory = array();
$verzeichnisMyInventory['folderName'] = 'My Inventory';
$verzeichnisMyInventory['type'] = '8';
$verzeichnisMyInventory['version'] = '17';
$verzeichnisMyInventory['folderID'] = $neuHauptFolderID;
$verzeichnisMyInventory['agentID'] = $benutzeruuid;
$verzeichnisMyInventory['parentFolderID'] = '00000000-0000-0000-0000-000000000000';

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisMyInventory);

// Ordner Photo Album
$PhotoAlbumuuid = generateUUID();

$verzeichnisPhotoAlbum = array();
$verzeichnisPhotoAlbum['folderName'] = 'Photo Album';
$verzeichnisPhotoAlbum['type'] = '15';
$verzeichnisPhotoAlbum['version'] = '1';
$verzeichnisPhotoAlbum['folderID'] = $PhotoAlbumuuid;
$verzeichnisPhotoAlbum['agentID'] = $benutzeruuid;
$verzeichnisPhotoAlbum['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisPhotoAlbum);

// Ordner Clothing
$Clothinguuid = generateUUID();

$verzeichnisClothing = array();
$verzeichnisClothing['folderName'] = 'Clothing';
$verzeichnisClothing['type'] = '5';
$verzeichnisClothing['version'] = '3';
$verzeichnisClothing['folderID'] = $Clothinguuid;
$verzeichnisClothing['agentID'] = $benutzeruuid;
$verzeichnisClothing['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisClothing);

// Ordner Objects
$Objectsuuid = generateUUID();

$verzeichnisObjects = array();
$verzeichnisObjects['folderName'] = 'Objects';
$verzeichnisObjects['type'] = '6';
$verzeichnisObjects['version'] = '1';
$verzeichnisObjects['folderID'] = $Objectsuuid;
$verzeichnisObjects['agentID'] = $benutzeruuid;
$verzeichnisObjects['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisObjects);

// Ordner Notecards
$Notecardsuuid = generateUUID();

$verzeichnisNotecards = array();
$verzeichnisNotecards['folderName'] = 'Notecards';
$verzeichnisNotecards['type'] = '7';
$verzeichnisNotecards['version'] = '1';
$verzeichnisNotecards['folderID'] = $Notecardsuuid;
$verzeichnisNotecards['agentID'] = $benutzeruuid;
$verzeichnisNotecards['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisNotecards);

// Ordner Scripts
$Scriptsuuid = generateUUID();

$verzeichnisScripts = array();
$verzeichnisScripts['folderName'] = 'Scripts';
$verzeichnisScripts['type'] = '10';
$verzeichnisScripts['version'] = '1';
$verzeichnisScripts['folderID'] = $Scriptsuuid;
$verzeichnisScripts['agentID'] = $benutzeruuid;
$verzeichnisScripts['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisScripts);

// Ordner Body Parts
$BodyPartsuuid = generateUUID();

$verzeichnisBodyParts = array();
$verzeichnisBodyParts['folderName'] = 'Body Parts';
$verzeichnisBodyParts['type'] = '13';
$verzeichnisBodyParts['version'] = '5';
$verzeichnisBodyParts['folderID'] = $BodyPartsuuid;
$verzeichnisBodyParts['agentID'] = $benutzeruuid;
$verzeichnisBodyParts['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisBodyParts);

// Ordner Trash
$Trashuuid = generateUUID();

$verzeichnisTrash = array();
$verzeichnisTrash['folderName'] = 'Trash';
$verzeichnisTrash['type'] = '14';
$verzeichnisTrash['version'] = '1';
$verzeichnisTrash['folderID'] = $Trashuuid;
$verzeichnisTrash['agentID'] = $benutzeruuid;
$verzeichnisTrash['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisTrash);

// Ordner Lost And Found
$LostAndFounduuid = generateUUID();

$verzeichnisLostAndFound = array();
$verzeichnisLostAndFound['folderName'] = 'Lost And Found';
$verzeichnisLostAndFound['type'] = '16';
$verzeichnisLostAndFound['version'] = '1';
$verzeichnisLostAndFound['folderID'] = $LostAndFounduuid;
$verzeichnisLostAndFound['agentID'] = $benutzeruuid;
$verzeichnisLostAndFound['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisLostAndFound);

// Ordner Animations
$Animationsuuid = generateUUID();

$verzeichnisAnimations = array();
$verzeichnisAnimations['folderName'] = 'Animations';
$verzeichnisAnimations['type'] = '20';
$verzeichnisAnimations['version'] = '1';
$verzeichnisAnimations['folderID'] = $Animationsuuid;
$verzeichnisAnimations['agentID'] = $benutzeruuid;
$verzeichnisAnimations['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisAnimations);

// Ordner Gestures
$Gesturesuuid = generateUUID();

$verzeichnisGestures = array();
$verzeichnisGestures['folderName'] = 'Gestures';
$verzeichnisGestures['type'] = '21';
$verzeichnisGestures['version'] = '1';
$verzeichnisGestures['folderID'] = $Gesturesuuid;
$verzeichnisGestures['agentID'] = $benutzeruuid;
$verzeichnisGestures['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisGestures);


// Friends
$Friendsuuid = generateUUID();

$verzeichnisFriends = array();
$verzeichnisFriends['folderName'] = 'Friends';
$verzeichnisFriends['type'] = '2';
$verzeichnisFriends['version'] = '2';
$verzeichnisFriends['folderID'] = $Friendsuuid;
$verzeichnisFriends['agentID'] = $benutzeruuid;
$verzeichnisFriends['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisFriends);

// Favorites
$Favoritesuuid = generateUUID();

$verzeichnisFavorites = array();
$verzeichnisFavorites['folderName'] = 'Favorites';
$verzeichnisFavorites['type'] = '23';
$verzeichnisFavorites['version'] = '1';
$verzeichnisFavorites['folderID'] = $Favoritesuuid;
$verzeichnisFavorites['agentID'] = $benutzeruuid;
$verzeichnisFavorites['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisFavorites);

// Current Outfit
$CurrentOutfituuid = generateUUID();

$verzeichnisCurrentOutfit = array();
$verzeichnisCurrentOutfit['folderName'] = 'Current Outfit';
$verzeichnisCurrentOutfit['type'] = '46';
$verzeichnisCurrentOutfit['version'] = '1';
$verzeichnisCurrentOutfit['folderID'] = $CurrentOutfituuid;
$verzeichnisCurrentOutfit['agentID'] = $benutzeruuid;
$verzeichnisCurrentOutfit['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisCurrentOutfit);

// All
$Alluuid = generateUUID();

$verzeichnisAll = array();
$verzeichnisAll['folderName'] = 'All';
$verzeichnisAll['type'] = '2';
$verzeichnisAll['version'] = '1';
$verzeichnisAll['folderID'] = $Alluuid;
$verzeichnisAll['agentID'] = $benutzeruuid;
$verzeichnisAll['parentFolderID'] = $neuHauptFolderID;

$statement = $pdo->prepare("INSERT INTO inventoryfolders (folderName, type, version, folderID, agentID, parentFolderID) VALUES (:folderName, :type, :version, :folderID, :agentID, :parentFolderID)");
$statement->execute($verzeichnisAll);
?>
