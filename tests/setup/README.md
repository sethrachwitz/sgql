### Database unit tests
A live MySQL server is used to test certain queries using PHPUnit, but a user must be created to allow PHPUnit to communicate with the database.  This user (below) will be used to set up certain databases and users for the tests.  **All** databases and users created during the tests will follow the format `sgql_unittests_*`, so no other databases or users should be named in this format.

The tests automatically clean up after themselves, so the database is always in the same state before and after the tests.  If you wish to see the state of the database during a test, insert a `die()` statement in the test.  The next time the tests are run, they will automatically clean up whatever was left over.

#### Add PHPUnit database user
Run the following SQL as a root user:

```
CREATE USER 'sgql_tests'@'localhost' IDENTIFIED BY 'sgql';
GRANT ALL PRIVILEGES ON *.* TO 'sgql_tests'@'localhost' WITH GRANT OPTION;
```

This should only be done on a development machine, as this user will have root privileges.  The config file at `tests/database/mysql_config.php` contains the credentials so the tests will know where this user is at.  If your testing database is not `localhost`, you will need to modify this file and add it to your `.gitignore`.

This user will be the master user used to set up and tear down the testing environment.
