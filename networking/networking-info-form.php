<?php
/*
 
  ----------------------------------------------------------------------
GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2004 by the INDEPNET Development Team.
 
 http://indepnet.net/   http://glpi.indepnet.org
 ----------------------------------------------------------------------
 LICENSE

This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
 
// Based on:
// IRMA, Information Resource-Management and Administration
// Christian Bauer 
// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

include ("_relpos.php");
include ($phproot . "/glpi/includes.php");
include ($phproot . "/glpi/includes_networking.php");
include ($phproot . "/glpi/includes_reservation.php");
include ($phproot . "/glpi/includes_tracking.php");
include ($phproot . "/glpi/includes_financial.php");

if(isset($_GET)) $tab = $_GET;
if(empty($tab) && isset($_POST)) $tab = $_POST;
if(!isset($tab["ID"])) $tab["ID"] = "";
if(!isset($tab["withtemplate"])) $tab["withtemplate"] = "";

if (isset($_POST["add"]))
{
	checkAuthentication("admin");
	addNetdevice($_POST);
	logEvent(0, "networking", 4, "inventory", $_SESSION["glpiname"]." added item name ".$_POST["name"].".");
	header("Location: ".$_SERVER['HTTP_REFERER']);
}
else if (isset($tab["delete"]))
{
	checkAuthentication("admin");
	if (!empty($tab["withtemplate"]))
		deleteNetdevice($tab,1);
	else deleteNetdevice($tab);

	logEvent($tab["ID"], "networking", 4, "inventory", $_SESSION["glpiname"] ."deleted item.");
	if(!empty($tab["withtemplate"])) 
		header("Location: ".$cfg_install["root"]."/setup/setup-templates.php");
	 else 
	header("Location: ".$cfg_install["root"]."/networking/");
}
else if (isset($_POST["restore"]))
{
	checkAuthentication("admin");
	restoreNetdevice($_POST);
	logEvent($tab["ID"], "networking", 4, "inventory", $_SESSION["glpiname"]." restored item.");
	header("Location: ".$cfg_install["root"]."/networking/");
}
else if (isset($_POST["purge"]))
{
	checkAuthentication("admin");
	deleteNetdevice($_POST,1);
	logEvent($tab["ID"], "networking", 4, "inventory", $_SESSION["glpiname"]." purge item.");
	header("Location: ".$cfg_install["root"]."/networking/");
}
else if (isset($_POST["update"]))
{
	checkAuthentication("admin");
	updateNetdevice($_POST);
	logEvent($_POST["ID"], "networking", 4, "inventory", $_SESSION["glpiname"]." updated item.");
	header("Location: ".$_SERVER['HTTP_REFERER']);
}
else
{
	if (empty($_GET["ID"]))
	checkAuthentication("admin");
	else checkAuthentication("normal");


	commonHeader($lang["title"][6],$_SERVER["PHP_SELF"]);

	if (!empty($tab["withtemplate"])) {

		showNetworkingForm($_SERVER["PHP_SELF"],$tab["ID"], $tab["withtemplate"]);
		
		if (!empty($tab["ID"])){
		showInfocomForm($cfg_install["root"]."/infocoms/infocoms-info-form.php",NETWORKING_TYPE,$tab["ID"],1,$tab["withtemplate"]);
		showPorts($tab["ID"], NETWORKING_TYPE,$tab["withtemplate"]);
		if ($tab["withtemplate"]!=2)
		showPortsAdd($tab["ID"],NETWORKING_TYPE);
		
		showContractAssociated(NETWORKING_TYPE,$tab["ID"],$tab["withtemplate"]);
		}
		
	} else {

		if (isAdmin($_SESSION["glpitype"])&&isset($_POST["delete_inter"])&&!empty($_POST["todel"])){
			$j=new Job;
			foreach ($_POST["todel"] as $key => $val){
				if ($val==1) $j->deleteInDB($key);
				}
			}

	
		if (showNetworkingForm ($_SERVER["PHP_SELF"],$tab["ID"])){
		showPorts($tab["ID"],NETWORKING_TYPE);

		showPortsAdd($tab["ID"],NETWORKING_TYPE);

		showInfocomForm($cfg_install["root"]."/infocoms/infocoms-info-form.php",NETWORKING_TYPE,$tab["ID"]);
		showContractAssociated(NETWORKING_TYPE,$tab["ID"]);
		showJobListForItem($_SESSION["glpiname"],NETWORKING_TYPE,$tab["ID"]);
		showOldJobListForItem($_SESSION["glpiname"],NETWORKING_TYPE,$tab["ID"]);
		}
	}
	commonFooter();
}



?>
