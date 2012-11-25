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

if (!defined('VOLATILITY_PHP')) {
    define('VOLATILITY_PHP', __DIR__ . '/../../volatility.php');
}

require_once __DIR__ . '/Bash.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Repository.php';

/**
 * Source Code Volatility (SCV) metric of a repository.
 * @author Yegor Bugayenko <yegor@tpc2.com>
 */
final class Scv
{
    /**
     * Repo.
     * @var Repository
     */
    private $_repo;
    /**
     * Public ctor.
     * @param Repository $repo The repository to checkout from
     */
    public function __construct(Repository $repo)
    {
        $this->_repo = $repo;
    }
    /**
     * By changesets.
     * @return float SCV by changes
     */
    public function byChanges()
    {
        return $this->_by('changesets');
    }
    /**
     * By authors.
     * @return float SCV by authors
     */
    public function byAuthors()
    {
        return $this->_by('authors');
    }
    /**
     * Total number of made commits.
     * @return int Total number of events (commits) in the repo
     */
    public function commits()
    {
        $data = json_decode(file_get_contents($this->_json()), true);
        return $data['changesets']['numbers'];
    }
    /**
     * Log of SCM.
     * @return string Absolute file name with SCM log
     */
    private function _log()
    {
        $cache = Cache::path('vol-' . $this->_repo->name() . '.log');
        if (file_exists($cache) && filesize($cache) != 0) {
            echo "% logs exist already in {$cache}";
        } else {
            if ($this->_repo instanceof SvnRepository) {
                Bash::exec(
                    'svn log -r1:HEAD -v '
                    . escapeshellarg($this->_repo->checkout())
                    . ' > ' . escapeshellarg($cache)
                );
            } elseif ($this->_repo instanceof GitRepository) {
                Bash::exec(
                    'git --git-dir '
                    . escapeshellarg($this->_repo->checkout() . '/.git')
                    . ' log --format=short --reverse --stat=1000'
                    . ' --stat-name-width=950'
                    . ' > ' . escapeshellarg($cache)
                );
            } else {
                throw new Exception("'{$this->_repo}' is in Git or SVN?");
            }
        }
        if (!file_exists($cache)) {
            throw new Exception("failed to fetch logs for '{$this->_repo}'");
        }
        return $cache;
    }
    /**
     * JSON output of {@code volatility.php}.
     * @return string Absolute file name with JSON report
     */
    private function _json()
    {
        $dir = $this->_repo->checkout();
        $cache = Cache::path('vol-' . $this->_repo->name() . '.json');
        if (file_exists($cache) && filesize($cache) != 0) {
            echo "% vol cache file {$cache} already exists\n";
        } else {
            if ($this->_repo instanceof SvnRepository) {
                Bash::exec(
                    '/usr/bin/php ' . VOLATILITY_PHP . ' --svn < '
                    . escapeshellarg($this->_log())
                    . ' > '
                    . escapeshellarg($cache)
                );
            } elseif ($this->_repo instanceof GitRepository) {
                Bash::exec(
                    '/usr/bin/php ' . VOLATILITY_PHP . ' --git < '
                    . escapeshellarg($this->_log())
                    . ' > '
                    . escapeshellarg($cache)
                );
            } else {
                throw new Exception("'{$this->_repo}' is in Git or SVN?");
            }
            if (!file_exists($cache) || filesize($cache) == 0) {
                throw new Exception(
                    "failed to collect VOLATILITY stats for '{$this->repo}'"
                );
            }
        }
        if (!file_exists($cache) || filesize($cache) == 0) {
            throw new Exception("failed to calc '{$this->repo}' into {$cache}");
        }
        $data = json_decode(file_get_contents($cache), true);
        if ($data == null) {
            throw new Exception("empty JSON for '{$this->_repo}'");
        }
        echo "% project volatility metrics loaded from {$cache}\n";
        return $cache;
    }
    /**
     * By this label.
     * @param string $label The label to use
     * @return float SCV by the given label
     */
    private function _by($label)
    {
        $data = json_decode(file_get_contents($this->_json()), true);
        if (!array_key_exists($label, $data)) {
            throw new Exception(
                "failed to collect '{$label}' for '{$this->_repo}'"
            );
        }
        return $data[$label]['variance'];
    }
}
