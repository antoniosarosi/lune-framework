# Pull Requests

Run `composer run php-cs-fixer` and `composer run tests` before submitting.

Don't worry about tests involving the database if you see "can't connect"
warnings. They will run correctly in the github workflow container, but if
you want to run them locally you can create a `.env.testing` file at the root
directory of the project and set the test database variables to match your
setup:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lune_tests # The database has to be created before running tests
DB_USERNAME=root
DB_PASSWORD=
```
