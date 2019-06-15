[![Build Status](https://travis-ci.org/yegor256/volatility.svg)](https://travis-ci.org/yegor256/volatility)
[![Maintainability](https://api.codeclimate.com/v1/badges/45932400a1661926a3ba/maintainability)](https://codeclimate.com/github/yegor256/volatility/maintainability)
[![Hits-of-Code](https://hitsofcode.com/github/yegor256/volatility)](https://hitsofcode.com/github/yegor256/volatility)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://github.com/yegor256/takes/blob/master/LICENSE.txt)

It's an experimental way to calculate how "volatile" is a project
source code repository, by comparing the amount of dead code (rarely touched)
with the amount of actively modified one. More or less detailed theoretical summary
is in [theory.pdf](https://github.com/downloads/yegor256/volatility/theory.pdf).

Prerequisites:

* [cloc.pl](http://sourceforge.net/projects/cloc/files/) &mdash;
  a Perl script that counts total lines of code in a code base by
  programming language;

* [volatility.php](https://github.com/yegor256/volatility/blob/master/volatility.php) &mdash;
  our custom PHP 5.3 script that collects statistical information from SVN and
  Git logs.

First, collect lines-of-code data (we assume that your source code
has been already checked-out/cloned into `PROJECT` directory):

```bash
$ ./cloc-1.56.pl --xml --quiet --progress-rate=0 PROJECT > cloc.xml
```

Next, calculate the volatility, using our custom PHP script. For Git
repository:

```
$ git --git-dir PROJECT/.git log --reverse --format=short --stat=1000 --stat-name-width=950 | \
  php volatility.php --git > vol.json
```

For a Subversion repository:

```
$ svn log -r1:HEAD -v PROJECT | php volatility.php --svn > vol.json
```

The `vol.json` file will contain the metrics collected.

That's it.
