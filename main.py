# The MIT License (MIT)
#
# Copyright (c) 2020 Aibolit
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included
# in all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.


import subprocess
import matplotlib.pyplot as plt


def calculate(dir):
    result = subprocess.run(['git', '--git-dir', dir + '/.git', 'log',
                             '--reverse',
                             '--format=short',
                             '--stat=1000', '--stat-name-width=950'],
                            stdout=subprocess.PIPE)
    parse(result.stdout.decode("utf-8"))


def find_next_commit(pos1, input):
    pos1 = input.find('commit ', pos1)
    pos2 = input.find('\n', pos1)
    pos2 = input.find('\n', pos2+1)
    pos2 = input.find('\n', pos2+1)
    pos2 = input.find('\n', pos2+1)
    pos2 = input.find('\n', pos2+1)
    pos1 = pos2 + 1
    return pos1


def show_histogram(values):
    plt.hist(values, bins=10)
    plt.show()


def parse(input):
    files = {}
    line = ''
    pos1 = find_next_commit(0, input)
    count = 0
    while(True):
        pos2 = input.find('|', pos1)
        pos3 = input.find('\n', pos1)
        line = input[pos1:pos3]
        if 'changed,' in line:
            if input.find('commit', pos1) > 0:
                pos1 = find_next_commit(pos1, input)
                continue
            else:
                print(files)
                break
        else:
            print('Count {}            {}'.format(count, input[pos1:pos2]))
            file = input[pos1:pos2].strip()
            if file in files:
                files[file] = files[file] + 1
            else:
                files[file] = 1
        pos1 = pos3 + 1
        count = count + 1
        if count > 10000:
            print(files)
            break

    show_histogram(list(files.values()))


if __name__ == "__main__":
    dir = 'D:\\Data\\volatility'
    calculate(dir)
