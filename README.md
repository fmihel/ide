# Web editor with ide functionally (JS and PHP)

## Install 
On you hosting or local create path (Ex: `"root\wsi"`  correspond: `"https:\\yousite\wsi\"`) and run:

`git clone https://github.com/fmihel/ide.git`

## Run 
In browser set:

`https:\\yoursite\wsi\ide\wsi.php`

Use login: `admin` pass: `admin` for enter

## Change autorize and add other users

If you install in `root\wsi`, that is, the project is in the folder `root\wsi\ide`, create
folder `root\wsi\users`, create you autorize file with name (as Ex:) `root\wsi\users\user1.php`
Set next struct:
```
<?php
  $user_data=array(
    'login'   =>'my_login',
    'password'=>'my_password',
  );
?>

```

Create file  `root\wsi\users\users_list.php` and add new user connection:
```
<?php
    
    $USERS->add_user('user1','user1.php');

?>
```





