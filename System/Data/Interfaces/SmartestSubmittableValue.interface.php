<?php

interface SmartestSubmittableValue{

    public function hydrateFromFormData($v);
    public function renderInput($params);

}