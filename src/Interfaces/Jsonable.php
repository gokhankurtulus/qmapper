<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 26.01.2024 Time: 16:26
 */

namespace QMapper\Interfaces;

interface Jsonable
{
    public function toJson(int $options = 0): string;
}