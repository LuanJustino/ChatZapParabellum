<?php
    if($Usuario != false){
      $Usuario = Usuarios::findById($id);
      $Usuario->delete($id);
    }else{
        echo 'User not found!';
    }
