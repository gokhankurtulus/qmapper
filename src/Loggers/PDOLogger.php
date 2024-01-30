<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 18:27
 */


namespace QMapper\Loggers;

use Logger\Logger;

class PDOLogger extends Logger
{
    protected static string $folderPath = "";
    protected static string $fileName = "pdo.log";
}