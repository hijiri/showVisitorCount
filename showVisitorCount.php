<?php
/**
 * Loggix_Plugin - Show Visitor Count
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.04.24
 * @version   10.5.27
 */

// $this->plugin->addFilter('navigation', 'showVisitorCount');

function showVisitorCount()
{
    global $pathToIndex;

    // SETTING BEGIN
    // Online User time (minute)
    $limit      = 5;
    // DataBase file
    $fileDB      = 'sqlite:' . $pathToIndex . '/data/showVisitorCount.db';
    // SETTING END

    try {

        $dbh = new PDO($fileDB);

        // Get Date (DB)
        $result = $dbh->query('SELECT date FROM count_log');
        $dateLog  = $result->fetchColumn();

        // Get Today
        $time = time();
        $dateNow = date('Y/m/d', $time);

        // Different day
        if ($dateLog !== $dateNow) {

            // initialize the access_log
            $sth = $dbh->prepare('DELETE FROM access_log');
            $sth->execute();
        
            // Update date
            $sql = 'UPDATE count_log '
                 . 'SET '
                 . "date = '" . $dateNow . "'";
            $sth = $dbh->prepare($sql);
            $sth->execute();
        }

        // Get host name
        $host = (isset($_SERVER['REMOTE_HOST'])) ? $_SERVER['REMOTE_HOST'] : '';
        $addr = $_SERVER['REMOTE_ADDR'];
        if (($host == '') || ($host == $addr)) { $host = gethostbyaddr($addr); }
        if ($host == '') { $host = $addr; }

        // Check already access
        $result = $dbh->query('SELECT host FROM access_log');
        if ($item = $result->fetchAll()) {
            foreach ($item as $row) {
                $accessList[] = $row['host'];
            }
            $countFlag = (in_array($host, $accessList)) ? FALSE : TRUE;

        // First access
        } else {
            $countFlag = TRUE;
        }

        // Get online user
        $result = $dbh->query('SELECT * FROM online_log');
        $item   = $result->fetchAll();
        foreach ($item as $row) {
            $onlineList[] = array('time' =>$row['time'], 'online_host' =>$row['online_host']);
        }

        // Delete over limit
        $i = 0;
        $limit *= 60;
        foreach ($onlineList as $row) {
            if (($time - $limit) > $row['time']) {
                array_splice($onlineList,$i);
                break;
            }
            $i++;
        }
    
        // Check already access
        $onlineFlag = TRUE;
        foreach ($onlineList as $row) {
            if (in_array($host, $row)) {
                $onlineFlag = FALSE;
                break;
            }
        }

        // Add new online user
        if ($onlineFlag) {
            array_unshift($onlineList, array('time'=>$time,'online_host'=>$host));
        }
    
        // Read count
        $result = $dbh->query('SELECT count FROM count_log');
        $count  = $result->fetchColumn();
        
        // Count up
        if ($countFlag) {
            // Increment
            $count++;

            // Add access
            $sql = 'INSERT INTO access_log '
                 . "VALUES('" . $host . "')";
            $sth = $dbh->prepare($sql);
            $sth->execute();

            // Update count
            $sql = 'UPDATE count_log '
                 . 'SET '
                 . "count = '" . $count . "'";
            $sth = $dbh->prepare($sql);
            $sth->execute();
        }

        // Save online user list
        $sth = $dbh->prepare('DELETE FROM online_log');
        $sth->execute();
        foreach ($onlineList as $row) {
            $sql = 'INSERT INTO online_log '
                 . "VALUES('" . $row['time'] . "', '" . $row['online_host'] . "')";
            $sth = $dbh->prepare($sql);
            $sth->execute();
        }

        // Get online user count
        $online = count($onlineList);

    } catch (PDOException $exception) {
        die($exception->getMessage());
    }

    $users = ($online == 1) ? 'user' : 'users';

    return '
<p>Visitor:' . $count . 'th.</p>
<p>Online:' . $online . $users . '.</p>
';
}
