<?
include_once(CORE_ROOT.'/classDataBase.php');
include_once(CORE_ROOT.'/classToken.php');
include_once(CORE_ROOT.'/classFiles.php');

class myRoutes{
    private $url;
    private $conDB;
    private $objToken;
    private $objFiles;
    private $userIP;
    private $method;
    private $activeUserToken;
    public function __construct(){
        $this->url = str_replace("/","",parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->conDB = new DataBase(DB_HOST,DB_NAME,DB_USER,DB_PASS);
        $this->objToken = new Token();
        $this->userIP = $_SERVER['REMOTE_ADDR'];
        $headers = getallheaders();
        if(isset($headers['Authorization'])){
            $this->activeUserToken=$headers['Authorization'];
        }
        
    }

    public function actionRoute(){
        header("Content-Type: application/json");
        switch($this->method){
            case "GET":{
                switch($this->url){
                    case "getToken":{
                        return $this->tokenRouteGet();
                    }
                    case "userFiles":{
                        return $this->allFilesRouteGet();
                    }
                    default:{
                        if(strlen($this->url)!=32) return $this->notFound();
                        return $this->fileShortUrlRouteDown($this->url);
                    }
                }
            }
            case "POST":{
                if($this->url==="file"){
                    return $this->fileUploadRoute();
                }
                return $this->notFound();
            }
            case "PUT":{
                if($this->url==="getToken"){
                    return $this->tokenRouteUpdate();
                }
                return $this->notFound();
            }
            case "DELETE":{
                if($this->url==="userFiles"){
                    return $this->allFilesRouteDel();
                }
                if(strlen($this->url)!=32) return $this->notFound();
                return $this->fileShortUrlRouteDel($this->url);
            }
            default:{
                return '{"responce":{"message":"this method('.$this->method.') is not valid for this API"}}';
            }
        }
    }

    private function tokenRouteGet(){//генерим токен для нового юзера,пишем в базу и отдаём токен
        $resp = $this->objToken->generateToken($this->userIP);
        $check = $this->conDB->checkUser($resp['secret']);
        if($check){
            return '{"responce":{"token":"'.$check->token.'"}}';//возвращаем токен из базы если уже есть юзер
        }
        $this->conDB->insertUserToken($resp['token'],$resp['secret']);//добавляем токен и ключ в базу
        return '{"responce":{"token":"'.$resp['token'].'"}}';
    }

    private function tokenRouteUpdate(){//генерим новый токен для юзера с токеном(если в headers был токен в поле Authorization),апдейтим в базе и отдаём новый токен
        if(!$this->activeUserToken) return '{"response":{"message":"in headers not specified token"}}';
        $resp = $this->objToken->generateToken($this->userIP);
        $infoUser = $this->conDB->selectInfoUserToken($this->activeUserToken,$resp['secret']);
        if(!$infoUser) return '{"response":{"message":"Perhaps this is not your token. Get a new token for further actions."}}';
        $this->conDB->updateToken($infoUser->id,$resp['token']);
        return '{"responce":{"oldToken":"'.$this->activeUserToken.'","newToken":"'.$resp['token'].'"}}';
    }

    private function fileUploadRoute(){//загрузка файла(если в headers был токен в поле Authorization)
        if(!$this->activeUserToken) return '{"response":{"message":"in headers not specified token"}}';
        $resp = $this->objToken->generateToken($this->userIP);
        $infoUser = $this->conDB->selectInfoUserToken($this->activeUserToken,$resp['secret']);
        if(!$infoUser) return '{"response":{"message":"Perhaps this is not your token. Get a new token for further actions."}}';
        $file=new Files();
        $resArr=$file->saveFile();
        if(is_array($resArr)){
            $this->conDB->insertUserFile($infoUser->id, $resArr['name'], $resArr['hashName'],$resArr['type']);
            return '{"responce":{"shortUrl":"'.$_SERVER['SERVER_NAME'].'/'.$resArr['hashName'].'"}}';
        }else{
            return $resArr;
        }
    }

    private function allFilesRouteGet(){//возвращаем список всех файлов юзера,если в headers был токен в поле Authorization
        if(!$this->activeUserToken) return '{"response":{"message":"in headers not specified token"}}';
        $resp = $this->objToken->generateToken($this->userIP);
        $infoUser = $this->conDB->selectInfoUserToken($this->activeUserToken,$resp['secret']);
        if(!$infoUser) return '{"response":{"message":"Perhaps this is not your token. Get a new token for further actions."}}';
        $resArrDB = $this->conDB->selectInfoAllFilesUser($infoUser->id);
        if(!$resArrDB) return '{"response":{"message":"no files"}}';
        $resArr=array();
        $nameServ=$_SERVER['SERVER_NAME'];
        foreach($resArrDB as $key=>$objFile){
            $buff = array("name"=>$objFile->file_name,"url"=>$nameServ.'/'.$objFile->for_short_url);
            array_push($resArr, $buff);
        }
        $response=json_encode($resArr, JSON_FORCE_OBJECT);
        return '{"files":'.$response.'}';
    }

    private function allFilesRouteDel(){//удаляем все файлы юзера(если в headers был токен в поле Authorization)
        if(!$this->activeUserToken) return '{"response":{"message":"in headers not specified token"}}';
        $resp = $this->objToken->generateToken($this->userIP);
        $infoUser = $this->conDB->selectInfoUserToken($this->activeUserToken,$resp['secret']);
        if(!$infoUser) return '{"response":{"message":"Perhaps this is not your token. Get a new token for further actions."}}';
        $resArrDB = $this->conDB->selectInfoAllFilesUser($infoUser->id);
        if(!$resArrDB) return '{"response":{"message":"no files"}}';
        $resArr=array();
        foreach($resArrDB as $key=>$objFile){
            array_push($resArr, $objFile->for_short_url.'.'.$objFile->type);
        }
        $file=new Files();
        $file->deleteFiles($resArr);
        $resultDel = $this->conDB->deleteAllFilesUser($infoUser->id);
        return '{"response":{"message":"files delete complited"}}';
    }

    private function fileShortUrlRouteDown(string $shortUrl){//отдаём на скачивание файл по shortUrl(если в headers был токен в поле Authorization)
        if(!$this->activeUserToken) return '{"response":{"message":"in headers not specified token"}}';
        $resp = $this->objToken->generateToken($this->userIP);
        $infoUser = $this->conDB->selectInfoUserToken($this->activeUserToken,$resp['secret']);
        if(!$infoUser) return '{"response":{"message":"Perhaps this is not your token. Get a new token for further actions."}}';
        $infoFile = $this->conDB->oneFileInfo($infoUser->id,$shortUrl);
        if(!$infoFile) return '{"response":{"message":"no file"}}';
        $file=new Files();
        $this->conDB->countDownloadAdd($infoFile->id);
        return $file->fileForceDownload($infoFile->for_short_url.'.'.$infoFile->type,$infoFile->file_name);
    }

    private function fileShortUrlRouteDel(string $shortUrl){//удаляем файл по shortUrl(если в headers был токен в поле Authorization)
        if(!$this->activeUserToken) return '{"response":{"message":"in headers not specified token"}}';
        $resp = $this->objToken->generateToken($this->userIP);
        $infoUser = $this->conDB->selectInfoUserToken($this->activeUserToken,$resp['secret']);
        if(!$infoUser) return '{"response":{"message":"Perhaps this is not your token. Get a new token for further actions."}}';
        $infoFile = $this->conDB->oneFileInfo($infoUser->id,$shortUrl);
        if(!$infoFile) return '{"response":{"message":"no file"}}';
        $file=new Files();
        $fullFileName=$shortUrl.'.'.$infoFile->type;
        $file->deleteFiles($fullFileName);
        $resDBdel = $this->conDB->deleteOneFile($infoUser->id,$shortUrl);
        if($resDBdel>0) return '{"response":{"message":"success"}}';
        return '{"response":{"message":"no files with this URL"}}';
    }

    private function notFound(){
        return '{"responce":{"status":404, "message":"URL Not Found for this method('.$this->method.')"}}';
    }
}
?>