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
     * Width.
     * @var int
     */
    private $_width = 8;
    /**
     * Height.
     * @var int
     */
    private $_height = 5;
    /**
     * Plots to draw.
     * @var array
     */
    private $_plots;
    /**
     * Max X.
     * @var float
     */
    private $_maxX;
    /**
     * Max Y.
     * @var float
     */
    private $_maxY;
    /**
     * Min X.
     * @var float
     */
    private $_minX;
    /**
     * Min Y.
     * @var float
     */
    private $_minY;
    /**
     * Public ctor.
     * @param array $repos Array of repositories
     * @param function $xf Callback for X value
     * @param function $yf Callback for Y value
     * @param function $stylef Callback for style
     */
    public function __construct(array $repos, $xf, $yf, $stylef)
    {
        $this->_maxX = 0;
        $this->_minX = 100000;
        $this->_maxY = 0;
        $this->_minY = 100000;
        $this->_plots = array();
        foreach ($repos as $id => $repo) {
            $x = call_user_func($xf, $repo);
            $y = call_user_func($yf, $repo);
            $this->_plots[] = array(
                'x' => $x,
                'y' => $y,
                'style' => call_user_func($stylef, $repo),
                'name' => $id,
            );
            $this->_maxX = max($x, $this->_maxX);
            $this->_minX = min($x, $this->_minX);
            $this->_maxY = max($y, $this->_maxY);
            $this->_minY = min($y, $this->_minY);
        }
        foreach ($this->_plots as &$plot) {
            $plot['x'] = $this->_width * ($plot['x'] - $this->_minX);
            if ($this->_maxX - $this->_minX != 0) {
                $plot['x'] /= $this->_maxX - $this->_minX;
            }
            $plot['y'] = $this->_height * ($plot['y'] - $this->_minY);
            if ($this->_maxY - $this->_minY != 0) {
                $plot['y'] /= $this->_maxY - $this->_minY;
            }
        }
    }
    /**
     * Build TikZ graph (in LaTeX).
     * @return string TeX code
     * @see http://faculty.cs.niu.edu/~hutchins/csci230/best-fit.htm
     */
    public function tikz()
    {
        $SumX = 0;
        $SumY = 0;
        $SumX2 = 0;
        $SumXY = 0;
        foreach ($this->_plots as $plot) {
            $SumX += $plot['x'];
            $SumY += $plot['y'];
            $SumX2 += $plot['x'] * $plot['x'];
            $SumXY += $plot['x'] * $plot['y'];
        }
        $XMean = $SumX / count($this->_plots);
        $YMean = $SumY / count($this->_plots);
        $Slope = sprintf('%.4f', ($SumXY - $SumX * $YMean) / ($SumX2 - $SumX * $XMean));
        $YInt = sprintf('%.4f', $YMean - $Slope * $XMean);
        $tex = "\\begin{tikzpicture}\n"
            . "\\draw[help lines] (0,0) grid"
            . " ({$this->_width},{$this->_height});\n"
            . "\\draw[domain=0:{$this->_width},color=gray,ultra thick]"
            . " plot (\x,{ ${Slope} * \x + ${YInt} });\n"
            . "\\node[anchor=east] at (0,0) {{$this->_minY}};\n"
            . "\\node[anchor=east] at (0,{$this->_height}) {{$this->_maxY}};\n"
            . "\\node[anchor=north] at (0,-0.2) {{$this->_minX}};\n"
            . "\\node[anchor=north] at ({$this->_width},-0.2)"
            . " {{$this->_maxX}};\n";
        foreach ($this->_plots as $plot) {
            $tex .= "\\node[{$plot['style']}]"
                . " at ({$plot['x']},{$plot['y']}) {{$plot['name']}};\n";
        }
        $tex .= "\\end{tikzpicture}\n";
        return $tex;
    }
}
