<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 9.06.2023 Time: 06:22
 */


namespace QMapper\Interfaces;

interface Jsonable
{
    public function toJson(int $options = 0): string;
}