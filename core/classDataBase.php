<?
class DataBase{
    private $dbh;
    public function __construct(string $host, string $name, string $user, string $pass){
        if(!isset($this->dbh)){
          // Connect to the database
          try{
            $conn = new PDO("mysql:host=".$host.";dbname=".$name, $user, $pass);
            $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbh = $conn;
          }catch(PDOException $e){
            die("Failed to connect with MySQL: " . $e->getMessage());
          }
        }
    }

    public function insertUserToken(string $token, string $secret){
        $resSelect = $this->selectInfoUserToken($token,$secret);
        if($resSelect) return false;
        $sql = 'INSERT INTO token_users SET token="'.$token.'",secret="'.$secret.'"';
        $row = $this->dbh->query($sql);
    }

    public function insertUserFile(string $id, string $fileName, string $hashName,string $type){
        $sql = 'INSERT INTO files_users SET id_user='.$id.',file_name="'.$fileName.'",for_short_url="'.$hashName.'",type="'.$type.'",count_down=0';
        $row = $this->dbh->query($sql);
    }

    public function countDownloadAdd(string $idFile){
        $sql = 'UPDATE files_users SET count_down = count_down + 1 WHERE id='.$idFile;
        $row = $this->dbh->query($sql);
    }

    public function deleteOneFile(string $idUser,string $hashName){
        $sql = 'DELETE FROM files_users WHERE (id_user='.$idUser.' AND for_short_url="'.$hashName.'")';
        $row = $this->dbh->query($sql);
        $count = $row->rowCount();
        return $count;
    }

    public function deleteAllFilesUser(string $idUser){
        $sql = 'DELETE FROM files_users WHERE (id_user='.$idUser.')';
        $row = $this->dbh->query($sql);
        $count = $row->rowCount();
        return $count;
    }

    public function oneFileInfo(string $idUser,string $hashName){
        $sql = 'SELECT * FROM files_users WHERE (id_user='.$idUser.' AND for_short_url="'.$hashName.'")';
        $query = $this->dbh->query($sql);
        $query->execute();
        while ($row = $query->fetchObject()) {
            $data[] = $row;
        }
        return !empty($data)?$data[0]:false;
    }

    public function selectInfoUserToken(string $token,string $secret){
        $sql = 'SELECT * FROM `token_users` WHERE (`token`="'.$token.'"AND `secret`="'.$secret.'") LIMIT 1';
        $query = $this->dbh->query($sql);
        $query->execute();
        while ($row = $query->fetchObject()) {
            $data[] = $row;
        }
        return !empty($data)?$data[0]:false;
    }

    public function selectInfoAllFilesUser(string $idUser){
        $sql = 'SELECT * FROM files_users WHERE (id_user='.$idUser.')';
        $query = $this->dbh->query($sql);
        $query->execute();
        while ($row = $query->fetchObject()) {
            $data[] = $row;
        }
        return !empty($data)?$data:false;
    }

    public function checkUser(string $secret){
        $sql = 'SELECT `id`, `token`, `secret` FROM `token_users` WHERE (`secret`="'.$secret.'")';
        $query = $this->dbh->query($sql);
        $query->execute();
        while ($row = $query->fetchObject()) {
            $data[] = $row;
        }
        return !empty($data)?$data[0]:false;
    }
    
    public function updateToken($idUser,string $token){
        $sql = 'UPDATE `token_users` SET `token`="'.$token.'" WHERE (`id`='.$idUser.')';
        $row = $this->dbh->query($sql);
    }

}
?>