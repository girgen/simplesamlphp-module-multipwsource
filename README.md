# simplesamlphp-module-multipwsource
SimpleSAMLphp module that accepts a list of sources for authentication, and tries one at a time until a we succeed or run out of sources.
multipwsource

Use more than one source for authentication, and try one at a time until we
succeed or run out of sources. This module does not present any choices to the
user, but simply asks for login name and password, and tries the sources one
at a time.

Most commonly used to first try one or more LDAP directories, followed by a
"local" SQL database as fallback.

All that is required is one single config parameter in authsources.php:
"sources". It is an array that points to other authsources. All authsources in
the list must be ones that accept username and password, i.e. they must extend
sspmod_core_Auth_UserPassBase.

A typical config/authsources.php:

``` php
'jb-multi' => array(
        'multipwsource:UserPassMulti',
        'sources' => array('jb-ldap', 'test-jb-sql'),
),

'test-jb-sql' => array(
        'sqlauth:SQL',
...
),

'jb-ldap' => array(
        'ldap:LDAP',

        // The hostname of the LDAP server.
        'hostname' => 'ldaps://ldapserver.jb.com',
...
```

Author
------
Written by Palle Girgensohn <girgen@pingpong.net>
