<?php
session_start();
ob_start();

include('./model/pdo.php');
include('./model/danhmuc.php');
include('./model/sanpham.php');
include('./model/taikhoan.php');
include('./model/giohang.php');
include('./userprofile/header1.php');
if(isset($_GET['act'])){
    $act = $_GET['act'];
    switch($act){
        case 'cart':
            include('cart.php');
            break;
        case 'search':
            if(isset($_GET['iddm'])){
                $iddm = $_GET['iddm'];
                
            }else{
                $iddm = 0;
            }
            if(isset($_POST['search'])){
                $kyws = $_POST['kyws'];
            }else{
                $kyws = '';
            }
            $search = search_danhmuc_kyws($iddm,$kyws);
            include('./userprofile/sanphamsearch.php');
            break;
        case 'sanphamct':
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $sanphamct = sanpham_chitiet($id);
                extract($sanphamct);
                $sanphamlienquan = sanpham_lienquan($id,$iddm);
            }
            include('./userprofile/sanphamct.php');
            break;
       
                
        case 'dangki':
            if(isset($_POST['dangki'])){
                $user = $_POST['user'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                 
                $target_dir ='./upload/';
                $rand = rand(1,100);
                $file_img = $_FILES['file_img']['name'];
                $target_file = $target_dir.'_'.$rand.'_'.$file_img;
                move_uploaded_file($_FILES['file_img']['tmp_name'],$target_file);
                insert_account($user,$email,$password,$target_file);
                $notify ="Bạn đã đăng kí tài khoản thành công <br> Vui lòng đăng nhập! ";
            }
            include('./userprofile/singup.php');
            break;
        case 'login':
            if(isset($_POST['login'])){
                $user = $_POST['user'];
                $password = $_POST['password'];
                $results = check_login($user,$password);
                if(is_array($results)){
                  var_dump($results);
                 $_SESSION['user'] = $results;
                   header('location:index.php');
                }else{
                $notify ="<h2 style='color:red;'>Tài Khoản Không Tồn Tại !</h2>";
                }
            }
            include('./userprofile/login.php');
            break;
        case 'profile':
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $user = user_profile($id);
            }
            include('./userprofile/profile.php');
            break;
        case 'updateacc':
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                $user = user_profile($id);
            }
            include('./userprofile/updateaccount.php');
            break;
        case 'updateaccount':
            if(isset($_POST['update'])){ 
               $id = $_POST['id'];
                $user = $_POST['user'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $add = $_POST['add'];
                $sdt = $_POST['sdt'];
                $target_file = $_POST['file_img'];
                $size = $_FILES['file_img']['size'];
                if($size > 0){
                    $target_dir='./upload/';
                    $file_img = $_FILES['file_img']['name'];
                    $rand = rand(1,100);
                    $target_file = $target_dir.'/'.$rand.'.'.$file_img;
                    move_uploaded_file($_FILES['file_img']['tmp_name'], $target_file);    
                }
                update_account_id($id,$user,$email,$password,$add,$sdt,$target_file);    
                $user = user_profile($id);
                $_SESSION['user'] = $user;
            }    
            include('./userprofile/profile.php');
            break;
        case 'logout':
            session_destroy();
            header('location:index.php');
            break;
        case 'deletecmt':
            if(isset($_GET['id'])){
                $sql = 'DELETE FROM binhluan WHERE id='.$_GET['id'];
                pdo_execute($sql);
                header("location:".$_SERVER['HTTP_REFERER']);
            }
            break;
        case 'giohang':
            
            if(isset($_POST['addcart'])){
                $idtk = $_SESSION['user']['id'];
               $id = $_POST['id'];
                $name = $_POST['name'];
                $price = $_POST['price'];
                $img = $_POST['img'];
                $soluong = $_POST['soluong'];
                $color = $_POST['color'];
                $size = $_POST['size'];
                $tongtien = $price * $soluong;
                insert_cart($name,$price,$img,$soluong,$color,$size,$tongtien,$idtk);
                $notify ='Thêm Thành Công ';
                header("location:".$_SERVER['HTTP_REFERER']);
                
            }
            include('./userprofile/sanphamct.php');
            break;
        case 'viewgiohang':
            if(isset($_GET['id'])){
                $id = $_GET['id'];
              $resultgh=   result_giohang($id);
              $tongtiengh = result_tongtien($id);
              $limit = 1;
              $rand = rand(1,10);
              $resultspgh = resultsp_gh($limit,$rand);
            }
            include('./userprofile/giohang.php');
            break;
        default:
        include('./userprofile/home.php');

    }
}else{
    include('./userprofile/home.php');

}
include('./userprofile/footer.php');
ob_end_flush();

?>