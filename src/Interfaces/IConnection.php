<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 05:11
 */


namespace QMapper\Interfaces;


interface IConnection
{
    public function initialize(): void;

    public function terminate(): void;
}