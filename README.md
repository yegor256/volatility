[![Made By Teamed.io](http://img.teamed.io/btn.svg)](http://www.teamed.io)
[![DevOps By Rultor.com](http://www.rultor.com/b/yegor256/volatility)](http://www.rultor.com/p/yegor256/volatility)

[![Build Status](https://travis-ci.org/yegor256/volatility.svg)](https://travis-ci.org/yegor256/volatility)
[![Build status](https://ci.appveyor.com/api/projects/status/3i9r5lak77mmfbr0?svg=true)](https://ci.appveyor.com/project/yegor256/volatility-1rwvv)

[![Gem Version](https://badge.fury.io/rb/volatility.svg)](http://badge.fury.io/rb/volatility)
[![Dependency Status](https://gemnasium.com/yegor256/volatility.svg)](https://gemnasium.com/yegor256/volatility)
[![Code Climate](http://img.shields.io/codeclimate/github/yegor256/volatility.svg)](https://codeclimate.com/github/yegor256/volatility)
[![Coverage Status](https://coveralls.io/repos/yegor256/volatility/badge.svg)](https://coveralls.io/r/yegor256/volatility)

It's an experimental way to calculate how "volatile" is a project
source code repository. More or less detailed theoretical summary
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

> $ ./cloc-1.56.pl --xml --quiet --progress-rate=0 PROJECT > cloc.xml

Next, calculate the volatility, using our custom PHP script. For Git
repository:

> $ git --git-dir PROJECT/.git log --reverse --format=short --stat=1000 --stat-name-width=950 | php volatility.php --git > vol.json

for Subversion repository:

> $ svn log -r1:HEAD -v PROJECT | php volatility.php --svn > vol.json

That's it. Please sends us that `cloc.xml`
and `vol.json` to [yegor@tpc2.com](mailto:yegor@tpc2.com?subject=volatility of).
