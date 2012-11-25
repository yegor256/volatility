<?php
$files = include('files.php');
$changes = max(
    array_map(
        function($f) {
            return $f['c'];
        },
        $files
    )
);
?>
%
\begin{tikzpicture}
    \draw [help lines] (0,0) grid (
        <?php echo count($files)/2?>,
        <?php echo ($changes+1)/2?>
    );
    \path [draw, -|] (0,0)
        node[below left] {0}
        --
        node[midway, below] {source code files}
        (<?php echo count($files)/2?>,0)
        node[right] {$i$};
    \path [draw, -|] (0,0)
        --
        node[midway, above, rotate=90] {changes per file}
        (0,<?php echo ($changes+1)/2?>)
        node[above] {$c(f_i)$};
    \draw[ycomb, line width=0.25cm, xshift=1pt] plot coordinates {
        <?php foreach ($files as $i=>$f): ?>
            (<?php echo $i/2?>, <?php echo $f['c']/2?>)
        <?php endforeach ?>
    };
\end{tikzpicture}
