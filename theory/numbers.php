<?php
$files = include('files.php');
usort($files, function($a, $b) { return $a['c'] < $b['c']; });
$changes = max(array_map(function($f) { return $f['c']; }, $files));
$nums = array();
foreach ($files as $i=>$f) {
    $nums[(string) ($i/count($files))] = $f['c'] / $changes;
}
$sum = 0;
foreach ($nums as $x=>$c) {
    $sum += $x * $c;
}
$mu = $sum / array_sum($nums);
$sum = 0;
foreach ($nums as $x=>$c) {
    $sum += abs($mu - $x) * $c;
}
$var = $sum / array_sum($nums);
?>
%
\begin{eqnarray}
\mu & \approx & <?=sprintf('%0.3f', $mu)?> \\
Var(X) & \approx & <?=sprintf('%0.4f', $var)?>
\end{eqnarray}

