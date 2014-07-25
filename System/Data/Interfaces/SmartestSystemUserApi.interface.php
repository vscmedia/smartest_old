<?php

interface SmartestSystemUserApi{
    
    public function hasToken($token, $include_root=true);
    public function hasGlobalToken($token);
    
}