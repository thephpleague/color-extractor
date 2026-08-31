# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [GitHub](https://github.com/thephpleague/color-extractor/pulls).


## Pull Requests

- **Coding Standard** - Run `composer -dtools/php-cs-fixer install` and `vendor/bin/php-cs-fixer fix -vvv` to make your code conform.

- **Add tests!** - Your patch won’t be accepted if it doesn’t have tests.

- **Document any change in behaviour** - Make sure the README and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow semver. Randomly breaking public APIs is not an option.

- **Create topic branches** - Don’t ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.


## Running Tests

First install PHPUnit:

```bash
composer -dtools/phpunit install
```

Then generate the codebase autoloader if it doesn’t already exist:

```bash
composer dump-autoload
```

You’re now ready to run tests:

```bash
vendor/bin/phpunit
```

Some tests may be skipped if you don’t have the required extensions installed.
Don’t worry though: the CI will run them all.


**Happy coding**!
