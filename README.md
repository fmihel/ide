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
```php
<?php
  $user_data=array(
    'login'   =>'my_login',
    'password'=>'my_password',
  );
?>

```

Create file  `root\wsi\users\users_list.php` and add new user connection:
```php
<?php
    
    $USERS->add_user('user1','user1.php');

?>
```
__Attention!__ Specify exclude unused folders from processing, to reduce the load.
Add `cc_path` element to `$user_data` array.

_Example_: exclude the paths `root/composer` and `root/relese`
```php
  $user_data=array(
    ...
    'cc_paths'=>array(
        '!composer/',
        '!release/',
    ),
    ...
  );

```

## Templates (snippets)
1. If you install in `root\wsi`, that is, the project is in the folder `root\wsi\ide`, create
folder `root\wsi\templates`
Set path to `$user_data` array. Example:
```php
  $user_data=array(
    ...
    'templates'=>'wsi/templates/',
  );

```
2. Create theme sub folder in `templates`. Ex: `templates\php`

3. In theme sub folder add xml. Ex: `templates\php\`
```xml
<?xml version="1.0" encoding="utf-8" ?>
<body>
    <header>
        <info>function</info>
        <short>func</short>
        <ext>php</ext>
        <vars>
          <item name='funcName'>default</item>
          <item name='comment'>comment</item>
        </vars>
    </header>
    <template><![CDATA[
    /**
    * function <{funcName}>
    * <{comment}>
    */
    function <{funcName}>(){
       <{$cursor}>
    }
    ]]>
    </template>
</body>

```
__xml struct__:

|tag|note|example|
|----|----|----|
|info|msg show in right list templates|function user|
|short|short key for fast call, enter the first char from short key and press `ctrl-j` |userf |
|ext|binds short key with file ext|php|
|template| code of snippets|```<![CDATA[  public static function <{$cursor}>(){}]]>```|
|vars|list of item tag for define template variables|  see example |










































