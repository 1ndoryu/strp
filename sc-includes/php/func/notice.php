<?php

class Notice
{
	public static function getNotices($ID_user, $no_read = false)
	{
        $w = array(
            "ID_user" => $ID_user
        );

        if($no_read == true)
        {
            $w['read'] = 0;
        }

        $rows = selectSQL("sc_notice", $w, "date DESC");

        return $rows;
    }

    public static function getNotice($ID_notice)
    {
        $w = array(
            "ID_notice" => $ID_notice
        );

        $rows = selectSQL("sc_notice", $w);

        return $rows[0];
    }

    public static function printNotices()
    {
        if(isset($_SESSION['data']['ID_user']) && $_SESSION['data']['notify'] == 1)
        {
           $data = array(
                'ID_user' => $_SESSION['data']['ID_user']
            );
            $notices = loadBlock('notices', $data);
            echo $notices;
        }
    }

    public static function addNotice($ID_user, $title, $text, $link = null, $data = null)
    {
        $w = array(
            "ID_user" => $ID_user,
            "read" => 0,
            "title" => $title,
            "text" => $text,
            "link" => $link,
            "date" => time(),
            "data" => json_encode($data)
        );

        insertSQL("sc_notice", $w);
    }

    public static function readNotice($ID_notice)
    {
        $w = array(
            "ID_notice" => $ID_notice
        );

        updateSQL("sc_notice",array(
            "read" => 1
        ), $w);
    }

    public static function deleteNotice($ID_notice)
    {
        $w = array(
            "ID_notice" => $ID_notice
        );

        deleteSQL("sc_notice", $w);
    }

    public static function catch()
    {
        if(isset($_GET['delete']))
        {
            $ID_notice = $_GET['delete'];
            self::deleteNotice($ID_notice);
        }

        if(isset($_GET['notify']))
        {
            $ID_user = $_SESSION['data']['ID_user'];
            $notify = $_GET['notify'];
            $_SESSION['data']['notify'] = $notify;
   
            updateSQL("sc_user", array(
                "notify" => $notify
            ), array(
                "ID_user" => $ID_user
            ));
            
        }
    }

}