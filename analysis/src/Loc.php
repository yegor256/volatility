<?php
/**
 * Copyright (c) 2012-2016, Yegor Bugayenko
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

if (!defined('CLOC_PERL')) {
    define('CLOC_PERL', shell_exec('cd ~ && pwd') . '/apps/cloc/cloc-1.56.pl');
}

require_once __DIR__ . '/Bash.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Repository.php';

/**
 * Lines of code (LOC) metric of a repository.
 * @author Yegor Bugayenko <yegor@tpc2.com>
 */
final class Loc
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
     * Total lines of code.
     * @param string $lang Lanuage you're interested in
     * @return int Total lines
     */
    public function code($lang = 'Java')
    {
        $xml = simplexml_load_string(trim(file_get_contents($this->_cloc())));
        $attr = $xml->xpath("//language[@name='{$lang}']/@code");
        return (string) $attr[0];
    }
    /**
     * Commented lines of code.
     * @param string $lang Lanuage you're interested in
     * @return int Lines of Java code with comments
     */
    public function comments($lang = 'Java')
    {
        $xml = simplexml_load_string(trim(file_get_contents($this->_cloc())));
        $attr = $xml->xpath("//language[@name='{$lang}']/@comment");
        return (string) $attr[0];
    }
    /**
     * CLOC report.
     * @return string Absolute file name with CLOC report
     */
    private function _cloc()
    {
        $cache = Cache::path('cloc-' . $this->_repo->name() . '.xml');
        if (file_exists($cache) && filesize($cache) != 0) {
            echo "% cache file {$cache} already exists\n";
        } else {
            Bash::exec(
                escapeshellcmd(CLOC_PERL)
                . ' --xml --quiet --progress-rate=0 '
                . escapeshellcmd($this->_repo->checkout())
                . ' > ' . escapeshellarg($cache)
            );
        }
        if (!file_exists($cache) || filesize($cache) == 0) {
            throw new Exception(
                "failed to count '{$this->_repo}' into {$cache}"
            );
        }
        echo "% CLOC metrics are ready in {$cache}\n";
        return $cache;
    }
}
