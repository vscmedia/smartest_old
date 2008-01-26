<?php

class DesktopManager{
    
    protected $database;
    
    public function __construct(){
        $this->database = SmartestPersistentObject::get('db:main');
    }
    
    public function getSites(){
	    $sql = "SELECT * FROM Sites";
	    $result = $this->database->queryToArray($sql);
	    return $result;
	}
    
    public function getSelfAssignedTodoListItems($user_id){
        
        $items = array();
        
        $sql = "SELECT * FROM TodoItems WHERE todoitem_receiving_user_id='".$user_id."' AND todoitem_type='".SmartestTodoItem::PERSONAL."' ORDER BY todoitem_time_assigned DESC";
        $result = $this->database->queryToArray($sql);
        
        foreach($result as $tdi){
            $i = new SmartestTodoItem;
            $i->hydrate($tdi);
            $items[] = $i;
        }
        
        return $items;
        
    }
    
    public function getSelfAssignedTodoListItemsAsArrays($user_id){
        
        $items = $this->getSelfAssignedTodoListItems($user_id);
        $arrays = array();
        
        foreach($items as $tdi){
            $arrays[] = $tdi->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getAssignedTodoListItems($user_id){
        
        $items = array();
        
        $sql = "SELECT * FROM TodoItems WHERE todoitem_receiving_user_id='".$user_id."' AND todoitem_type='".SmartestTodoItem::ASSIGNMENT."' ORDER BY todoitem_time_assigned DESC";
        $result = $this->database->queryToArray($sql);
        
        foreach($result as $tdi){
            $i = new SmartestTodoItem;
            $i->hydrate($tdi);
            $items[] = $i;
        }
        
        return $items;
        
    }
    
    public function getAssignedTodoListItemsAsArrays($user_id){
        
        $items = $this->getAssignedTodoListItems($user_id);
        $arrays = array();
        
        foreach($items as $tdi){
            $arrays[] = $tdi->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getLockedPageDuties($user_id){
        
        $all_items = array();
        
        // pages that I am editing and have locked
        $sql = "SELECT * FROM Pages WHERE page_is_held=1 AND page_held_by='".$user_id."' AND page_deleted !='1'";
        $result = $this->database->queryToArray($sql);
        
        $pages = array();
        
        foreach($result as $locked_page){
            $p = new SmartestPage;
            $p->hydrate($locked_page);
            $pages[] = $p->__toArray();
        }
        
        return $pages;
        
    }
    
    public function getLockedItemDuties($user_id){
        
        $all_items = array();
        
        // pages that I am editing and have locked
        $sql = "SELECT * FROM Items WHERE item_is_held=1 AND item_held_by='".$user_id."' AND item_deleted !='1'";
        $result = $this->database->queryToArray($sql);
        
        $items = array();
        
        foreach($result as $locked_item){
            $i = new SmartestItem;
            $i->hydrate($locked_item);
            $items[] = $i->__toArray();
        }
        
        return $items;
        
    }
    
    public function getItemsAwaitingApproval(){
        $sql = "SELECT * FROM Items WHERE item_modified > item_last_published AND item_changes_approved !=1 AND item_deleted != 1";
        $result = $this->database->queryToArray($sql);
        
        $items = array();
        
        foreach($result as $item){
            $itemObj = new SmartestItem;
            $itemObj->hydrate($item);
            $items[] = $itemObj->__toArray();
        }
        
        return $items;
        
    }
    
    public function getPagesAwaitingApproval(){
        $sql = "SELECT * FROM Pages WHERE page_modified > page_last_published AND page_changes_approved !=1 AND page_deleted != 'TRUE'";
        $result = $this->database->queryToArray($sql);
        
        $pages = array();
        
        foreach($result as $page){
            $itemObj = new SmartestPage;
            $itemObj->hydrate($page);
            $pages[] = $itemObj->__toArray();
        }
        
        return $pages;
    }
    
    public function getItemsAwaitingPublishing(){
        $sql = "SELECT * FROM Items WHERE item_modified > item_last_published AND item_changes_approved ='1' AND item_deleted != 1";
        $result = $this->database->queryToArray($sql);
        
        foreach($result as $item){
            $itemObj = new SmartestItem;
            $itemObj->hydrate($item);
            $items[] = $itemObj->__toArray();
        }
        
        return $items;
    }
    
    public function getPagesAwaitingPublishing(){
        $sql = "SELECT * FROM Pages WHERE page_modified > page_last_published AND page_changes_approved = '1' AND page_deleted != 'TRUE'";
        $result = $this->database->queryToArray($sql);
        
        $pages = array();
        
        foreach($result as $page){
            $itemObj = new SmartestPage;
            $itemObj->hydrate($page);
            $pages[] = $itemObj->__toArray();
        }
        
        return $pages;
    }
    
}