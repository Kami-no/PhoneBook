<?php

// Import main config
require_once('config.php');

function get_members($group=FALSE,$inclusive=FALSE) {

    // Import LDAP config
    require_once('config-ldap.php');

    // Connect to AD
    $ldap = ldap_connect($ldap_host) or die('Could not connect to LDAP');
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    ldap_bind($ldap,$user,$password) or die('Could not bind to LDAP');

    // Begin building query
    if($group) $query = '(&'; else $query = '';

    $query .= '(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))';

    // Close query
    if($group) $query .= ')'; else $query .= '';

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

// Sort by names
array_multisort($quest, SORT_ASC);

// Import translation
$lang_file = 'langs/'.$lang.'.php';
require_once($lang_file);

// Output
print '
    <html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Система авторизации в LDAP</title>
    </head>
    <body>
    <table align="center" class="atable" border="1"><thead>';
    print '<TR>';
    print '<TH>'.$cn.'</TH>';
    print '<TH>'.$telephonenumber.'</TH>';
    print '<TH>'.$mobile.'</TH>';
    print '</TR></thead><tbody>';

for($i=0; $i<count($quest); $i++) {
    print '<TR>';
    print '<TD>'.$quest[$i]['cn'].' </TD>';
    print '<TD>'.$quest[$i]['telephonenumber'].' </TD>';
    print '<TD>'.$quest[$i]['mobile'].' </TD>';
    print '</TR>';
}
print '</table>
    </body><html>';

?>