<?php
    class database{

        private function ketnoi(){
            $conn=new mysqli("localhost","root","","cafeshop");
            if($conn->connect_error){
                echo "Kết nối thất bại!";
                exit();
            }
            else{
                return $conn;
            }
        }
        public function xuatdulieu($sql){
            $link=$this->ketnoi();
            $arr=array();
            $result=$link->query($sql);
            if($result->num_rows){
                while($row=$result->fetch_assoc())
                $arr[]=$row;
            return $arr;
            }
            else{
                return 0;
            }
        }

        public function dangnhap($tk, $mk) {
        $link = $this->ketnoi();
        $sql = "SELECT * FROM khachhang WHERE email = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $tk);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($mk, $row['matkhau'])) {
                return $row;
            }
        }
        return 0;
    }


        public function themxoasua($sql) {
    $link = $this->ketnoi();
    if ($link->query($sql) === TRUE) {
        return true; 
    } else {
        echo "<p style='color:red;'>Lỗi SQL: " . $link->error . "</p>";
        return false;
    }
}

        

    }

?>

