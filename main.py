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
import re
import requests
import argparse


files = {}
token = ''


def process_file(name):
    import json
    with open(name) as json_file:
        data = json.load(json_file)
        show_histogram(list(data.values()))


def start(url):
    prev = '{}{}{}'.format('https://api.github.com/repos/', url,
                           '/commits?per_page=100')
    r = requests.get(prev,
                     headers={'Content-Type': 'application/json',
                              'Authorization': 'token {}'.format(token)})
    data = r.json()
    if 'Link' in r.headers:
        links = r.headers['Link'].split(', ')
        for link in links:
            t = link.split('; ')
            if t[1] == 'rel="last"':
                prev = t[0].replace('<', '').replace('>', '')

    while(prev is not None):
        print(prev)
        r = requests.get('{}'.format(prev),
                         headers={'Content-Type': 'application/json',
                                  'Authorization': 'token {}'.format(token)})
        data = r.json()
        if len(data) == 0:
            break
        prev = None
        if 'Link' in r.headers:
            links = r.headers['Link'].split(', ')
            for link in links:
                t = link.split('; ')
                if t[1] == 'rel="prev"':
                    prev = t[0].replace('<', '').replace('>', '')
        get_commits(data)
    print('\n', '\n', '\n', '\n', '\n', '\n', '\n', files)
    show_histogram(list(files.values()))


def get_commits(data):
    for commit in reversed(data):
        r1 = requests.get(commit['url'],
                          headers={'Content-Type': 'application/json',
                                   'Authorization': 'token {}'.format(token)}
                          )
        thecommit = r1.json()
        if 'files' not in thecommit:
            print('{}{}'.format(commit['url'], ' does not have files'))
        else:
            for file in thecommit['files']:
                if 'previous_filename' in file and \
                   file['previous_filename'] != file['filename']:
                    prev = 0
                    if file['filename'] in files and \
                       files[file['filename']] > prev:
                        prev = files[file['filename']]
                    if file['previous_filename'] in files:
                        prev = files[file['previous_filename']]
                        del files[file['previous_filename']]
                    files[file['filename']] = prev
                if file['additions'] > 0 or file['deletions'] > 0 or \
                   file['changes'] > 0:
                    if file['filename'] in files:
                        files[file['filename']] = files[file['filename']] + 1
                    else:
                        files[file['filename']] = 1


def calculate(dir):
    result = subprocess.run(['git', '--git-dir', dir + '/.git', 'log',
                             '--reverse',
                             '--format=short',
                             '--stat=1000', '--stat-name-width=950'],
                            stdout=subprocess.PIPE)
    parse(result.stdout.decode("utf-8"))


def find_next_commit(pos1, input):
    while(True):
        pos1 = input.find('commit ', pos1)
        pos2 = input.find('Author: ', pos1)
        pos2 = input.find('\n', pos2+1)
        pos2 = input.find('\n', pos2+1)
        pos2 = input.find('\n', pos2+1)
        pos2 = input.find('\n', pos2+1)
        pos1 = pos2 + 1
        if input[pos1: pos1 + 6] != 'commit':
            break
    return pos1


def show_histogram(values):
    cnt = len(values)
    values.sort()
    values = values[:-(int(0.05*cnt))]
    plt.xlabel('Changes', fontsize=18)
    plt.ylabel('Files count', fontsize=16)
    plt.hist(values, bins=10)
    plt.show()


def parse(input):
    files = {}
    line = ''
    pos1 = find_next_commit(0, input)
    while(True):
        pos2 = input.find('|', pos1)
        pos3 = input.find('\n', pos1)
        line = input[pos1:pos3]
        if 'changed,' in line:
            if input.find('commit', pos1) > 0:
                pos1 = find_next_commit(pos1, input)
                continue
            else:
                with open('out.txt', 'w+') as the_file:
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

    show_histogram(list(files.values()))


if __name__ == "__main__":
    parser = argparse.ArgumentParser(add_help=False)
    parser.add_argument('--token', type=str, required=True, default='')
    args = parser.parse_args()
    token = args.token
    start('yegor256/volatility')
