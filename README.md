[![Build Status](https://travis-ci.org/yegor256/volatility.svg)](https://travis-ci.org/yegor256/volatility)
[![Maintainability](https://api.codeclimate.com/v1/badges/45932400a1661926a3ba/maintainability)](https://codeclimate.com/github/yegor256/volatility/maintainability)
[![Hits-of-Code](https://hitsofcode.com/github/yegor256/volatility)](https://hitsofcode.com/github/yegor256/volatility)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://github.com/yegor256/takes/blob/master/LICENSE.txt)

It's an experimental way to calculate how "volatile" is a project
source code repository, by comparing the amount of dead code (rarely touched)
with the amount of actively modified one. More or less detailed theoretical summary
is in [theory.pdf](https://github.com/downloads/yegor256/volatility/theory.pdf).

### Build
```
python3 setup.py sdist bdist_wheel
```

### Upload package
```
python3 -m twine upload --repository-url https://test.pypi.org/legacy/ dist/*
```

Next, calculate the volatility, using `volatility` command.
For a Git repository:

```
$ volatility --path .
```
Volatility will be output in percentages.

That's it.
