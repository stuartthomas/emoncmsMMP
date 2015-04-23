<?php

$username = "stuart";
$password = "password";
$server   = "localhost";
$database = "emoncms";

// Create connection
$conn = mysqli_connect($server, $username, $password, $database);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
	

$user2 = $_SESSION['username'];
$sql = "SELECT usertype FROM users WHERE username = '$user2'"; 

$result = mysqli_query($conn, $sql);
// echo $result;
$res = "null";
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
		
        
		$res = $row["usertype"];
    }
} else {
    echo "0 results";
}





mysqli_close($conn);

if($res=="admin"){



/*
    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
*/

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

function admin_controller()
{
    global $mysqli,$session,$route,$updatelogin;

    // Allow for special admin session if updatelogin property is set to true in settings.php
    // Its important to use this with care and set updatelogin to false or remove from settings
    // after the update is complete.
    if ($updatelogin || $session['admin'])
        $sessionadmin = true;

    if ($sessionadmin)
    {
        if ($route->action == 'view') $result = view("Modules/admin/admin_main_view.php", array());

        if ($route->action == 'db')
        {
            $applychanges = get('apply');
            if (!$applychanges) $applychanges = false;
            else $applychanges = true;

            require "Modules/admin/update_class.php";
            require_once "Lib/dbschemasetup.php";

            $update = new Update($mysqli);

            $updates = array();
            $updates[] = array(
                'title'=>"Database schema",
                'description'=>"",
                'operations'=>db_schema_setup($mysqli,load_db_schema(),$applychanges)
            );

            if (!$updates[0]['operations']) {

            // In future versions we could check against db version number as to what updates should be applied
            $updates[] = $update->u0001($applychanges);
            //$updates[] = $update->u0002($applychanges);
            $updates[] = $update->u0003($applychanges);
            $updates[] = $update->u0004($applychanges);

            }

            $result = view("Modules/admin/update_view.php", array('applychanges'=>$applychanges, 'updates'=>$updates));
        }

        if ($route->action == 'users' && $session['write'] && $session['admin'])
        {
            $result = view("Modules/admin/userlist_view.php", array());
        }

        if ($route->action == 'userlist' && $session['write'] && $session['admin'])
        {
            $data = array();
            $result = $mysqli->query("SELECT id,username,email,usertype FROM users");
            while ($row = $result->fetch_object()) $data[] = $row;
            $result = $data;
        }

        if ($route->action == 'setuser' && $session['write'] && $session['admin'])
        {
            $_SESSION['userid'] = intval(get('id'));
            header("Location: ../user/view");
        }
    }

    return array('content'=>$result);
}
}
else{
echo "access denied";
}