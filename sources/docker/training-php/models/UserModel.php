<?php
require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function auth($userName, $password) {
        $userName = mysqli_real_escape_string(self::$_connection, $userName);
        $md5Password = md5($password);
        $md5Password = mysqli_real_escape_string(self::$_connection, $md5Password);

        $sql = "SELECT * FROM users WHERE name='$userName' AND password='$md5Password'";
        return $this->select($sql);
    }

    public function getUsers($params = []) {
        if (!empty($params['keyword'])) {
            $keyword = mysqli_real_escape_string(self::$_connection, $params['keyword']);
            $sql = "SELECT * FROM users WHERE name LIKE '%$keyword%'";
            return $this->select($sql);
        } else {
            $sql = "SELECT * FROM users";
            return $this->select($sql);
        }
    }

    public function findUserById($id) {
        $id = intval($id);
        $sql = "SELECT * FROM users WHERE id = $id";
        return $this->select($sql);
    }

    public function insertUser($input) {
        // Tránh lỗi SQL injection
        $name = mysqli_real_escape_string(self::$_connection, $input['name']);
        $fullname = mysqli_real_escape_string(self::$_connection, $input['fullname']);
        $email = isset($input['email']) ? mysqli_real_escape_string(self::$_connection, $input['email']) : '';
        $type = isset($input['type']) ? mysqli_real_escape_string(self::$_connection, $input['type']) : 'user';
        $password = md5($input['password']);
    
        $sql = "INSERT INTO `users` (`name`, `fullname`, `email`, `type`, `password`) VALUES (
                    '$name', '$fullname', '$email', '$type', '$password'
                )";
    
        return $this->insert($sql);
    }
    
    public function updateUser($input) {
        $id = intval($input['id']);
        $name = mysqli_real_escape_string(self::$_connection, $input['name']);
        $fullname = mysqli_real_escape_string(self::$_connection, $input['fullname']);
        $email = isset($input['email']) ? mysqli_real_escape_string(self::$_connection, $input['email']) : '';
        $type = isset($input['type']) ? mysqli_real_escape_string(self::$_connection, $input['type']) : 'user';
        $password = !empty($input['password']) ? md5($input['password']) : null;
    
        $sql = "UPDATE `users` SET 
                    name = '$name',
                    fullname = '$fullname',
                    email = '$email',
                    type = '$type'";
        
        if ($password) {
            $sql .= ", password = '$password'";
        }
    
        $sql .= " WHERE id = $id";
    
        return $this->update($sql);
    }
    

    public function deleteUserById($id) {
        $id = intval($id);
        $sql = "DELETE FROM users WHERE id=$id";
        return $this->delete($sql);
    }
}
