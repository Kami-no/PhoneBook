<?php
// Active Directory server
$ldap_host = 'dc.domain.local';

// Active Directory DN
$ldap_dn = 'OU=Users,DC=domain,DC=local';

// Active Directory user
$user = 'domain\Administrator';
$password = 'Qwerty123';

// User attributes we want to keep
// List of User Object properties:
// http://www.dotnetactivedirectory.com/Understanding_LDAP_Active_Directory_User_Object_Properties.html
$keep = array(
    'cn',
    'mobile',
    'telephonenumber'
);

?>