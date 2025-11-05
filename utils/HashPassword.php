<?php
class HashPassword {
    public function hash($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function compare($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>