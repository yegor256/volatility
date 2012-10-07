<?php
$files = include('files.php');
usort($files, function($a, $b) { return $a['c'] < $b['c']; });
$changes = max(array_map(function($f) { return $f['c']; }, $files));
?>
%
\begin{tikzpicture}
    \path [draw, ->] (0,0)
        node[below left] {0}
        --
        (<?=count($files)/2 + 1?>,0)
        node[right] {$X_i$};
    \path [draw, ->] (0,0)
        --
        (0,<?=$changes/2 + 1?>)
        node[above] {$p(X_i)$};
    \draw[mark=*] plot coordinates {
        <? foreach ($files as $i=>$f): ?>
            (<?=$i/2?>, <?=$f['c']/2?>)
        <? endforeach ?>
    };
    \path [draw] (<?=(count($files) - 1)/2?>, 0.25) -- +(0,-0.5) node [below] {1};
    \path [draw] (0.25, <?=$changes/2?>) -- +(-0.5,0) node [left] {1};
    \node [anchor=north east, align=right] at (<?=count($files)/2?>,<?=$changes/2?>)
        {$0 \leq X_i \leq 1$\\$0 \leq p(X_i) \leq 1$};
\end{tikzpicture}
