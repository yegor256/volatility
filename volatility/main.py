# -*- coding: utf-8 -*-

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
import re
import argparse
import logging
import codecs


files = {}


def calculate(dir):
    result = subprocess.run(['git', '--git-dir', dir + '/.git', 'log',
                             '--reverse',
                             '--format=short',
                             '--stat=1000', '--stat-name-width=950'],
                            stdout=subprocess.PIPE)
    return parse(result.stdout.decode("utf-8"))


def find_next_commit(pos1, input):
    pos2 = input.find('commit ', pos1)
    if pos2 < 0 or pos2 < pos1:
        return -1
    pos1 = pos2
    pos2 = input.find('Author: ', pos1)
    pos2 = input.find('\n', pos2+1)
    pos2 = input.find('\n', pos2+1)
    pos2 = input.find('\n', pos2+1)
    pos2 = input.find('\n', pos2+1)
    if pos2 < 0:
        return pos2
    pos1 = pos2 + 1
    return pos1


def parse(input):
    line = ''
    num = 1
    pos1 = find_next_commit(0, input)
    if pos1 >= 0:
        logging.info('Commit: {}'.format(num))
    num = num + 1
    with codecs.open("git_out.txt", "w+", "utf-8") as f:
        f.write(input)
    while(pos1 < len(input) and pos1 >= 0):
        pos2 = input.find('|', pos1)
        pos3 = input.find('\n', pos1)
        line = input[pos1:pos3]
        if 'changed,' in line or line[0:6] == 'commit':
            if input.find('commit', pos1+1) > 0:
                pos1 = find_next_commit(pos1, input)
                logging.info('Commit: {}'.format(num))
                num = num + 1
                continue
            else:
                if line[0:6] == 'commit':
                    logging.info('Commit: {}'.format(num))
                with open('files_out.txt', 'w+') as the_file:
                    the_file.write(str(files))
                break
        else:
            file = input[pos1:pos2].strip()
            if "=>" in file:
                f = file.split("=>")
                if f[0] in files:
                    prev = files[f[0]]
                    del files[f[0]]
                    files[f[1]] = prev
                    res = re.findall(r'\d+', input[pos2 + 1:pos3])
                    if int(res[0]) > 0:
                        files[f[1]] = prev + 1
            else:
                if file in files:
                    files[file] = files[file] + 1
                else:
                    files[file] = 1
        pos1 = pos3 + 1
    return {'files': files, 'commits': num}


def run_application():
    parser = argparse.ArgumentParser(add_help=False)
    parser.add_argument('--path', type=str, required=False, default='')
    args = parser.parse_args()
    folder = args.path
    if len(folder) > 0:
        data = calculate(folder)
    else:
        print('Missing --path parameter')
        return -1
    vals = list(data['files'].values())
    vals.sort(reverse=True)
    calcmax = max(vals)
    cnt = len(vals)
    X = []
    p = []
    sum1 = 0
    sum2 = 0
    for i in range(cnt):
        p.append(vals[i]/calcmax)
        X.append(i/cnt)
        sum1 = sum1 + i/cnt * vals[i]/calcmax
        sum2 = sum2 + vals[i]/calcmax

    mu = sum1/sum2
    sum1 = 0
    for i in range(cnt):
        sum1 = sum1 + (X[i] - mu) * (X[i] - mu) * p[i]

    return round(sum1/sum2 * 100, 2)


if __name__ == "__main__":
    run_application()
