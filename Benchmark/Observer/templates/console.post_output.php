<?php
if (!function_exists('convertSecondsToTimeFormat')) {

    function convertSecondsToTimeFormat($seconds)
    {
        if (!is_numeric($seconds)) {
            return false;
        }

        $seconds = doubleval($seconds);
        if ($seconds < 1) {
            $ms = $seconds * 1000;
            if ($ms < 1) {
                $ys = $ms * 1000;
                return number_format($ys, 0, ',', '.') . ' ys';
            }

            return number_format($ms, 1, ',', '.') . ' ms';
        }

        return number_format($seconds, 1, ',', '.') . ' s';
    }
}

if (!function_exists('center_text')) {

    function center_text($text, $line_width)
    {
        $exact_space_count = ($line_width - strlen($text)) / 2;
        $pre_space_count = $post_space_count = round($exact_space_count);
        if (round($exact_space_count) != floor($exact_space_count)) {
            $post_space_count -= 1;
        }

        return sprintf('%' . $pre_space_count . '.s%s%' . $post_space_count . '.s', ' ', $text, ' ');
    }

}
?>
 Results:

<?php if ($this->benchmark_targets != null && count($this->benchmark_targets) > 0) : ?>
<?php $keys = array_keys($this->benchmark_targets); ?>
<?php $fastest_time = $this->times[$keys[0]]; ?>
     Factor | Description                                        | Time requi | Memory leaked
    --------+----------------------------------------------------+------------+-----------------<?php for ($c = count($this->benchmark_targets), $i = 0; $i < $c; $i++) : ?>
<?php $factor = number_format(($this->times[$keys[$i]] / $fastest_time), 1); ?>
<?php $description = $this->benchmark_targets[$keys[$i]]->getDescription(); ?>
<?php $description_lines = explode("\n", wordwrap(strip_tags($description), 50, "\n", true)); ?>
<?php $time_string = convertSecondsToTimeFormat($this->times[$keys[$i]] / $this->benchmark_iterations); ?>
<?php $memory_leaked = $this->memory[$keys[$i]] . ' byte' . ($this->memory[$keys[$i]] != 1 ? 's' : ''); ?>
<?php echo sprintf("\n" . '  %8sx | %-50s | %-10s | %s', $factor, $description_lines[0], $time_string, $memory_leaked); ?>
<?php for ($cd = count($description_lines), $id = 1; $id < $cd; $id++) : ?>
<?php echo sprintf("\n" . '   %8s | %-50s | %-10s | %s' . "", ' ', $description_lines[$id], ' ', ' '); ?>
<?php endfor; ?>
<?php endfor; ?>

    --------+----------------------------------------------------+------------+-----------------
<?php else: ?>
                >> No targets found
<?php endif; ?>

 +----------------------------------------------------------------------+
 | php benchmark framework written by tom tomsen <tom.tomsen@inbox.com> |
 |                                                                      |
<?php echo sprintf(" | %s |", center_text('version: ' . Version::id(), (73 - 5))); ?>

 +----------------------------------------------------------------------+

