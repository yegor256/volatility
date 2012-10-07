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
 * It's a simple test for GitInput class from volatility.php.
 * Run it from command line as (PHPUnit 3.6+ has to be installed):
 *
 * $ phpunit ./test/GitInputTest.php
 */

defined('TESTING') || define('TESTING', true);
require_once __DIR__ . '/../volatility.php';

final class GitInputTest extends PHPUnit_Framework_TestCase {
    public function testProducesDataFromGitLog() {
        $this->markTestSkipped('doesnt work');
        $stdin = $this->getMock('Stdin');
        $stdin->expects($this->any())->method('next')->will(
            $this->onConsecutiveCalls(
                'commit fdad9dbadd187d8208e325d26d05cf533b81b582',
                'Author: John Doe <john.doe@example.com>',
                ' some-file.txt',
                '',
                '    some comment'
            )
        );
        $stdin->expects($this->any())->method('eof')->will(
            $this->onConsecutiveCalls(
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                true
            )
        );
        $input = new GitInput($stdin);
        $data = $input->data();
        $metrics = $data->metrics();
        $this->assertArrayHasKey('changesets', $metrics);
    }
}
