<?php

/**
 *   ____ _    __  __     _  __    ___  _
 *  / ___| |   \ \/ /    / |/ /_  ( _ )/ |
 * | |   | |    \  /_____| | '_ \ / _ \| |
 * | |___| |___ /  \_____| | (_) | (_) | |
 *  \____|_____/_/\_\    |_|\___/ \___/|_|
 *
 * Task: https://cloudrexx.atlassian.net/browse/CLX-1681
 * This script replaces "Contrexx" by "Cloudrexx" in every Content Pages.
 *
 * Step 1: Change content in Content Pages: http://wiki.contrexx.com/en/index.php?title=Update_Script_Placeholder_Change
 * Step 2: Reset logged Content Page entries: https://bitbucket.org/cloudrexx/scripts/src/db313ef1a90a37bacb5772297b2a3f0b3b3cbbb9/resetHistory.php
 */

/**
 * General
 */

// sets max execution time to 2 hours
ini_set('max_execution_time', 7200);

require_once dirname(__FILE__) . '/core/Core/init.php';

// debugging
//error_reporting(-1);
//ini_set('display_errors', 'On');
//\DBG::activate(DBG_PHP);
//\DBG::activate(DBG_LOG);


/**
 * Step 1
 */

echo '-------------------------- Step 1: START --------------------------<br />';

// init cx
$cx = init('minimal');
// gets db stuff
$em = $cx->getDb()->getEntityManager();
$cxn = $em->getConnection();

try {
    // starts transaction
    $cxn->beginTransaction();

    // migrate content pages
    migrateContentPage(array('contrexx', 'Contrexx'), 'Cloudrexx');
    migrateContentPage('WCMS', 'CXM');
    migrateContentPage('Web Content Management System', 'Customer Experience Management System');

    $cxn->commit();
    echo 'Step 1: Migration of done.<br />';

} catch (\Cx\Lib\UpdateException $e) {
    $cxn->rollback();
    echo 'Step 1: Migration of abort.<br />';

    return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
}

echo '-------------------------- Step 1: END --------------------------<br />';


/**
 * Step 2
 */

echo '-------------------------- Step 2: START --------------------------<br />';

$log = array('Cx\\Core\\ContentManager\\Model\\Entity\\Page');
$obj = new ResetHistory();
$obj->loggableEntries($log);

echo '-------------------------- Step 2: END --------------------------<br />';


/**
 * Lib
 */

/**
 * Replaces certain strings in a content page
 *
 * @param   mixed $search Search string or array of strings
 * @param   mixed $replace Replacement string or array of strings
 */
function migrateContentPage($search, $replace)
{
    // init cx
    $cx = init('minimal');
    // gets db stuff
    $em = $cx->getDb()->getEntityManager();
    $pageRepo = $em->getRepository('Cx\Core\ContentManager\Model\Entity\Page');

    $pages = $pageRepo->findAll();
    foreach ($pages as $page) {
        if ($page) {
            $page->setContent(str_replace($search, $replace, $page->getContent()));
            $page->setTitle(str_replace($search, $replace, $page->getTitle()));
            $page->setCustomContent(str_replace($search, $replace, $page->getCustomContent()));
            $page->setMetatitle(str_replace($search, $replace, $page->getMetatitle()));
            $page->setMetadesc(str_replace($search, $replace, $page->getMetadesc()));
            $page->setMetakeys(str_replace($search, $replace, $page->getMetakeys()));
            $page->setMetarobots(str_replace($search, $replace, $page->getMetarobots()));
            $page->setContentTitle(str_replace($search, $replace, $page->getContentTitle()));
            $page->setUpdatedAtToNow();
            $em->persist($page);
        }
    }
    $em->flush();
}

class ResetHistory
{
    function loggableEntries($log)
    {
        global $objDatabase;

        foreach ($log as $logEntryClass) {
            //Delete Associated log entry in table
            $objDatabase->Execute('DELETE FROM `' . DBPREFIX . 'log_entry` WHERE `object_class` ="' . contrexx_input2db($logEntryClass) . '"');

            //Fetch All Entries
            $entriesArray = $this->fetchAllEntry($logEntryClass);

            //Build the create-log-entries (SQL-statements) for each entity and output them into a SQL-dump contrexx_log_entry.sql
            $sqlData = $this->getSqlCreateLogEntry($entriesArray, $logEntryClass);

            $this->loadGeneratedLogEntriesToDb($sqlData);
            //$this->createDatabaseDump($sqlData);
            //$this->loadSqlDump();
        }
    }

