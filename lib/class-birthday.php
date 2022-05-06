<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 5-05-2022
 ****************************************************************
 *  class-birthday.php
 *  Birthday stuff
 *
 *************************************************************/
// NOT DONE.
// Maybe this file should extent Contact. All the information such as names, dates, etc. should be
// determined by ID in the Contact object.
// Birthday class should only retrieve a list of ID's by date order and that way would determine
// which ID's to call in instances of Contact object.

global $globalUsers;

$globalUsers->checkForLogin();

class Birthday
{

    private function getBirthdayData($bdayInterval)
    {
        global $globalSqlLink;
        $select = 'id, CONCAT(firstname,\' \',lastname) AS fullname,
					   DATE_FORMAT(birthday, \'%M %e, %Y\') AS birthday,
                       MONTHNAME(birthday) AS month,
                       DAYOFMONTH(birthday) AS day,
                       YEAR(birthday) AS year,
					   (YEAR(NOW()) - YEAR(birthday) + (RIGHT(CURRENT_DATE,5)>RIGHT(birthday,5))) AS age,
				       (TO_DAYS((birthday + INTERVAL (YEAR(CURRENT_DATE)-YEAR(birthday) + (RIGHT(CURRENT_DATE,5)>RIGHT(birthday,5))) YEAR)) - TO_DAYS(CURRENT_DATE)) as daysAway';
        $where = 'birthday != \'\'
					AND (TO_DAYS((birthday + INTERVAL (YEAR(CURRENT_DATE)-YEAR(birthday) + (RIGHT(CURRENT_DATE,5)>RIGHT(birthday,5)) ) YEAR)) - TO_DAYS(CURRENT_DATE)) < ' . $bdayInterval . '
					AND hidden != 1';
        $globalSqlLink->SelectQuery($select, TABLE_CONTACT, $where, "ORDER BY daysAway ASC, age DESC");

        return $globalSqlLink->FetchQueryResult();

    }

    public function GetBirthday($options, $lang, $file_address)
    {
        $r_bday = $this->getBirthdayData($options->bdayInterval());
        $body['langbirth'] = $lang['BIRTHDAY_UPCOMING1'] . $options->bdayInterval() . $lang['BIRTHDAY_UPCOMING2'];

        if($r_bday != -1) {
            return $r_bday;
        }

    }

}
