<?php
function get_members($group=FALSE,$inclusive=FALSE) {
    // Active Directory server
    $ldap_host = "dc.domain.local";

    // Active Directory DN
    $ldap_dn = "OU=Users,OU=Users,DC=domain,DC=local";

    // Active Directory user
    $user = "domain\Administrator";
    $password = "Qwerty123";

    // User attributes we want to keep
    // List of User Object properties:
    // http://www.dotnetactivedirectory.com/Understanding_LDAP_Active_Directory_User_Object_Properties.html
    $keep = array(
        "cn",
        "mobile",
        "telephonenumber"
    );

    // Connect to AD
    $ldap = ldap_connect($ldap_host) or die("Could not connect to LDAP");
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    ldap_bind($ldap,$user,$password) or die("Could not bind to LDAP");

    // Begin building query
    if($group) $query = "(&"; else $query = "";

    // list enabled users
    $query .= "(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";

    // Close query
    if($group) $query .= ")"; else $query .= "";

    // Search AD
    $results = ldap_search($ldap,$ldap_dn,$query);
    $entries = ldap_get_entries($ldap, $results);

    // Remove first entry (it's always blank)
    array_shift($entries);

    $output = array(); // Declare the output array

    $i = 0; // Counter
    // Build output array
    foreach($entries as $u) {
        foreach($keep as $x) {
            // Check for attribute
            if(isset($u[$x][0])) $attrval = $u[$x][0]; else $attrval = NULL;
 
            // Append attribute to output array
            $output[$i][$x] = $attrval;
        }        
        $i++;
    }
    return $output;
}

$quest = get_members();

array_multisort($quest, SORT_ASC);

// Sort by CN
$tmp = Array(); 
foreach($quest as &$ma) 
    $tmp[] = &$ma["cn"]; 
array_multisort($tmp, $quest);

// Output
print "
    <table align='center'>
    <tbody>
    <tr><td>
    <TABLE class='atable' border='1'><thead>";
    print "<TR>";
    print "<TH> Name </TH>";
    print "<TH> Phone Number </TH>";
    print "<TH> Mobile Number </TH>";
    print "</TR></thead><tbody>";

for($i=0; $i<count($quest); $i++) {
    print "<TR>";
    print "<TD>" . $quest[$i]["cn"] . " </TD>";
    print "<TD>" . $quest[$i]["telephonenumber"] . " </TD>";
    print "<TD>" . $quest[$i]["mobile"] . " </TD>";
    print "</TR>";
}
print "</tbody></table>
    </td>
    </tr>
    </tbody></table>";

?>