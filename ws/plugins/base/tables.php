<?php

class TEST_CLIENTS_TABLE extends _table {
    const NAME  ="TEST_CLIENTS";
    const ALIAS ="TC";
    const INDEX ="ID_CLIENT";
    /*c:types*/
    public static $types=array(
        "ID_CLIENT"  =>"int",
        "NAME"       =>"string",
        "AGE"        =>"int",
        "LAST_MODIFY"=>"date",
        "SUM"        =>"double",
        "UUID"       =>"string",
    );
    /*c:update*/
    public static $update=array(
        "NAME",
        "AGE",
        "SUM",
        "UUID",
    );
    public static $select=array(
        /*c:default*/
        "default"=>array(
            "ID_CLIENT",
            "NAME",
            "AGE",
            "LAST_MODIFY",
            "SUM",
        ),
    );
    public static $noAlias = array(
        'ID_CLIENT',
        'NAME'
    );
};

class TEST_CLIENTS_PHONE_TABLE extends _table {
    const NAME  ="TEST_CLIENTS_PHONE";
    const ALIAS ="TCP";
    const INDEX ="ID_CLIENT_PHONE";
    /*c:types*/
    public static $types=array(
        "ID_CLIENT_PHONE"=>"int",
        "ID_CLIENT"      =>"int",
        "PHONE"          =>"string",
        "LAST_MODIFY"    =>"date",
    );
    /*c:update*/
    public static $update=array(
        "ID_CLIENT",
        "PHONE",
    );
    public static $select=array(
        /*c:default*/
        "default"=>array(
            "ID_CLIENT_PHONE",
            "ID_CLIENT",
            "PHONE",
            "LAST_MODIFY",
        ),
    );
    
    public static $noAlias = array(
        'ID_CLIENT',
        'ID_CLIENT_PHONE'
    );
    
};


?>