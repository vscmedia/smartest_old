<?php

interface SmartestStorableValue{

    public function getStorableFormat();
    public function hydrateFromStorableFormat($v);

}