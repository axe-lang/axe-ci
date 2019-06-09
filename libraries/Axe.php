<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Axe {

  private $buffer = "";

  private $raw;

  private $output = [];

  function __construct() {

  }
  /**
   * [run description]
   * @param  [type] $path        [description]
   * @param  [type] $raw_data    [description]
   * @param  array  $extractions [description]
   * @return [type]              [description]
   */
  function run($path, $raw_data, &$extractions=[]) {
    $this->buffer = $this->raw = $raw_data;
    $script = file_get_contents($path);
    $script = preg_replace("/#( )*[\w ]+/", "{@@@@}", $script); // Mark Comments.
    $script = preg_split("/(\r\n|\n|\r)/", $script); // Prepare to Split.
    $line_number = 1;
    foreach($script as $line) {
      if ($line == "" || $line == "{@@@@}") {++$line_number; continue;}
      preg_match("/[\w]+/", $line, $command);
      if (method_exists($this, $command[0])) {
        call_user_func([$this, strtolower($command[0])], $this->get_expression($command[0], $line));
      } else {
        throw new Exception("Syntax Error at Line: $line_number");
      }
      ++$line_number;
    }
    $extractions = $this->output;
  }
  /**
   * [get_expression description]
   * @param  [type] $function [description]
   * @param  [type] $line     [description]
   * @return [type]           [description]
   */
  private function get_expression($function, $line) {
    $exp = preg_replace("/$function\(\"/", "", $line);
    $exp = trim($exp);
    return preg_replace("/(\"\))$/", "", $exp);
  }
  /**
   * [carve description]
   * @param  [type] $exp [description]
   * @return [type]      [description]
   */
  private function carve($exp) {
    preg_match("/$exp/", $this->buffer, $match);
    $this->buffer = $match[0];
  }
  /**
   * [axe description]
   * @param  [type] $exp [description]
   * @return [type]      [description]
   */
  private function axe($exp) {
    $this->buffer = preg_replace("/$exp/", "", $this->buffer);
  }
  /**
   * [put description]
   * @param  [type] $exp [description]
   * @return [type]      [description]
   */
  private function pack($exp) {
    $this->output[$exp] = $this->buffer;
    $this->buffer = $this->raw;
  }
}
?>
