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

require_once __DIR__ . '/../src/GitRepository.php';
require_once __DIR__ . '/../src/Scv.php';

/**
 * Test case fo {@link Scv}.
 * @author Yegor Bugayenko <yegor@tpc2.com>
 */
final class ScvTest extends PHPUnit_Framework_TestCase
{
    public function testCalculatesScvForItself()
    {
        $repo = new GitRepository(
            'self',
            'git@github.com:yegor256/volatility.git'
        );
        $loc = new Scv(
            $repo,
            function ($repo) {
                return 'git --git-dir '
                    . escapeshellarg($repo->checkout() . '/.git')
                    . ' log --format=short --reverse --stat=1000'
                    . ' --stat-name-width=950';
            },
            '--git'
        );
        $this->assertGreaterThan(0, $loc->byChanges());
        $this->assertGreaterThan(0, $loc->byAuthors());
        $this->assertGreaterThan(0, $loc->commits());
    }
}
