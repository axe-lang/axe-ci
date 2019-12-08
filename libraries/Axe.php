
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Axe {
  /**
   * [private description]
   * @var [type]
   */
  private $buffer             = "";
  /**
   * [private description]
   * @var [type]
   */
  private $raw                = "";
  /**
   * [private description]
   * @var [type]
   */
  private $output             = [];
  /**
   * [private description]
   * @var [type]
   */
  private $check_fail_silent;
  /**
   * [private description]
   * @var [type]
   */
  private $verify_fail_silent;
  /**
   * [private description]
   * @var [type]
   */
  private $carve_fail_silent;

  function __construct($params=null) {
    if ($params != null) {
      $this->check_fail_silent = $params['check_fail_silent'] ?? false;
      $this->verify_fail_silent = $params['verify_fail_silent'] ?? false;
      $this->carve_fail_silent = $params['carve_fail_silent'] ?? false;
    }
  }
  /**
   * [run description]
   * @date   2019-07-05
   * @param  string    $path        AXE Script File Path
   * @param  array     $raw_data    [description]
   * @param  array     $extractions [description]
   * @throws Exception
   * @return int       Exit Code.
   */
  function run(string $path, string $raw_data, &$extractions=[]):int
  {
    $this->buffer = $this->raw = $raw_data;
    $script = file_get_contents($path);
    $script = preg_replace("/^(#( )*[\w \d]+)/m", "{@@@@}", $script); // Mark Comments.
    $script = preg_split("/(\r\n|\n|\r)/", $script); // Prepare to Split.
    $line_number = 1;
    foreach($script as $line) {
      if ($line == "" || $line == "{@@@@}") {++$line_number; continue;}
      preg_match("/[\w]+/", $line, $command);
      if (method_exists($this, $command[0])) {
        if (!call_user_func([$this, strtolower($command[0])], $this->get_expression($command[0], $line))) {
          switch (strtolower($command[0])) {
            case 'check':
              if ($this->check_fail_silent) break;
              throw new Exception("CHECK FAILED on Line $line_number: '$line'");
            case 'verify':
              if ($this->verify_fail_silent) break;
              throw new Exception("VERIFY FAILED on Line $line_number: '$line'");
            case 'carve':
              if ($this->carve_fail_silent) break;
              throw new Exception("CARVE FAILED on Line $line_number: '$line'");
            default:
              throw new Exception("Unknown Function Call on Line $line_number: '$line'");
          }
        }
      } else {
        throw new Exception("Syntax Error at Line: $line_number, Unknown Function Call: $line");
      }
      ++$line_number;
    }
    $extractions = $this->output;
    $this->raw = "";
    $this->buffer = "";
    return 0;
  }
  /**
   * [get_expression description]
   * @param  [type] $function [description]
   * @param  [type] $line     [description]
   * @return [type]           [description]
   */
  private function get_expression($function, $line)
  {
    $exp = preg_replace("/$function\(\"/", "", $line);
    $exp = trim($exp);
    return preg_replace("/(\"\))$/", "", $exp);
  }
  /**
   * [check description]
   * @param  [type] $exp [description]
   * @return [type]      [description]
   */
  private function check($exp)
  {
    preg_match("/$exp/", $this->buffer, $match);
    return count($match) > 0;
  }
  /**
   * [verify description]
   * @date   2019-07-05
   * @param  [type]     $exp [description]
   * @return bool            [description]
   */
  private function verify($exp):bool
  {
    preg_match("/$exp/", $this->buffer, $match);
    return count($match) > 0 && strlen($match[0]) == strlen($this->buffer);
  }
  /**
   * [carve description]
   * @param  [type] $exp [description]
   * @return [type]      [description]
   */
  private function carve($exp)
  {
    preg_match("/$exp/", $this->buffer, $match);

    if ($match != null && count($match) > 0) {
      $this->buffer = $match[0];
      return true;
    }

    return false;
  }
  /**
   * [axe description]
   * @param  [type] $exp [description]
   * @return [type]      [description]
   */
  private function axe($exp)
  {
    $this->buffer = preg_replace("/$exp/", "", $this->buffer);
    return true;
  }
  /**
   * [put description]
   * @date   2019-07-05
   * @param  [type] $exp [description]
   * @return [type]      [description]
   */
  private function pack($exp):bool
  {
    $this->output[$exp] = $this->buffer;
    $this->buffer = $this->raw;
    return true;
  }
}
?>
