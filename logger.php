<?php

class Logger{
  protected $db;
  protected $log_dir;
  protected $username;
  private $start_time = 0;
  private $id;

  public function __construct(EscapeDB $db, $log_dir, $username){
    $this->db = $db;
    $this->log_dir = $log_dir;
    $this->username = $username;
    $this->start_time = microtime(true);
    $this->id = time().'.'.rand(100,999);
  }

  public function warning($msg){
    error_log('warning: '.$msg);
    $this->dbLog('warning',$msg);
    $this->postAccessLog();
    exit();
  }

  public function error($msg){
    error_log('error: '.$msg);
    $this->dbLog('error',$msg);
    $this->postAccessLog();
    exit();
  }

  public function dbLog($type,$msg,$data=null){
    // try to log error in request_log table
    if($this->db){
      $username = $this->username ? $this->username : 'null';
      $q = "INSERT INTO request_log SET user_login = :user_login, user_id = NULL, type=:type, msg = :msg, data = :data";
      try{
        $this->db->exec($q, ['user_login'=>$username, 'type'=>$type, 'msg'=>$msg, 'data'=>$data]);
      }catch (Exception $e) {
        error_log($e->getMessage());
      }
    }
  }

  /**
   * Write log to file
   * First argument is filename
   * All other arguments are translated to strings, concatenated and written to filename
   */
  public function fileLog(){
    if(count(func_get_args()) < 2) return false;

    $msg = "";
    foreach(func_get_args() as $i => $arg){
      if($i==0) $filename = $arg;
      else{
        if(is_array($arg)) $msg .= var_export($arg, true);
        else $msg .= $arg;
        $msg .= " ";
      }
    }
    error_log($this->username." ".$this->id." ".$msg."\n", 3, $this->log_dir."/".$filename);
    return true;
  }

  /**
   * Log messages to error_log
   * All arguments are translated to strings and concatenated
   */
  public function log(){
    $msg = "";
    foreach(func_get_args() as $arg){
      if(is_array($arg)) $msg .= var_export($arg, true);
      else $msg .= $arg;
      $msg .= "\n";
    }
    $msg = $this->username." ".$this->id." ".$msg;
    error_log($msg);
    //$this->dbLog('log',$msg);
  }

  public function preAccessLog(){
    $ref = isset($_SERVER['HTTP_REFERER']) ?$_SERVER['HTTP_REFERER'] : null;
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
    $self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : null;
    $req_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
    $this->fileLog('pre_access.log', $this->id.';'.time().';'.date('Y-m-d H:i:s').';'.getmypid().';'.$ref.';'.$uri.';'.$self.';'.$req_method.';'.memory_get_usage());
  }

  public function postAccessLog(){
    $ref = isset($_SERVER['HTTP_REFERER']) ?$_SERVER['HTTP_REFERER'] : null;
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
    $self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : null;
    $req_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
    $this->fileLog('post_access.log',$this->id.';'.time().';'.date('Y-m-d H:i:s').';'.getmypid().';'.$ref.';'.$uri.';'.$self.';'.$req_method.';'.memory_get_usage().';'.(microtime(true) - $this->start_time).';'.$this->db->getTotalTimeInQuery());
  }
}