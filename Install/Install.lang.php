<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-19-2022
 ****************************************************************
 *
 *
 ****************************************************************/



// constants
define('VERSION_NO', '1.2');
define('OLDVERSION_NO', '1.04');
define('FILE_INSTALL', 'install.php');
define('FILE_LIST', 'list.php');
define('FILE_UPGRADE', 'upgrade.php');
define('URL_HOMEPAGE', 'https//aodhome.com');
define('URL_SOURCEFORGE', 'https://github.com/stimepy/TheAddressBook/');


//language
$lang = array(
    "title" => "The Address Book - Installation",
    "charset" =>    "iso-8859-1",
    "installVersion" => "(version ". VERSION_NO .")",
    "mainInstallText" => "<p> Thank you for choosing The Address Book!</p>
                 <p>By now, you should have configured your database login information in <b>config.php</b>.  If you have not, do that now and then upload it to the same folder as your Address Book installation. If you attempt to proceed with an incomplete or incorrect configuration The Address Book will not be installed correctly. <p> If you are ready to go, click Next to log into the database and set up all the tables pertaining to the Address Book.
                 <p style=\"color:red;\"><b>Warning: Clicking Next will overwrite a previous installation of the Address Book.</b> <br>If you want to <a href=\"".FILE_UPGRADE."\">upgrade</a> your installation, do not click Next.</p>",
    'TITLE_TAB' =>"The Address Book",
    'FOOTER_VERSION' =>"version:",
    'FOOTER_HOMEPAGE_LINK' =>"homepage",
    'complete' => "Address Book Installation Complete!",
    'removalmessage' => "<p> That's it! </p>    
		<p>  <A HREF=\"".FILE_LIST."\">Click here</A> to go straight to the main list of your Address Book and begin entries.
		<br> You will be automatically logged in as admin. If you wish to add entries as some other user, then either create
		<br> a new user or toggle on \"Allow User Self Registration\" in options and log off and self register as somebody else.</p>
		<p>You should remove this files (install.php), (upgrade.php) and the folder (Install) from the server so others won't try to abuse it.</p>",
    'upgradeTitle' => "Address Book - Upgrade ". OLDVERSION_NO ." to ". VERSION_NO,
    "upgradeMessage" => "<p><div style=\"color:red;\"><b>WARNING</div> This is for upgrading your installation of the Address Book ONLY.</b></p>
					<p>This will only upgrade previous installations of The Address Book version ". OLDVERSION_NO ." to version ". VERSION_NO .". If you are beginning a new installation (or want to overwrite a previous installation), please use <A HREF=\"install.php\">install.php</A>.</p>",
    "readChangelog" => "<p>Please read the change log so your aware of the changes.</p>",
    "step1button" => "Next",
    "upgradeComplete" => "Address Book Upgrade Complete!",
    "success" =>    "Your installation has successfully upgraded from ".OLDVERSION_NO." to ".VERSION_NO.".",
    "returnToIndex" => "<a href=\"index.php\">Click here</a> to enter the Address Book.",
    "removalmessageUpgrade" => "<b><div style=\"color:#FF0000\">IMPORTANT!</div></b> You may want to delete the files and folder including the folder <i>Install<\i> <i>upgrade.php</i> and <i>install.php</i> from your server so that other people can not abuse it. Leaving these files on your server will pose a <b>great security risk</b> and could potentially damage your Address Book installation.",
);


// todo make a script that is runable.
$tables = array(
    'TABLE_ADDITIONALDATA' => "additionaldata",
    'TABLE_ADDRESS' => 'address',
    'TABLE_CONTACT'=> 'contact',
    'TABLE_EMAIL' => 'email',
    'TABLE_GROUPS' => 'groups',
    'TABLE_GROUPLIST' => 'grouplist',
    'TABLE_LANGUAGE' => 'language',
    'TABLE_MESSAGING' => 'messaging',
    'TABLE_OPTIONS' => 'options',
    'TABLE_OTHERPHONE' =>'otherphone',
    'TABLE_SCRATCHPAD' => 'scratchpad',
    'TABLE_USERS' => 'users',
    'TABLE_WEBSITES' => 'websites',

);


