<?php
/**
 * Copyright (c) 2012-2019, Yegor Bugayenko
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

require_once __DIR__ . '/AbstractRepository.php';
require_once __DIR__ . '/Bash.php';
require_once __DIR__ . '/Cache.php';

/**
 * Subversion repository.
 * @author Yegor Bugayenko <yegor@tpc2.com>
 */
final class SvnRepository extends AbstractRepository
{
    /**
     * Public ctor.
     * @param string $name Name of the repo
     * @param string $url Git URL of the repo
     */
    public function __construct($name, $url)
    {
        parent::__construct($name);
        $this->_url = $url;
    }
    /**
     * Checkout directory.
     * @return string Absolute location of its dir
     */
    public function checkout()
    {
        $dir = Cache::path('export-svn-' . $this->name());
        if (file_exists($dir) && count(scandir($dir))) {
            echo "% SVN directory {$dir} already exists, no need to checkout\n";
        } else {
            echo "% SVN checking out ${dir}...\n";
            Bash::exec(
                'rm -rf '
                . ' ' . escapeshellcmd($dir)
                . ' && svn co --non-interactive --quiet --trust-server-cert'
                . ' ' . escapeshellcmd(
                    str_replace('{/trunk}', '/trunk', $this->_url)
                )
                . ' ' . escapeshellcmd($dir)
            );
            if (!file_exists($dir)) {
                throw new Exception("failed to SVN checkout '{$this}'");
            }
            echo "% checked out {$this->_url} into {$dir}\n";
        }
        return $dir;
    }
    /**
     * Get volatility of the project.
     * @return Scv Volatility metric
     */
    public function scv()
    {
        return new Scv(
            $this,
            function ($repo) {
                return 'svn log -r1:HEAD -v '
                    . escapeshellarg($repo->checkout());
            },
            '--svn'
        );
    }
}
