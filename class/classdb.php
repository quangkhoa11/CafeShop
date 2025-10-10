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
            $sql = "select idkh from khachhang where sdt='$tk' and matkhau='$mk'";
            $result = $link->query($sql);
            if ($result->num_rows) {
                $row = $result->fetch_assoc();
                return reset($row);
            }
            return 0;
        }
        

    }

?>

