<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2008 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

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
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')){
	die("Sorry. You can't access directly to this file");
	}

function showProfileEntityUser($target,$ID,$prof){	
 	
 	global $DB,$LANG,$CFG_GLPI;
 	
	$canedit=haveRight("user","w");

	$show=true;
 	if (!empty($ID)&&$ID){
		$prof->getFromDB($ID);
	} else {
		$prof->getEmpty();
		$show=false;
	}
	 	
 	echo "<div class='center'>";
	echo "<table class='tab_cadre_fixe'><tr>";
	echo "<th>".$LANG["profiles"][22]." :&nbsp;&nbsp;&nbsp;&nbsp;".$prof->fields["name"]."</th></tr>";
	echo "<tr><th colspan='2'>".$LANG["Menu"][14]." (D=".$LANG["profiles"][29].", R=".$LANG["profiles"][28].")</th></tr>";
 	echo "</table>";
	echo "</div>";
  	
	if (!$show){
		return false;
	}

  	$query="SELECT glpi_users.*, glpi_users_profiles.FK_entities AS entity, glpi_users_profiles.ID AS linkID, glpi_users_profiles.dynamic as dynamic,glpi_users_profiles.recursive as recursive   
 		FROM glpi_users_profiles 
 		LEFT JOIN glpi_entities ON (glpi_entities.ID=glpi_users_profiles.FK_entities)
 		LEFT JOIN glpi_users ON (glpi_users.ID=glpi_users_profiles.FK_users)
 		WHERE glpi_users_profiles.FK_profiles=".$ID." AND glpi_users.deleted=0 ".getEntitiesRestrictRequest("AND","glpi_users_profiles")." 
 		ORDER BY glpi_entities.completename";

 	echo "<div class='center'>";
	echo "<table class='tab_cadre_fixe'>";
 	
	$i=0;
	$nb_per_line=3;

 	if ($result = $DB->query($query))
 	{ 
 		if ($DB->numrows($result)!=0)
	 	{

	 		$temp=-1;
	 		while ($data=$DB->fetch_array($result)) 
			{	
	 			if($data["entity"]!=$temp)
				{
					while ($i%$nb_per_line!=0)
					{
						if ($canedit){
							echo "<td width='10'>&nbsp;</td>";
						}
						echo "<td class='tab_bg_1'>&nbsp;</td>\n";
						$i++;
					}

					if ($i!=0) {
						echo "</table>";
						if ($canedit){
							echo "<div class='center'>";
							echo "<table width='100%' class='tab_glpi'>";
							echo "<tr><td><img src=\"".$CFG_GLPI["root_doc"]."/pics/arrow-left.png\" alt=''></td><td class='center'><a onclick= \"if ( markCheckboxes('profileuser_form".$rand."_$temp') ) return false;\" href='".$_SERVER['PHP_SELF']."?ID=$ID&amp;select=all'>".$LANG["buttons"][18]."</a></td>";
							
							echo "<td>/</td><td class='center'><a onclick= \"if ( unMarkCheckboxes('profileuser_form".$rand."_$temp') ) return false;\" href='".$_SERVER['PHP_SELF']."?ID=$ID&amp;select=none'>".$LANG["buttons"][19]."</a>";
							echo "</td><td align='left' width='80%'>";
							dropdownValue("glpi_entities","FK_entities",0,1,$_SESSION['glpiactiveentities']);
							echo "&nbsp;<input type='submit' name='moveentity' value=\"".$LANG["buttons"][20]."\"
 class='submit'>";
							echo "&nbsp;<input type='submit' name='deleteuser' value=\"".$LANG["buttons"][6]."\" class='submit'>";

							echo "</td></tr>";
							echo "</table>";
							echo "</div>";
						}
						echo "</div></form></td></tr>\n";
					}


					// New entity
		 			$i=0;
		 			$temp=$data["entity"];		

					$rand=mt_rand();
					echo "<tr class='tab_bg_2'>";
					echo "<td align='left'>"; 
					echo "<a href=\"javascript:showHideDiv('entity$temp$rand','imgcat$temp', '".GLPI_ROOT."/pics/folder.png','".GLPI_ROOT."/pics/folder-open.png');\">";
					echo "<img alt='' name='imgcat$temp' src=\"".GLPI_ROOT."/pics/folder.png\">&nbsp; <strong>".getDropdownName('glpi_entities',$data["entity"])."</strong>";
					echo "</a>"; 
					echo "</td>"; 
					echo "</tr>"; 
					echo "<tr><td>";
	 				
					echo "<form name='profileuser_form".$rand."_$temp' id='profileuser_form".$rand."_$temp' method='post' action=\"$target\">";
					echo "<div align='center' id='entity$temp$rand' style=\"display:none;\">\n"; 
					echo "<table class='tab_cadre_fixe'>\n";
				}

		 		if ($i%$nb_per_line==0) {
					if ($i!=0) echo "</tr>\n";
						echo "<tr class='tab_bg_1'>\n";
					$i=0;	
				}

				if ($canedit){
					echo "<td width='10'>";
					$sel="";
					if (isset($_GET["select"])&&$_GET["select"]=="all") $sel="checked";
					echo "<input type='checkbox' name='item[".$data["ID"]."]' value='1' $sel>";
					echo "</td>";
				}

				echo "<td class='tab_bg_1'>".formatUserName($data["ID"],$data["name"],$data["realname"],$data["firstname"],1);

				if ($data["dynamic"]||$data["recursive"]){
					echo "<strong>&nbsp;(";
					if ($data["dynamic"]) echo "D";
					if ($data["dynamic"]&&$data["recursive"]) echo ", ";
						if ($data["recursive"]) echo "R";
					echo ")</strong>";
				}
				echo "</td>\n";

				$i++;
 					
			}
			
			if ($i%$nb_per_line!=0)
			{
				while ($i%$nb_per_line!=0)
				{
					if ($canedit){
						echo "<td width='10'>&nbsp;</td>";
					}

					echo "<td class='tab_bg_1'>&nbsp;---</td>";

					$i++;
				}
				
			}
			if ($i!=0) {
				echo "</table>";
				if ($canedit){
					echo "<div class='center'>";
					echo "<table width='100%' class='tab_glpi'>";
					echo "<tr><td><img src=\"".$CFG_GLPI["root_doc"]."/pics/arrow-left.png\" alt=''></td><td class='center'><a onclick= \"if ( markCheckboxes('profileuser_form".$rand."_$temp') ) return false;\" href='".$_SERVER['PHP_SELF']."?ID=$ID&amp;select=all'>".$LANG["buttons"][18]."</a></td>";
					
					echo "<td>/</td><td class='center'><a onclick= \"if ( unMarkCheckboxes('profileuser_form".$rand."_$temp') ) return false;\" href='".$_SERVER['PHP_SELF']."?ID=$ID&amp;select=none'>".$LANG["buttons"][19]."</a>";
					echo "</td><td align='left' width='80%'>";
					dropdownValue("glpi_entities","FK_entities",0,1,$_SESSION['glpiactiveentities']);
					echo "&nbsp;<input type='submit' name='moveentity' value=\"".$LANG["buttons"][20]."\" class='submit'>";
					echo "&nbsp;<input type='submit' name='deleteuser' value=\"".$LANG["buttons"][6]."\" class='submit'>";
					echo "</td>";
					echo "</table>";
					echo "</div>";
				}
				echo "</div></form></td></tr>\n";	
			}

		}
 		else
 			echo "<tr><td class='tab_bg_1' align=center>".$LANG["profiles"][33]."</td></tr>";
 		}
 
 	echo "</table>";
	echo "</div>";

  }

?>
