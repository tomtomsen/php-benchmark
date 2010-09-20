
 <?php echo $this->benchmark_title; ?>

 <?php echo str_pad('', strlen($this->benchmark_title), '='); ?>

 <?php if ( $this->benchmark_description ) : echo $this->benchmark_description; endif; ?>


 Iterations: <?php echo $this->benchmark_iterations ?>

 PHP Version: <?php echo $this->php_version; ?>


