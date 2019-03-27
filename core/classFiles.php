<?
class Files{
    private $origName;
    private $sizeFile;
    private $localName;
    private $error;
    private $dirSave;
    private $maxSizeFile;
    private $hashName;
    public function __construct(){
        $this->origName=$_FILES[INPUT_UPLOAD_FILE]['name'];
        $this->sizeFile=$_FILES[INPUT_UPLOAD_FILE]['size'];
        $this->localName=$_FILES[INPUT_UPLOAD_FILE]['tmp_name'];
        $this->error=$_FILES[INPUT_UPLOAD_FILE]['error'];
        $this->dirSave=UPLOAD_FILES_ROOT;
        $this->maxSizeFile=(integer)MAX_SIZE_FILE * 1;
        $this->hashName=md5(time().$this->origName);
        //$validPath = substr(UPLOAD_FILES_ROOT, -1)=='/'?;
        if(!file_exists(UPLOAD_FILES_ROOT)){
            mkdir(UPLOAD_FILES_ROOT);
        }
    }

    public function saveFile(){
        if($this->error) return $this->error;
        if($this->sizeFile>$this->maxSizeFile) return '{"response":{"message":"Oh!NO!Your file is very big for this api"}}';
        $info = new SplFileInfo($this->origName);
        $type = $info->getExtension();
        if($type=="jpg" || $type=="png" || $type=="svg" || $type=="txt"){
            $buffName = $this->hashName.'.'.$type;
            if(move_uploaded_file($this->localName, $this->dirSave. basename($buffName))){
                $buffArr=array('name'=>$this->origName,'hashName'=>$this->hashName,'serverName'=>$buffName,'type'=>$type);
                return $buffArr;
            }
        }else{
            return '{"response":{"message":"not valid format file"}}';
        }
    }

    public function fileForceDownload(string $file,string $origName) {
        $buffPath=$this->dirSave.$file;//'../public/uploadFiles/'
        if (file_exists($buffPath)) {
          if (ob_get_level()) {
            ob_end_clean();
          }
          //header('Content-Description: File Transfer');
          header("Content-Type: application/force-download");
          header("Content-Type: application/octet-stream");
          header("Content-Type: application/download");
          //header('Content-Disposition: attachment; filename=' . basename($buffPath));
          //header('Content-Transfer-Encoding: image/jpeg');
          header('Content-Transfer-Encoding: binary');
          //header('Expires: 0');
          //header('Cache-Control: must-revalidate');
          //header('Pragma: public');
          //header('Content-Length: ' . filesize($buffPath));
          //readfile($buffPath);
          
          if ($fd = fopen($buffPath, 'rb')) {
            while (!feof($fd)) {
              print fread($fd, 1024);
            }
            fclose($fd);
          }
        }
    }

    public function deleteFiles($files){
        if(is_string($files)){
            unlink(UPLOAD_FILES_ROOT.$files);
        }elseif(is_array($files)){
            foreach ($files as $file) {
                unlink(UPLOAD_FILES_ROOT.$file);
            }
        }
    }
}
?>