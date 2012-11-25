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
 * It's a simple test for Data class from volatility.php.
 * Run it from command line as (PHPUnit 3.6+ has to be installed):
 *
 * $ phpunit ./test/DataTest.php
 */

defined('TESTING') || define('TESTING', true);
require_once __DIR__ . '/../volatility.php';

final class DataTest extends PHPUnit_Framework_TestCase
{
    private $_data;
    public function setUp()
    {
        $this->_data = new Data();
    }
    public function testThrowsForEmptyInput()
    {
        $this->setExpectedException('VolatilityException');
        $this->_data->metrics();
    }
    public function testGeneratesMetricsForSimpleInput()
    {
        $all = array();
        for ($file = 0; $file < 5; ++$file) {
            $all[] = 'file-' . $file;
        }
        for ($pos = 0; $pos < 10; ++$pos) {
            $files = $all;
            shuffle($files);
            $this->_data->add(
                $pos,
                'author',
                array_slice($files, 0, rand(1, count($files)))
            );
        }
        $metrics = $this->_data->metrics();
        $this->assertTrue(is_array($metrics));
        $this->assertArrayHasKey('changesets', $metrics);
        $this->assertArrayHasKey('numbers', $metrics['changesets']);
        $this->assertArrayHasKey('events', $metrics['changesets']);
        $this->assertArrayHasKey('mean', $metrics['changesets']);
        $this->assertArrayHasKey('deviation', $metrics['changesets']);
        $this->assertArrayHasKey('variance', $metrics['changesets']);
        $this->assertArrayHasKey('authors', $metrics);
        $this->assertArrayHasKey('numbers', $metrics['authors']);
        $this->assertArrayHasKey('events', $metrics['authors']);
        $this->assertArrayHasKey('mean', $metrics['authors']);
        $this->assertArrayHasKey('deviation', $metrics['authors']);
        $this->assertArrayHasKey('variance', $metrics['authors']);
        var_dump($metrics);
    }
}
