<?php

$auth = false; // Assume user is not authenticated

if (isset( $PHP_AUTH_USER ) && isset($PHP_AUTH_PW)) {

    // Read the entire file into the variable $file_contents

    $filename = '/HOSTESS/login/logins.x9jh482k562skj3';
    $fp = fopen( $filename, 'r' );
    
    $file_contents = fread( $fp, filesize( $filename ) );
    fclose( $fp );

    // Place the individual lines from the file contents into an array.

    $lines = explode ( "\n", $file_contents );

    // Split each of the lines into a username and a password pair
    // and attempt to match them to $PHP_AUTH_USER and $PHP_AUTH_PW.

    foreach ( $lines as $line ) {

        list( $username, $password ) = explode( ':', $line );

print "$username:$password<br>";		

        if ( ( $username == "$PHP_AUTH_USER" ) &&
             ( $password == "$PHP_AUTH_PW" ) ) {

            // A match is found, meaning the user is authenticated.
            // Stop the search.

            $auth = true;
            break;

        }
    }
}

$auth = true;

if ( ! $auth ) {

    header( 'WWW-Authenticate: Basic realm="Private"' );
    header( 'HTTP/1.0 401 Unauthorized' );
    echo 'Authorization Required.';
    exit;

} else {

    echo '<P>You are authorized!</P>';
}

?> 