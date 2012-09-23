<?php
/**
 * Copyright (c) 2012, Yegor Bugayenko
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met: 1) Redistributions of source code must retain the above
 * copyright notice, this list of conditions and the following
 * disclaimer. 2) Redistributions in binary form must reproduce the above
 * copyright notice, this list of conditions and the following
 * disclaimer in the documentation and/or other materials provided
 * with the distribution. 3) Neither the name of Yegor Bugayenko nor
 * the names of other contributors may be used to endorse or promote
 * products derived from this software without specific prior written
 * permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT
 * NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * This script calculates volatility of software source code in Subversion
 * or Git repository. As an input for the script we're using repository
 * logs.
 *
 * More details here: https://github.com/yegor256/volatility
 */

class Data {
    private $_changes;
    private $_authors;
    public function __construct() {
        $this->_changes = array();
        $this->_authors = array();
    }
    public function add($commit, $author, array $files) {
        foreach ($files as $file) {
            if (!isset($this->_changes[$file])) {
                $this->_changes[$file] = 0;
            }
            if (!isset($this->_authors[$file])) {
                $this->_authors[$file] = array();
            }
            $this->_changes[$file]++;
            $this->_authors[$file][$author] = true;
        }
    }
    public function move($from, $to) {
        if (isset($this->_changes[$from])) {
            $count = $this->_changes[$from];
            unset($this->_changes[$from]);
            $this->_changes[$to] = $count;
            foreach (array_keys($this->_authors[$from]) as $author) {
                $this->_authors[$to][$author] = true;
            }
            unset($this->_authors[$from]);
        }
    }
    public function metrics() {
        $authors = array();
        foreach ($this->_authors as $names) {
            $authors[] = count($names);
        }
        return array(
            'changesets' => $this->_calculate($this->_changes),
            'authors' => $this->_calculate($authors),
        );
    }
    private function _calculate(array $values) {
        if (empty($values)) {
            throw new Exception('empty set of values');
        }
        rsort($values);
        $numbers = count($values);
        $events = array_sum($values);
        $sum = 0;
        foreach ($values as $value=>$frequency) {
            $sum += ($value / ($numbers - 1)) * $frequency;
        }
        $mean = $sum / $events;
        $mods = 0;
        foreach ($values as $value=>$frequency) {
            $mods += abs($mean - ($value / ($numbers - 1))) * $frequency;
        }
        $deviation = $mods / $events;
        $squares = 0;
        foreach ($values as $value=>$frequency) {
            $squares += pow(abs($mean - ($value / ($numbers - 1))), 2) * $frequency;
        }
        $variance = $squares / $events;
        return array(
            'numbers' => $numbers,
            'events' => $events,
            'mean' => $mean,
            'deviation' => $deviation,
            'variance' => $variance,
        );
    }
}

class Stdin {
    private $_stdin;
    public function __construct() {
        $this->_stdin = fopen('php://stdin', 'r');
    }
    public function next() {
        if ($this->eof()) {
            throw new Exception('end of file');
        }
        $line = fgets($this->_stdin);
        return rtrim($line);
    }
    public function eof() {
        return feof($this->_stdin);
    }
}

interface Input {
    function data();
}

class GitInput implements Input {
    private $_in;
    public function __construct(Stdin $in) {
        $this->_in = $in;
    }
    public function data() {
        $data = new Data();
        while (!$this->_in->eof()) {
            $files = array();
            while (!$this->_in->eof()) {
                $next = $this->_in->next();
                if (preg_match('/^commit +([a-f0-9]{40})$/', $next, $matches) === 1) {
                    $commit = $matches[1];
                }
                if (preg_match('/^Author:(.*)$/', $next, $matches) === 1) {
                    $author = trim($matches[1]);
                }
                if (preg_match('/^ (.*) \|/', $next, $matches) !== 1) {
                    break;
                }
                $files[] = trim($matches[1]);
            }
            if (!empty($files)) {
                $data->add($commit, $author, $files);
            }
        }
        return $data;
    }
}

class SvnInput implements Input {
    private $_in;
    public function __construct(Stdin $in) {
        $this->_in = $in;
    }
    public function data() {
        $data = new Data();
        while (!$this->_in->eof()) {
            $files = array();
            while (!$this->_in->eof()) {
                $next = $this->_in->next();
                if (preg_match('/^(r\d+) \| (.*?) \|/', $next, $matches) === 1) {
                    $commit = $matches[1];
                    $author = $matches[2];
                }
                if (preg_match('/^   [A-Z] (.*?)(?: \(from (.*?):\d+\))?$/', $next, $matches) !== 1) {
                    break;
                }
                if (isset($matches[2])) {
                    $data->move($matches[2], $matches[1]);
                }
                $files[] = trim($matches[1]);
            }
            if (isset($commit)) {
                $data->add($commit, $author, $files);
            }
        }
        return $data;
    }
}

foreach ($argv as $arg) {
    if ($arg == '--git') {
        $input = new GitInput(new Stdin());
    }
    if ($arg == '--svn') {
        $input = new SvnInput(new Stdin());
    }
}

if (!isset($input)) {
    throw new Exception('specify either --git or --svn');
}

$data = $input->data();
$metrics = $data->metrics();
echo json_encode($metrics);

