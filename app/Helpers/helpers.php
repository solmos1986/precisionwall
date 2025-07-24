<?php
function jsonLog($data)
{
    return strval(json_encode($data, JSON_PRETTY_PRINT));
}