    //Load the generated log entries into db
    function loadGeneratedLogEntriesToDb($sqlData)
    {
        global $objDatabase;
        //load into db
        foreach ($sqlData as $query) {
            $result = $objDatabase->Execute($query);
            if (!$result === true) exit("Error in executing query");
        }
        echo 'Log Entries Histories has been cleaned Successfully<br />';
    }

    //Fetch All Entries
    function fetchAllEntry($logEntryClass)
    {
        //Fetch all the entities from the database.
        $fetchedEntriesArray = $this->getDoctrineEntityArray($logEntryClass);
        return $fetchedEntriesArray;
    }

    //Build the create-log-entries (SQL-statements) for each entity and output them into a SQL-dump contrexx_log_entry.sql
    function getSqlCreateLogEntry($dataArray, $logClass)
    {
        $logsTable = 'contrexx_log_entry';

        $logsMap = array(
            'action' => 'create',
            'version' => 1,
            'object_class' => $logClass,
            'username' => '{"id":1,"name":"system"}',
        );

        $sql = array();
        foreach ($dataArray as $data) {
            $log = array(
                'logged_at' => $data['updatedAt']->format('Y-m-d H:i:s'),
                'object_id' => $data['id'],
                'data' => serialize($data),
            );

            $sql[] = $this->getSqlInsertFromFieldArray($logsTable, $log, $logsMap);

        }

        return $sql;
    }

    //Create Database Dump
    function createDatabaseDump($sqlData)
    {

        if (!empty($sqlData)) {
            $file = fopen($_SESSION->getTempPath() . '/contrexx_log_entry.sql', "w");
            fwrite($file, $sqlData);
            fclose($file);
        }
    }

    //Load generated sql-dump contrexx_log_entry.sql into the database
    function loadSqlDump()
    {
        global $objDatabase;

        //Load generated sql-dump contrexx_log_entry.sql into the database
        $fp = @fopen($_SESSION->getTempPath() . '/contrexx_log_entry.sql', "r");
        if ($fp !== false) {
            while (!feof($fp)) {
                $buffer = fgets($fp);
                if ((substr($buffer, 0, 1) != "#") && (substr($buffer, 0, 2) != "--")) {
                    $sqlQuery .= $buffer;
                    if (preg_match("/;[ \t\r\n]*$/", $buffer)) {
                        $result = $objDatabase->Execute($sqlQuery);
                        if ($result === false) {
                            $statusMsg = "Error in executing query";
                        } else {
                            $statusMsg = "Log Entries Histories has been cleaned Successfully";
                        }
                        $sqlQuery = "";
                    }
                }
            }
            echo $statusMsg . '<br />';
        }
    }

    //Fetch all the entities from the database.
    function getDoctrineEntityArray($entityClass)
    {
        $entities = \Env::get('em')->getRepository($entityClass)->findAll();
        $metadata = \Env::get('em')->getClassMetadata($entityClass);
        $entitiesArray = array();
        foreach ($entities as $entity) {
            $entityArray = array();
            foreach ($metadata->fieldNames as $fieldName) {
                $entityArray[$fieldName] = call_user_func(array($entity, 'get' . ucfirst($fieldName)));
            }
            $entitiesArray[] = $entityArray;
        }
        return $entitiesArray;
    }

    function getSqlInsertFromFieldArray($table_name, $fields, $map = '')
    {
        $insertfields = array();

        // map can disallow or rename fields
        foreach ($fields as $name => $value) {
            if (isset($map[$name])) {
                $mapval = $map[$name];
                unset($map[$name]);

                // ignore field
                if ($mapval === 0) {
                    continue;

                    // rename field
                } else {
                    $name = $mapval;
                }
            }
            $insertfields[$name] = $value;
        }

        // map can set additional fields
        foreach ($map as $name => $value) {
            $insertfields[$name] = $value;
        }

        // prepare for sql
        if (!count($insertfields)) {
            return '';
        }
        foreach ($insertfields as $name => $value) {
            $insertfields[$name] = $this->prepareDataForSQLStatement($insertfields[$name]);
            if ($value != 'NULL') {
                $insertfields[$name] = '\'' . $insertfields[$name] . '\'';
            }
        }
        return 'INSERT INTO `' . $table_name . '`(`' . implode('`, `', array_keys($insertfields)) . '`) VALUES(' . implode(', ', $insertfields) . ');' . "\n";
    }

    private function prepareDataForSQLStatement($data)
    {
        $data = addslashes($data);
        $data = preg_replace('/\r/', '\r', $data);
        $data = preg_replace('/\n/', '\n', $data);
        return $data;
    }
}
