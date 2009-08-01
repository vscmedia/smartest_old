<?php

interface SmartestSystemUiObject extends ArrayAccess{
    
    public function getSmallIcon();
    
    public function getLargeIcon();
    
    public function getLabel();
    
    public function getActionUrl();
    
}