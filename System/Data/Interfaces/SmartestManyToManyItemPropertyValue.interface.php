<?php

interface SmartestManyToManyItemPropertyValue{

    public function hydrateFromStoredIdsArray($ids, $draft_mode=false);

}