// todo make a script that is runable.
$columns = array(
    'additionaldata' => "id INT(11) NOT NULL DEFAULT '0', type VARCHAR(20) DEFAULT NULL, value TEXT",
    'address' => "refid INT NOT NULL AUTO_INCREMENT PRIMARY KEY, id INT(11) NOT NULL DEFAULT '0', type VARCHAR(20) NOT NULL DEFAULT '', line1 VARCHAR(100) DEFAULT NULL, line2 VARCHAR(100) DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, state VARCHAR(50) DEFAULT NULL, zip VARCHAR(20) DEFAULT NULL, country VARCHAR(3) DEFAULT NULL, phone1 VARCHAR(20) DEFAULT NULL, phone2 VARCHAR(20) DEFAULT NULL",
    'contact' => "id INT(11) NOT NULL AUTO_INCREMENT, firstname VARCHAR(40) NOT NULL DEFAULT '', lastname VARCHAR(80) NOT NULL DEFAULT '', middlename VARCHAR(40) DEFAULT NULL, primaryAddress INT(11) DEFAULT NULL, birthday DATE DEFAULT NULL, nickname VARCHAR(40) DEFAULT NULL, pictureURL VARCHAR(255) DEFAULT NULL, notes TEXT, lastUpdate DATETIME DEFAULT NULL, hidden INT(1) DEFAULT '0' NOT NULL, whoAdded VARCHAR(15), PRIMARY KEY (id)",
    'email' => "id INT(11) NOT NULL DEFAULT '0', email VARCHAR(100) DEFAULT NULL, type VARCHAR(20) DEFAULT NULL",
    'grouplist' => "groupid INT(11) NOT NULL DEFAULT '0', groupname VARCHAR(60) DEFAULT NULL, PRIMARY KEY (groupid)",
    'groups' => "id INT(11) NOT NULL DEFAULT '0', groupid INT(11) NOT NULL DEFAULT '0'",
    'language' => "id INT(11) NOT NULL AUTO_INCREMENT, filename VARCHAR(60) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci', fileLanguage VARCHAR(60) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci', defaultLang TINYINT(4) NULL DEFAULT '0', PRIMARY KEY (`id`)",
    'messaging' => "id INT(11) NOT NULL DEFAULT '0', handle VARCHAR(30) DEFAULT NULL, type VARCHAR(20) DEFAULT NULL",
    'options' => "bdayInterval INT(3) DEFAULT '21' NOT NULL, bdayDisplay INT(1) DEFAULT '1' NOT NULL, displayAsPopup INT(1) DEFAULT '0' NOT NULL, useMailScript INT(1) DEFAULT '1' NOT NULL, picAlwaysDisplay INT(1) DEFAULT '0' NOT NULL, picWidth INT(1) DEFAULT '140' NOT NULL, picHeight INT(1) DEFAULT '140' NOT NULL, picDupeMode INT(1) DEFAULT '1' NOT NULL, picAllowUpload INT(1) DEFAULT '1' NOT NULL, modifyTime VARCHAR(3) DEFAULT '0' NOT NULL, msgLogin TEXT NULL, msgWelcome VARCHAR(255) NULL, countryDefault CHAR(3) DEFAULT '0' NULL, allowUserReg INT(1) DEFAULT '0' NOT NULL, eMailAdmin int(1) NOT NULL default '0', requireLogin INT(1) DEFAULT '1' NOT NULL, language VARCHAR(25) NOT NULL, defaultLetter char(2) default NULL, limitEntries smallint(3) NOT NULL default '0'",
    'otherphone' => "id INT(11) NOT NULL DEFAULT '0', phone VARCHAR(20) DEFAULT NULL, type VARCHAR(20) DEFAULT NULL",
    'websites' => "id INT(11) NOT NULL DEFAULT '0', webpageURL VARCHAR(255) DEFAULT NULL, webpageName VARCHAR(255) DEFAULT NULL",
    'users' => "id INT(2) NOT NULL AUTO_INCREMENT, username VARCHAR(15) NOT NULL, usertype ENUM('admin','user','guest') NOT NULL DEFAULT 'user', password VARCHAR(32) NOT NULL DEFAULT '', email VARCHAR(50) NOT NULL, confirm_hash VARCHAR(50) NOT NULL, is_confirmed TINYINT(1) DEFAULT '0' NOT NULL, bdayInterval int(3) default NULL, bdayDisplay int(1) default NULL, displayAsPopup int(1) default NULL, useMailScript int(1) default NULL, language varchar(25) default NULL, defaultLetter char(2) default NULL, limitEntries smallint(3) default NULL, PRIMARY KEY (id), UNIQUE KEY username (username)",
    'scratchpad' => "notes TEXT NOT NULL",
);



