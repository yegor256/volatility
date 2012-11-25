<?php
$files = include('files.php');
usort(
    $files,
    function($a, $b) {
        return $a['c'] < $b['c'];
    }
);
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
    \path [draw, ->] (0,0)
        node[below left] {0}
        --
        (<?php echo count($files)/2 + 1?>,0)
        node[right] {$x$};
    \path [draw, ->] (0,0)
        --
        (0,<?php echo $changes/2 + 1?>)
        node[above] {$p(x)$};
    \draw[mark=*] plot coordinates {
        <?php foreach ($files as $i=>$f): ?>
            (<?php echo $i/2?>, <?php echo $f['c']/2?>)
        <?php endforeach ?>
    };
    \path [draw] (<?php echo (count($files) - 1)/2?>, 0.25)
        -- +(0,-0.5) node [below] {1};
    \path [draw] (0.25, <?php echo $changes/2?>) -- +(-0.5,0) node [left] {1};
    \node [anchor=north east, align=right] at (
        <?php echo count($files)/2?>,
        <?php echo $changes/2?>
    )
        {$0 \leq x \leq 1$\\$0 \leq p(x) \leq 1$};
\end{tikzpicture}
