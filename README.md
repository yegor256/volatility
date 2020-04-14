[![Build Status](https://travis-ci.org/yegor256/volatility.svg)](https://travis-ci.org/yegor256/volatility)
[![Maintainability](https://api.codeclimate.com/v1/badges/45932400a1661926a3ba/maintainability)](https://codeclimate.com/github/yegor256/volatility/maintainability)
[![Hits-of-Code](https://hitsofcode.com/github/yegor256/volatility)](https://hitsofcode.com/github/yegor256/volatility)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://github.com/yegor256/takes/blob/master/LICENSE.txt)

It's an experimental way to calculate how "volatile" is a project
source code repository, by comparing the amount of dead code (rarely touched)
with the amount of actively modified one. More or less detailed theoretical summary
is in [theory.pdf](https://github.com/downloads/yegor256/volatility/theory.pdf).

You need to have Ruby 2.6+ installed. Then you install this tool:

```bash
$ gem install volatility
```

Then, you run it:

```bash
$ volatility --help
```

## How to contribute

Read [these guidelines](https://www.yegor256.com/2014/04/15/github-guidelines.html).
Make sure your build is green before you contribute
your pull request. You will need to have [Ruby](https://www.ruby-lang.org/en/) 2.3+ and
[Bundler](https://bundler.io/) installed. Then:

```
$ bundle update
$ bundle exec rake
```

If it's clean and you don't see any error messages, submit your pull request.
