# encoding: utf-8
#
# Copyright (c) 2012-2016 Yegor Bugayenko
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the 'Software'), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

require 'date'
require 'volatility/metrics'

module Volatility
  # Git source code base.
  class Git
    def initialize(dir)
      @dir = dir
    end

    def metrics
      version = `git --version`.split(/ /)[2]
      fail "git version #{version} is too old, upgrade it to 2.0+" unless
        Gem::Version.new(version) >= Gem::Version.new('2.0')
      data().calculate
    end

    private

    def data
      cmd = [
        "cd #{@dir} && git",
        'log', '--reverse', '--format=short',
        '--stat=1000', '--stat-name-width=950',
        '--ignore-space-change', '--ignore-all-space',
        '--ignore-submodules', '--no-color',
        '--find-copies-harder', '-M', '--diff-filter=ACDM',
        '--', '.',
      ].join(' ')
      files = []
      data = Data.new()
      commit = ''
      author = ''
      `#{cmd}`.split(/\n/).each do |line|
        if match = line.match(/^commit +([a-f0-9]{40})$/)
          commit = match.captures
        end
        if match = line.match(/^Author:(.*)$/)
          author = match.captures
        end
        if match = line.match(/^ (.*) \|/)
          files.push match.captures
        else
          data.add(commit, author, files) unless files.empty?
          files = []
        end
      end
      data
    end
  end

  private

  class Data
    def add(commit, author, files)
      puts commit
      puts author
      puts files
    end
    def calculate
      {
        time: Time.now(),
        variance: 0.4
      }
    end
  end
end
