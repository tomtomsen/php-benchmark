 Results:
 
<?php if ( $this->benchmark_targets != null && count($this->benchmark_targets) > 0 ) : ?>
<?php $keys = array_keys($this->benchmark_targets); ?>
<?php $fastest_time = $this->times[$keys[0]]; ?>
     Factor | Description                                        | Time requi | Memory leaked
    --------+----------------------------------------------------+------------+-----------------<?php for($c = count($this->benchmark_targets),$i = 0; $i < $c; $i++) : ?>
<?php   $factor = number_format(($this->times[$keys[$i]]/$fastest_time), 1); ?>
<?php   $description = $this->benchmark_targets[$keys[$i]]->getDescription(); ?>
<?php   $description_lines = explode("\n", wordwrap(strip_tags($description), 50, "\n", true)); ?>
<?php   $time_string = Helper::convertSecondsToTimeFormat($this->times[$keys[$i]]/$this->benchmark_iterations); ?>
<?php   $memory_leaked = $this->memory[$keys[$i]] . ' byte' . ($this->memory[$keys[$i]] != 1 ? 's' : ''); ?>
<?php echo sprintf("\n" . '  %8sx | %-50s | %-10s | %s', $factor, $description_lines[0], $time_string, $memory_leaked); ?>
<?php for($cd = count($description_lines),$id=1;$id<$cd;$id++) : ?>
<?php echo sprintf("\n" . '   %8s | %-50s | %-10s | %s' . "", ' ', $description_lines[$id], ' ', ' ');?>
<?php endfor; ?>
<?php /* echo strip_tags($this->benchmark_targets[$keys[$i]]->getDescription()); ?> | <?php echo Helper::convertSecondsToTimeFormat($this->times[$keys[$i]]/$this->benchmark_iterations); ?> | <?php echo $this->memory[$keys[$i]]; ?> byte<?php if ($this->memory[$keys[$i]] != 1) : ?>s<?php endif;*/ ?>
<?php endfor; ?>

    --------+----------------------------------------------------+------------+-----------------
                <?php else: ?>
  >> No targets found
                <?php endif; ?>

 ----------------------------------------------------------------------
  php benchmark framework written by tom tomsen <tom.tomsen@inbox.com>
 ----------------------------------------------------------------------

