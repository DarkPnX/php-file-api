<?
//не правильная логика.Распределить задачи по классам и вызыватьв роуте по порядку!
class Token{
    private $secret;
    private $openKey;
    //private $userIP;
    private $date;
    //private $userToken;
    private $delimiter='-';
    //private $conectDB;
    public function __construct(){
        $this->date=time();
        //$this->userIP=$_SERVER['REMOTE_ADDR'];
        //$this->secret=md5($this->userIP);
        //$this->conectDB = $conDB;
        //if(isset($_SERVER['PHP_AUTH_DIGEST'])){
            //$this->userToken=$_SERVER['PHP_AUTH_DIGEST'];
        //}
    }
    public function generateToken(string $userIP){
        $buffKey=base64_encode($this->date.$this->delimiter.$userIP);
        $this->secret = md5($userIP);
        $this->openKey=base64_encode($buffKey.$this->delimiter.$this->secret);
        //$resDB = $this->conectDB->insertUserToken($this->openKey);
        //if(!$resDB) return 'у вас уже есть токен';
        $resArr = array("secret"=>$this->secret,"token"=>$this->openKey,"userIP"=>$userIP);
        return $resArr;
    }

    public function updateToken(string $userIP){
        return $this->generateToken($userIP);
    }
    /*public function validToken(){
        if(empty($this->userToken)){
            return 'not found user token in headers';
        }
        $conectDB=new DataBase();
        $selectResult=$conectDB->selectUser($this->userToken);
        if($selectResult!==false){
            $dbTokenDecode=base64_decode($selectResult);
            $buffArrKey=explode(":", $dbTokenDecode);
            $buffSecret=md5($this->userIP);
            return ($buffSecret===$buffArrKey[1])?true:false;
        }else{
            return false;
        }
    }*/
}
?>