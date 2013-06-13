<?php
// Create some user access variables
$isRegistered = isset($user->uid);
$isCurator = is_array($user->roles) && in_array('sk_curator', $user->roles);
$isEditor = is_array($user->roles) && in_array('sk_editor', $user->roles);
$isAdmin = user_access('administer site configuration');
?>