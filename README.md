Source Code Volatility calculator
======

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
