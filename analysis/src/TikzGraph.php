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
final class TikzGraph
{
    /**
     * Repositories.
     * @var array
     */
    private $_repos;
    /**
     * X-function.
     * @var function
     */
    private $_xf;
    /**
     * Y-function.
     * @var function
     */
    private $_yf;
    /**
     * Style-function.
     * @var function
     */
    private $_stylef;
    /**
     * Public ctor.
     * @param array $repos Array of repositories
     * @param function $xf Callback for X value
     * @param function $yf Callback for Y value
     * @param function $stylef Callback for style
     */
    public function __construct(array $repos, $xf, $yf, $stylef)
    {
        $this->_repos = $repos;
        $this->_xf = $xf;
        $this->_yf = $yf;
        $this->_stylef = $stylef;
    }
    /**
     * Build TikZ graph (in LaTeX).
     * @return string TeX code
     */
    public function tikz()
    {
        $maxX = 0;
        $minX = 100000;
        $maxY = 0;
        $minY = 100000;
        foreach ($this->_repos as $repo) {
            $x = call_user_func($this->_xf, $repo);
            $y = call_user_func($this->_yf, $repo);
            $maxX = max($x, $maxX);
            $minX = min($x, $minX);
            $maxY = max($y, $maxY);
            $minY = min($y, $minY);
        }
        $width = 8;
        $height = 5;
        $tex = "\\begin{tikzpicture}\n";
        $tex .= "\\draw [help lines] (0,0) grid ({$width},{$height});\n";
        $tex .= "\\node[anchor=east] at (0,0) {" . $minY . "};\n";
        $tex .= "\\node[anchor=east] at (0,{$height}) {" . $maxY . "};\n";
        $tex .= "\\node[anchor=north] at (0,-0.2) {" . $minX . "};\n";
        $tex .= "\\node[anchor=north] at ({$width},-0.2) {" . $maxX . "};\n";
        foreach ($this->_repos as $id => $repo) {
            $x = call_user_func($this->_xf, $repo);
            $y = call_user_func($this->_yf, $repo);
            $x = $width * ($x - $minX);
            if ($maxX - $minX != 0) {
                $x /= $maxX - $minX;
            }
            $y = $height * ($y - $minY);
            if ($maxY - $minY != 0) {
                $y /= $maxY - $minY;
            }
            $tex .= "\\node["
                . call_user_func($this->_stylef, $repo)
                . "] at ({$x},{$y}) {{$id}};\n";
        }
        $tex .= "\\end{tikzpicture}\n";
        return $tex;
    }
}
