<?php

namespace logger;

/**
 * Description of Logger
 * Class to log the proccess in hte logs folder in the project folder
 * @author eren
 */

class Logger {

    private $path = '/logs/';
    private $filePath;
    private $fileName;
    private $fp;
    
    /*
     * Constructor sets the file name and time zone
     */
    
    public function __construct( $fileName ) {
        
        date_default_timezone_set('America/Los_Angeles');
        
        $this->fileName = date("Ymd") . "_" . trim($fileName) . ".log";
             
    }
    
    /*
     * Logging messages
     * 
     * @param string $msg message to log
     */
    
    public function log( $msg ) {

        $this->filePath = dirname(__FILE__) . "/../logs/" . $this->fileName;

        if (isset($this->fp) === false) {

            $this->fp = fopen($this->filePath, "a");
        }

        if ($this->fp) {

            if (fwrite($this->fp, "[" . date("H:i:s") . "] -> " . $msg . "\n") !== false) {

                return true;
            }
        }

        return false;
    }
    
    /*
     * Closing file stream
     * 
     * @return boolean
     */
    public function close() {

        if ( fclose( $this->fp ) ) {
            
            return true;
        }
        
        return false;
    }

}
