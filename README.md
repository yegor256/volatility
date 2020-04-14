[![EO principles respected here](https://www.elegantobjects.org/badge.svg)](https://www.elegantobjects.org)
[![Managed by Zerocracy](https://www.0crat.com/badge/C3RFVLU72.svg)](https://www.0crat.com/p/C3RFVLU72)
[![DevOps By Rultor.com](http://www.rultor.com/b/yegor256/volatility)](http://www.rultor.com/p/yegor256/volatility)
[![We recommend RubyMine](https://www.elegantobjects.org/rubymine.svg)](https://www.jetbrains.com/ruby/)

[![Build Status](https://travis-ci.org/yegor256/volatility.svg)](https://travis-ci.org/yegor256/volatility)
[![Build status](https://ci.appveyor.com/api/projects/status/ndp6dbqajjgr7y7y?svg=true)](https://ci.appveyor.com/project/yegor256/volatility-jm2bc)
[![PDD status](http://www.0pdd.com/svg?name=yegor256/volatility)](http://www.0pdd.com/p?name=yegor256/volatility)
[![Gem Version](https://badge.fury.io/rb/volatility.svg)](http://badge.fury.io/rb/volatility)
[![Maintainability](https://api.codeclimate.com/v1/badges/74c909f06d4afa0d8001/maintainability)](https://codeclimate.com/github/yegor256/volatility/maintainability)

[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://github.com/yegor256/takes/volatility/master/LICENSE.txt)
[![Test Coverage](https://img.shields.io/codecov/c/github/yegor256/volatility.svg)](https://codecov.io/github/yegor256/volatility?branch=master)
[![Hits-of-Code](https://hitsofcode.com/github/yegor256/volatility)](https://hitsofcode.com/view/github/yegor256/volatility)

It's an experimental way to calculate how "volatile" is a project
source code repository, by comparing the amount of dead code (rarely touched)
with the amount of actively modified one. More or less detailed theoretical summary
is in [theory.pdf](https://github.com/yegor256/volatility/raw/master/theory.pdf).

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
