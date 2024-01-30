<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 18:25
 */


namespace QMapper\Loggers;

use Logger\Logger;

class ModelLogger extends Logger
{
    protected static string $folderPath = "";
    protected static string $fileName = "model.log";
}