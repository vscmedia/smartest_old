<?php

/**
 *
 * PHP versions 4,5
 *
 * @category   WebApplication
 * @package    Smartest
 * @subpackage Pages
 * @author     Marcus Gilroy-Ware <marcus@visudo.com>
 * @author     Eddie Tejeda <eddie@visudo.com>
 */
 
// include_once "Managers/AssetsManager.class.php";
include_once "Managers/SetsManager.class.php";
include_once "Managers/TemplatesManager.class.php";
include_once "System/Applications/MetaData/MetaDataManager.class.php";

class Pages extends SmartestSystemApplication{
	
	protected $setsManager;
	protected $templatesManager;
	protected $propertiesManager;
	
	protected function __smartestApplicationInit(){
		$this->setsManager = new SetsManager;
		$this->templatesManager = new TemplatesManager;
		// $this->propertiesManager = new PagePropertiesManager;
		// var_dump($this);
	}
	
	public function startPage(){
		// No code is needed here, just a function definition
		$this->setTitle("Welcome to Smartest");
	}
	
	public function openPage($get){
	    
	    if($this->getRequestParameter('page_id')){
	        
	        $page = new SmartestPage;
	        
	        if($page->hydrate($this->getRequestParameter('page_id'))){
	            
	            $page->setDraftMode(true);
	            
	            if($this->getUser()->hasToken('modify_page_properties')){
	            
	                if($page->getIsHeld() && $page->getHeldBy() && $page->getHeldBy() != $this->getUser()->getId() && !$this->getUser()->hasToken('edit_held_pages')){
    	                
    	                // page is already being edited by another user
    	                $editing_user = new SmartestUser;
	                    
    	                if($editing_user->hydrate($page->getHeldBy())){
    	                    $this->addUserMessageToNextRequest($editing_user->__toString().' is already editing this page.', SmartestUserMessage::ACCESS_DENIED);
    	                }else{
    	                    $this->addUserMessageToNextRequest('Another user is already editing this page.', SmartestUserMessage::ACCESS_DENIED);
    	                }
	                
    	                $this->redirect('/smartest/pages');
    	                
    	            }else{
	                    
	                    // page is available to edit
			            SmartestSession::set('current_open_page', $page->getId());
			            
			            if($page->getIsHeld() && $this->getUser()->hasToken('edit_held_pages') && $page->getHeldBy() != $this->getUser()->getId()){
			                
			                $editing_user = new SmartestUser;
                            
                            if($editing_user->hydrate($page->getHeldBy())){
        	                    $this->addUserMessageToNextRequest('Careful: '.$editing_user->__toString().' has not yet released this page.', SmartestUserMessage::INFO);
        	                }else{
        	                    $this->addUserMessageToNextRequest('Careful: another user has not yet released this page.', SmartestUserMessage::INFO);
        	                }
        	                
			            }else{
			                // lock it against being edited by other people
    			            $page->setIsHeld(1);
    			            $page->setHeldBy($this->getUser()->getId());
    			            $page->save();
			            
    			            /* if(!$this->getUser()->hasTodo('SM_TODOITEMTYPE_RELEASE_PAGE', $page->getId())){
    			                $this->getUser()->assignTodo('SM_TODOITEMTYPE_RELEASE_PAGE', $page->getId(), 0);
    		                } */
	                    }
			            
			            $page->clearRecentlyEditedInstances($this->getSite()->getId(), $this->getUser()->getId());
        			    $this->getUser()->addRecentlyEditedPageById($page->getId(), $this->getSite()->getId());
		            
			            // $this->redirect('/'.$this->getRequest()->getModule().'/editPage?page_id='.$page->getWebid());
			            $this->redirect('@websitemanager:basic_info?page_id='.$page->getWebid());
    			        
    		        }
		        
	            }else{
	                
	                $this->addUserMessageToNextRequest('You don\'t have permission to edit pages.', SmartestUserMessage::ACCESS_DENIED);
	                
	                if(SmartestSession::hasData('current_open_project')){
	                    $this->redirect('/smartest/pages');
                    }else{
                        $this->redirect('/smartest');
                    }
	                
	            }
		        
		    }else{
		        $this->redirect('/smartest');
		    }
		}
	}
	
	public function closeCurrentPage($get){
	    
	    if($this->getRequestParameter('release') && $this->getRequestParameter('release') == 1){
	        $page = new SmartestPage;
	        
	        if($page->hydrate(SmartestSession::get('current_open_page'))){
	            $page->setIsHeld(0);
	            $page->setHeldBy('');
	            $page->save();
	        }
	    }
	    
	    SmartestSession::clear('current_open_page');
	    $this->redirect('/smartest/pages');
	}
	
	public function releasePage($get){
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($this->getRequestParameter('page_id'))){
	        
	        $page->setDraftMode(true);
	        
	        if($page->getIsHeld() == '1'){
	            
	            if($page->getHeldBy() == $this->getUser()->getId()){
                    
                    $page->setIsHeld(0);
                    $page->setHeldBy('');
                    $page->save();
                    $this->addUserMessageToNextRequest("The page has been released.", SmartestUserMessage::SUCCESS);
                    
                    if($todo = $this->getUser()->getTodo('SM_TODOITEMTYPE_RELEASE_PAGE', $page->getId())){
		                $todo->complete();
	                }
	                
                }else{
                    //  the page is being edited by another user
                    $this->addUserMessageToNextRequest("You can't release this page because another user is editing it.", SmartestUserMessage::INFO);
                }
            }else{
                $this->addUserMessageToNextRequest("The page has been released.", SmartestUserMessage::SUCCESS);
                // $this->addUserMessageToNextRequest("The page is not currently held by any user.", SmartestUserMessage::INFO);
            }
            
        }
	    
	    // SmartestSession::clear('current_open_page');
	    
	    if($this->getRequestParameter('from') && $this->getRequestParameter('from') == 'todoList'){
	        $this->redirect('/smartest/todo');
        }else{
            SmartestSession::clear('current_open_page');
            $this->redirect('/smartest/pages');
        }
	}
	
	public function clearPagesCache(){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        if($this->getUser()->hasToken('clear_pages_cache')){
            
                $page_prefix = 'site'.$this->getSite()->getId().'_';
            
                $cache_files = SmartestFileSystemHelper::load(SM_ROOT_DIR.'System/Cache/Pages/');
            
                if(is_array($cache_files)){
                
                    $deleted_files = array();
                    $failed_files = array();
                    $untouched_files = array();
                
                    foreach($cache_files as $f){
                    
                        $path = SM_ROOT_DIR.'System/Cache/Pages/'.$f;
                    
                        if(strlen($f) && $page_prefix == substr($f, 0, strlen($page_prefix))){
                            // echo "deleting ".$path.'...<br />';
                            if(@unlink($path)){
                                $deleted_files[] = $f;
                            }else{
                                $failed_files[] = $f;
                            }
                        }else{
                            $untouched_files = $f;
                        }
                    }
                    
                    SmartestLog::getInstance('site')->log("{$this->getUser()} cleared the pages cache. ".count($deleted_files)." files were removed.", SmartestLog::USER_ACTION);
                    
                    $this->send(true, 'show_result');
                    $this->send($deleted_files, 'deleted_files');
                    $this->send(count($deleted_files), 'num_deleted_files');
                    $this->send($failed_files, 'failed_files');
                    $this->send($untouched_files, 'untouched_files');
                    $this->send(SM_ROOT_DIR.'System/Cache/Pages/', 'cache_path');
                
                }else{
                
                    $this->send(false, 'show_result');
                
                }
            
            }else{
                
                SmartestLog::getInstance('site')->log("{$this->getUser()} tried to cleared the pages cache but did not have permission.", SmartestLog::ACCESS_DENIED);
                $this->addUserMessageToNextRequest('You don\'t have permission to clear the page cache for this site.', SmartestUserMessage::ACCESS_DENIED);
                $this->redirect('/smartest/pages');

            }
            
        }else{
            $this->addUserMessageToNextRequest('No site selected.', SmartestUserMessage::ERROR);
            $this->redirect('/smartest');
        }
	    
	}
  
	public function editPage($get){
		
		// $this->addUserMessage('This is a really long test message with more than one line of text.');
		// $this->addUserMessage('You are on thin ice, Mr. Gilroy-Ware.');
		
		if(!$this->requestParameterIsSet('from')){
		    $this->setFormReturnUri();
		}
		
		$page_webid = $this->getRequestParameter('page_id');
		
		$helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
		if(isset($type_index[$page_webid])){
		    if(($type_index[$page_webid] == 'ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_ITEMCLASS' || $type_index[$page_webid] == 'SM_PAGETYPE_DATASET') && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		if($page->hydrate($page_webid)){
    	    
    	    if($page->getDeleted() == 'TRUE'){
                $this->send(true, 'show_deleted_warning');
            }
            
            $page->setDraftMode(true);
    	    
    	    if(($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET') && (!$this->getRequestParameter('item_id') || !is_numeric($this->getRequestParameter('item_id')))){
            
                $this->send(true, 'allow_edit');
            
                $model = new SmartestModel;
                
                if($model->hydrate($page->getDatasetId())){
                    $items = $model->getSimpleItems($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send($page, 'page');
                    $this->send(true, 'require_item_select');
                    $this->send('Please choose an item to continue editing.', 'chooser_message');
                    $this->send('websitemanager/editPage', 'continue_action');
                }
                
                $editable = $page->isEditableByUserId($this->getUser()->getId());
        		$this->send($editable, 'page_is_editable');
            
            }else{
    	        
    	        if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
    	            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
    	                $page->setPrincipalItem($item);
    	                $this->send($item, 'item');
    	            }
	            }
    	        
    	        $this->send(false, 'require_item_select');
    	        $editorContent = $page;
    		
            	if($this->getUser()->hasToken('modify_page_properties')){
		
            		$site_id = $this->getSite()->getId();
            		$page_id = $page->getId();
		
            		if($site_id){
			
            			if($this->getSite()->getTopPageId() == $page->getId()){
            				$ishomepage = true;
            			}else{
            				$ishomepage = false;
            			}
            		}
		        
    		        $parent_pages = $page->getOkParentPages();
    		        
            		if($page->getIsHeld() == '1' && $page->getHeldBy() == $this->getUser()->getId()){
            		    $allow_release = true;
            		}else{
            		    $allow_release = false;
            		}
        		
            		$this->send($allow_release, 'allow_release');
            		$this->send($this->getUser()->hasToken('edit_page_name'), 'allow_edit_page_name');
            		$editable = $page->isEditableByUserId($this->getUser()->getId());
            		$this->send($editable, 'page_is_editable');
		            
    		        $available_icons = $page->getAvailableIconImageFilenames();
    		        
    		        $this->send($available_icons, 'available_icons');
		        
            		if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
                
                        $model = new SmartestModel;
                        
                        if($model->find($page->getDatasetId())){
                            $editorContent['model_name'] = $model->getName();
                            
                            if($page->getParent() && ($type_index[$page->getParent()] == 'ITEMCLASS' || $type_index[$page->getParent()] == 'SM_PAGETYPE_ITEMCLASS' || $type_index[$page->getParent()] == 'SM_PAGETYPE_DATASET')){
                                
                                $parent_indicator_properties = $model->getForeignKeyPropertiesForModelId($page->getParentPage(false)->getDatasetId(), (int) $this->getRequestParameter('item_id'));
                            
                                $this->send(true, 'show_parent_meta_page_property_control');
                                $this->send($model, 'model');
                                
                                if($page->getParentPage(false)->getDatasetId() == $page->getDatasetId()){
                                    
                                    // parent metapage has same model as this one
                                    $parent_model = &$model;
                                    $this->send(true, 'show_self_option');
                                    
                                }else{
                                    
                                    // quickly fetch parent meta-page's model
                                    $parent_model = new SmartestModel;
                                
                                    if($parent_model->hydrate($page->getParentPage(false)->getDatasetId())){
                                        
                                    }else{
                                        $this->addUserMessage("The parent of this page is a meta-page, but not linked to any existing model", SmartestUserMessage::WARNING);
                                    }
                                    
                                    $this->send(false, 'show_self_option');
                                    
                                }
                            
                                if(count($parent_indicator_properties) > 1){
                                    // there is a choice as to which property should be used to indicate which is the 'parent' item
                                    // convert to arrays and send to form
                                    
                                    $arrays = array();
                                    
                                    foreach($parent_indicator_properties as $p){
                                        
                                        $property_array = $p->__toArray();
                                        
                                        if($p instanceof SmartestItemPropertyValueHolder){
                                            
                                            $foreign_item = new SmartestItem;

                                            if($foreign_item->hydrate($p->getData()->getDraftContent())){
                                                $property_array['selected_item_name'] = $foreign_item->getName();
                                            }else{
                                                $property_array['selected_item_name'] = "Not Selected";
                                            }

                                        }else{
                                            $property_array['selected_item_name'] = "Unknown";
                                        }
                                        
                                        $arrays[] = $property_array;
                                        
                                    }
                                    
                                    $this->send($page->getParentMetaPageReferringPropertyId(), 'parent_data_source_property_id');
                                    $this->send('dropdown', 'parent_mpp_control_type');
                                    $this->send($arrays, 'parent_meta_page_property_options');
                                    
                                }else if(count($parent_indicator_properties) > 0){
                                    
                                    // the parent meta-page must be defined by a single foreign-key property of the model of this meta-page.
                                    // Display it, but there is no choice.
                                    
                                    if(!$page->getParentMetaPageReferringPropertyId()){
                                        $page->setParentMetaPageReferringPropertyId($parent_indicator_properties[0]->getId());
                                    }
                                    
                                    $this->send('text', 'parent_mpp_control_type');
                                    $property_array = $parent_indicator_properties[0]->__toArray();
                                    
                                    if($parent_indicator_properties[0] instanceof SmartestItemPropertyValueHolder){
                                        
                                        $foreign_item = new SmartestItem;
                                        
                                        if($foreign_item->hydrate($parent_indicator_properties[0]->getData()->getDraftContent())){
                                            $property_array['selected_item_name'] = $foreign_item->getName();
                                        }else{
                                            $property_array['selected_item_name'] = "Not Selected";
                                        }
                                        
                                    }else{
                                        $property_array['selected_item_name'] = "Unknown";
                                    }
                                    
                                    $this->send($property_array, 'parent_meta_page_property');
                                    
                                }else{
                                    
                                    // there are no properties in this meta-page that point to the data type of the parent meta-page. this is a problem so we nnotify the user.
                                    if($page->getParentPage(false)->getDatasetId() == $page->getDatasetId()){
                                        $this->addUserMessage("This ".$model->getName()." meta-page is the child of a meta-page that is also used to represent ".$model->getPluralName().", but the ".$model->getName()." model has no foreign-key properties that refer to other ".$model->getPluralName().". This page will assign its own item to it's parent meta-page.", SmartestUserMessage::WARNING);
                                        $page->setParentMetaPageReferringPropertyId('_SELF');
                                        $this->send('_SELF', 'parent_meta_page_property');
                                        $this->send('text', 'parent_mpp_control_type');
                                    }else{
                                        $this->addUserMessage("This ".$model->getName()." meta-page is the child of a meta-page used for model ".$parent_model->getName().", but the ".$model->getName()." model (that this page refers to) has no foreign-key properties that refer to ".$parent_model->getPluralName().".", SmartestUserMessage::WARNING);
                                    }
                                    
                                }
                                
                            }
                            
                        }else{
                            
                            $this->addUserMessage("This page is a meta-page, but not linked to any existing model", SmartestUserMessage::WARNING);
                            
                        }
                    }
                
            		$this->setTitle("Edit Page | ".$page->getTitle());
    		
            		$this->send($editorContent, "page");
            		$this->send($parent_pages, "parent_pages");
            		$this->send($ishomepage, "ishomepage");
            		$this->send($this->getSite(), "site");
            		$this->send(true, 'allow_edit');
		
        	    }else{
	        
        	        $this->addUserMessageToNextRequest('You don\'t have permission to modify page properties.', SmartestUserMessage::ACCESS_DENIED);
        	        $this->redirect('/smartest/pages');
        	        $this->send($editorContent, "pageInfo");
        	        $this->send(false, 'allow_edit');
	        
        	    }
        	}
	    
        }else{
            $this->addUserMessageToNextRequest('The page ID was not recognized.', SmartestUserMessage::ERROR);
            $this->redirect("/smartest/pages");
        }
		
	}
	
	function approvePageChanges($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
        $page = new SmartestPage;
        
        if($page->hydrate($page_webid)){
	    
	        if($this->getUser()->hasToken('approve_page_changes')){
	        
	            $page->setChangesApproved(1);
	            $this->addUserMessageToNextRequest("The changes to this page have been approved.", SmartestUserMessage::SUCCESS);
	            $page->save();
	        
	        }else{
	            $this->addUserMessageToNextRequest("You don't have sufficient permissions to approve pages.", SmartestUserMessage::ACCESS_DENIED);
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The page ID wasn't recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function addPlaceholder($get){
		
		$h = new SmartestAssetClassesHelper;
		$asset_class_types = $h->getTypes();
		
		$placeholder_name = SmartestStringHelper::toVarName($this->getRequestParameter('placeholder_name'));
		$selected_type = ($this->getRequestParameter('placeholder_type') && in_array($this->getRequestParameter('placeholder_type'), $h->getTypeCodes())) ? $this->getRequestParameter('placeholder_type') : 'SM_ASSETCLASS_RICH_TEXT';
		$label = ($this->getRequestParameter('placeholder_label') && strlen($this->getRequestParameter('placeholder_label'))) ? $this->getRequestParameter('placeholder_label') : SmartestStringHelper::toTitleCaseFromVarName($placeholder_name);
		
		$groups = $h->getAssetGroupsForPlaceholderType($selected_type, $this->getSite()->getId());
		
		$this->send($groups, 'groups');
		$this->send($label, 'label');
		$this->send($selected_type, 'selected_type');
		$this->send($placeholder_name, 'name');
		$this->send($asset_class_types, 'types');
		
	}
	
	public function addContainer($get){
		
		$container_name = SmartestStringHelper::toVarName($this->getRequestParameter('name'));
		$container_label = SmartestStringHelper::toTitleCaseFromVarName($container_name);
    
        $this->send($container_label, 'label');
		$this->send($container_name, 'name');
		$this->send($asset_class_types, 'types');
		
	}
	
	public function insertPlaceholder($get, $post){
		
		$placeholder = new SmartestPlaceholder;
		
		if($this->getRequestParameter('placeholder_name')){
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('placeholder_name'));
		}else{
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('placeholder_label'));
		}
		
		if($placeholder->exists($name, $this->getSite()->getId())){
	        $this->addUserMessageToNextRequest("A placeholder with the name \"".$name."\" already exists.", SmartestUserMessage::WARNING);
	    }else{
	        
		    $placeholder->setLabel($this->getRequestParameter('placeholder_label'));
		    $placeholder->setName($name);
		    $placeholder->setSiteId($this->getSite()->getId());
		    $placeholder->setType($this->getRequestParameter('placeholder_type'));
		    
		    if($this->getRequestParameter('placeholder_filegroup') == 'NONE'){
		        $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_NONE');
		    }else if(is_numeric($this->getRequestParameter('placeholder_filegroup'))){
		        $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_ASSETGROUP');
		        $placeholder->setFilterValue($this->getRequestParameter('placeholder_filegroup'));
		    }
		    
		    $placeholder->save();
		    $this->addUserMessageToNextRequest("A new container with the name \"".$name."\" has been created.", SmartestUserMessage::SUCCESS);
		}
		
		$this->formForward();
	}
	
	public function insertContainer($get, $post){
		
		if($this->getRequestParameter('container_name')){
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('container_name'));
		}else{
		    $name = SmartestStringHelper::toVarName($this->getRequestParameter('container_label'));
		}
		
		$container = new SmartestContainer;
		
		if($container->exists($name, $this->getSite()->getId())){
	        $this->addUserMessageToNextRequest("A container with the name \"".$name."\" already exists.", SmartestUserMessage::WARNING);
	    }else{
		    $container->setLabel($this->getRequestParameter('container_label'));
		    $container->setName($name);
		    $container->setSiteId($this->getSite()->getId());
		    $container->setType('SM_ASSETCLASS_CONTAINER');
		    $container->save();
		    $this->addUserMessageToNextRequest("A new container with the name \"".$name."\" has been created.", SmartestUserMessage::SUCCESS);
	    }
		
		$this->formForward();
	}
	
    public function placeholders(){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('placeholders');
	    
	    $placeholders = $this->getSite()->getPlaceholders();
	    $this->send($placeholders, 'placeholders');
	    
	}
	
	public function editPlaceholder($get){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        $this->send($placeholder, 'placeholder');
	        $this->send($placeholder->getPossibleFileGroups($this->getSite()->getId()), 'possible_groups');
	        $definitions = $placeholder->getDefinitions(true, $this->getSite()->getId());
	        $this->send((count($definitions) == 0), 'allow_type_change');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The placeholder ID wasn't recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function placeholderDefinitions($get){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        $mode = ($this->getRequestParameter('mode') && $this->getRequestParameter('mode') == 'live') ? "live" : "draft";
	        
	        $draft_mode = ($mode == "draft");
	        
	        $definitions = $placeholder->getDefinitions($draft_mode, $this->getSite()->getId());
	        
	        $this->send($placeholder, 'placeholder');
	        $this->send($definitions, 'definitions');
	        $this->send($mode, 'mode');
	    
	    }
	    
	}
	
	public function updatePlaceholder($get, $post){
	    
	    $placeholder_id = (int) $this->getRequestParameter('placeholder_id');
	    $placeholder = new SmartestPlaceholder;
	    
	    if($placeholder->find($placeholder_id)){
	        
	        $placeholder->setLabel($this->getRequestParameter('placeholder_label'));
	        
	        if($this->getRequestParameter('placeholder_filter')){
	            if($this->getRequestParameter('placeholder_filter') == 'NONE'){
	                $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_NONE');
	                $placeholder->setFilterValue('');
	            }else{
	                $placeholder->setFilterType('SM_ASSETCLASS_FILTERTYPE_ASSETGROUP');
	                $placeholder->setFilterValue($this->getRequestParameter('placeholder_filter'));
	            }
	        }
	        
	        $placeholder->save();
	        $this->addUserMessageToNextRequest("The placeholder was updated.", SmartestUserMessage::SUCCESS);
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The placeholder ID wasn't recognized.", SmartestUserMessage::ERROR);
	        
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function movePageUp($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
	    $page = new SmartestPage();
	    $page->setDraftMode(true);
	    
	    if($page->hydrateBy('webid', $page_webid)){
	        $page->moveUp();
	        // $this->addUserMessageToNextRequest("The page has been moved up.", SmartestUserMessage::SUCCESS);
	        SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
	    }else{
	        $this->addUserMessageToNextRequest("The page ID wasn't recognised.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	}
	
	public function movePageDown($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
	    $page = new SmartestPage();
	    $page->setDraftMode(true);
	    
	    if($page->hydrateBy('webid', $page_webid)){
	        $page->moveDown();
	        // $this->addUserMessageToNextRequest("The page has been moved down.", SmartestUserMessage::SUCCESS);
	        SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
	    }else{
	        $this->addUserMessageToNextRequest("The page ID wasn't recognised.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	}
	
	function preview($get){
		
		// if(!$this->getRequestParameter('from')){
		    $this->setFormReturnUri();
		    $this->setFormReturnDescription('page preview');
	    // }
		
		$content = array();
		
		$page_webid = $this->getRequestParameter('page_id');
		$page = new SmartestPage;
		
		if($page->hydrate($page_webid)){
		    
		    $this->send($page, 'page');
		    
		    $domain = 'http://'.$page->getParentSite()->getDomain();
		    
		    if(!SmartestStringHelper::endsWith('/', $domain)){
		        $domain .= '/';
		    }
		    
		    if($page->getDraftTemplate() && is_file(SM_ROOT_DIR.'Presentation/Masters/'.$page->getDraftTemplate())){
		    
		        if($page->getType() == 'NORMAL'){
		        
    		        $this->send(true, 'show_iframe');
    		        $this->send($domain, 'site_domain');
    		        $this->setTitle('Page Preview | '.$page->getTitle());
    		        $this->send(false, 'show_edit_item_option');
                    $this->send(false, 'show_publish_item_option');
                    // $this->send($page->getMasterTemplate()->getImportedStylesheets(), 'stylesheets');
                    $this->send($page->getStylesheets(), 'stylesheets');
		        
    		    }else if($page->getType() == 'ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_ITEMCLASS' || $page->getType() == 'SM_PAGETYPE_DATASET'){
		        
    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
    		            $item_id = $this->getRequestParameter('item_id');
		            
    		            $item = SmartestCmsItem::retrieveByPk($item_id);
		            
    		            if(is_object($item)){
    		                $this->send($item, 'item');
    		                $this->send(true, 'show_iframe');
    		                $this->send($page->getMasterTemplate()->getImportedStylesheets(), 'stylesheets');
    		                
    		                $this->send($domain, 'site_domain');
    		                $this->setTitle('Meta-Page Preview | '.$item->getName());
    		                
    		                if(($this->getUser()->hasToken('publish_approved_items') && $item->isApproved() == 1) || $this->getUser()->hasToken('publish_all_items')){
                    	        $this->send(true, 'show_publish_item_option');
                    	    }else{
                    	        $this->send(false, 'show_publish_item_option');
                    	    }
                    	    
                    	    if($this->getUser()->hasToken('modify_items')){
                    	        $this->send(true, 'show_edit_item_option');
                    	    }else{
                    	        $this->send(true, 'show_false_item_option');
                    	    }
    		                
    		            }else{
		                    
		                    $this->send(false, 'show_edit_item_option');
		                    $this->send(false, 'show_publish_item_option');
		                    
    		                $this->send(false, 'show_iframe');
		                
    		                /* $set = new SmartestCmsItemSet;

        	                if($set->hydrate($page->getDatasetId())){

        	                    $items = $set->getMembersAsArrays(true);
        	                    $this->send($items, 'set_members');
        	                    $this->addUserMessage("Please choose an item to preview this page.");
        	                    $this->send(true, 'show_item_list');

        	                } */
                            
                            $this->send(true, 'show_item_list');

        	                $model = new SmartestModel;

        	                /* if($model->hydrate($page->getDatasetId())){
        	                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
        	                    $this->send($items, 'items');
        	                    $this->send($model->__toArray(), 'model');
        	                }else{
        	                    $this->send(array(), 'items');
        	                } */
        	                
        	                if($model->hydrate($page->getDatasetId())){
	                            $items = $model->getSimpleItemsAsArrays($this->getSite()->getId());
	                            $this->send($items, 'items');
	                            $this->send($model, 'model');
	                            $this->send($page, 'page');
	                        }else{
	                            $this->send(array(), 'items');
	                        }
        	                
        	                $this->setTitle('Meta-Page Preview | Choose '.$model->getName().' to Continue');
    		            }
		            
    	            }else{
	                
    	                $this->send(false, 'show_iframe');
	                
    	                $this->send(true, 'show_item_list');
	                
    	                $model = new SmartestModel;
	                
    	                if($model->hydrate($page->getDatasetId())){
    	                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
    	                    $this->send($items, 'items');
    	                    $this->send($model, 'model');
    	                    $this->send('Please choose an item to preview on this page.', 'chooser_message');
                            $this->send('websitemanager/preview', 'continue_action');
    	                }else{
    	                    $this->send(array(), 'items');
    	                }
    	                
    	                $this->setTitle('Meta-Page Preview | Choose '.$model->getName().' to Continue');
    	            }
    		    }    	    
    	    }else{
    	        
    	        $this->send(false, 'show_iframe');
    	        $this->addUserMessage("The preview of this page cannot be displayed because no master template is chosen.", SmartestUserMessage::WARNING);
    	        
    	    }
		    
		    if($this->getUser()->hasToken('approve_page_changes') && $page->getChangesApproved() != 1){
    	        $this->send(true, 'show_approve_button');
    	    }else{
    	        $this->send(false, 'show_approve_button');
    	    }
    	    
    	    if(($this->getUser()->hasToken('publish_approved_pages') && $page->getChangesApproved() == 1) || $this->getUser()->hasToken('publish_all_pages')){
    	        $this->send(true, 'show_publish_button');
    	    }else{
    	        $this->send(false, 'show_publish_button');
    	    }
    	    
    	    $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
    	    $this->send(($page->getIsHeld() && $page->getHeldBy() == $this->getUser()->getId()), 'show_release_page_option');
		    
		}else{
		    $this->addUserMessage("The page ID was not recognized.", SmartestUserMessage::ERROR);
		    $this->send(false, 'show_iframe');
		}
		
		/* if($content["page"] = $this->manager->getPage($page_id)){
			return $content;
		}else{
			return array("page"=>array());
		}*/
	}
	
	public function pageComments($get){
	    
	    $id = $this->getRequestParameter('page_id');
	    $page = new SmartestPage;
		
		if($page->hydrate($id)){
		    $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
		}else{
		    $this->addUserMessage('The page ID has not been recognized.', SmartestUserMessage::ERROR);
		}
		
	}
	
	function deletePage($get){
		
		$id = $this->getRequestParameter('page_id');
		/* $sql = "UPDATE Pages SET page_deleted='TRUE' WHERE Pages.page_webid='$id'";
		$id = $this->database->rawQuery($sql);
		$title = $this->database->specificQuery('page_title', 'page_id', $id, 'Pages'); */
		
		if($this->getUser()->hasToken('remove_pages')){
		
		    $page = new SmartestPage;
		
    		if($page->hydrate($id)){
		    
    		    // retrieve site id for cache deletion
    		    $site_id = $page->getSiteId();
		    
    		    // set the page to deleted and save
    		    $page->setDeleted('TRUE');
    		    $page->save();
		    
    		    // clear cache
    		    SmartestCache::clear('site_pages_tree_'.$site_id, true);
		    
    		    // make sure user is notified
    		    $this->addUserMessageToNextRequest("The page has been successfully moved to the trash.", SmartestUserMessage::SUCCESS);
		    
    		    // log deletion
        		SmartestLog::getInstance('site')->log("Page '".$title."' was deleted by user '".$this->getUser()->getUsername()."'", SmartestLog::USER_ACTION);
		    
    		}else{
    		    $this->addUserMessageToNextRequest("The page ID was not recognized.", SmartestUserMessage::ERROR);
    		}
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have sufficient permissions to delete pages.", SmartestUserMessage::ACCESS_DENIED);
	        
	    }
		
		// forward
		$this->formForward();
	}
	
	public function sitePages($get){
		
		$this->requireOpenProject();
		$this->setFormReturnUri();
		$this->setFormReturnDescription('site tree');

        $site_id = $this->getSite()->getId();
        
        $pagesTree = $this->getSite()->getPagesTree(true);
        
        if($this->getRequestParameter('refresh') == 1){
            SmartestCache::clear('site_pages_tree_'.$site_id, true);
        }
        
        $this->setTitle($this->getSite()->getName()." | Site Tree");
        
        $this->send($pagesTree, "tree");
        $this->send($site_id, "site_id");
        $this->send(true, "site_recognised");
        
        $recent = $this->getUser()->getRecentlyEditedPages($this->getSite()->getId());
	    $this->send($recent, 'recent_pages');
		    
	}
	
	public function releaseCurrentUserHeldPages(){
	    
	    $this->requireOpenProject();
	    
	    $num_held_pages = $this->getUser()->getNumHeldPages($this->getSite()->getId());
	    $this->getUser()->releasePages($this->getSite()->getId());
	    $this->addUserMessageToNextRequest($num_held_pages." pages were released.", SmartestUserMessage::SUCCESS);
	    $this->redirect('/smartest/pages');
	    
	}
	
	public function addPage($get, $post){
		
		$this->requireOpenProject();
		
		$user_id = $this->getUser()->getId(); //['user_id'];
		
		$helper = new SmartestPageManagementHelper;
		
		if($this->getRequestParameter('stage') && is_numeric($this->getRequestParameter('stage')) && is_object(SmartestSession::get('__newPage'))){
		    $stage = $this->getRequestParameter('stage');
		}else{
		    $stage = 1;
		}
		
		// echo $this->getRequestParameter('stage');
		
		if($this->getSite() instanceof SmartestSite){
		    $site_id = $this->getSite()->getId();
		    $site_info = $this->getSite();
		}else{
		    $this->addUserMessageToNextRequest("You must have chosen a site to work on before adding pages.", SmartestUserMessage::INFO);
		    $this->redirect("/smartest");
		}
		
		/* if($this->getRequestParameter('page_id')){
			$page_id = $this->getRequestParameter('page_id');
			$parent = new SmartestPage;
			$parent->hydrate($page_id);
			$parent_info = $parent;
		}else{
		    if(is_object(SmartestSession::get('__newPage')) && SmartestSession::get('__newPage')->getParent()){
			    $parent = new SmartestPage;
			    $parent->hydrate(SmartestSession::get('__newPage')->getParent());
			    $parent_info = $parent;
			}
		} */
		
		// $templates = $helper->getMasterTemplates($site_id);
		$tlh = new SmartestTemplatesLibraryHelper;
		$templates = $tlh->getMasterTemplates($this->getSite()->getId());

		switch($stage){
			
			////////////// STAGE 2 //////////////
			
			case "2":
			
			if(!$this->getRequestParameter('page_title')){
			    $this->setRequestParameter('stage', 1);
			    $p = new SmartestPage;
			    if($p->find($this->getRequestParameter('page_parent'))){
			        $this->setRequestParameter('page_id', $p->getWebId());
		        }
		        $this->addUserMessage("You must enter a title for your new page", SmartestUserMessage::WARNING);
		        $this->forward('websitemanager', 'addPage');
			}else{
			    SmartestSession::get('__newPage')->setTitle(htmlentities($this->getRequestParameter('page_title'), ENT_COMPAT, 'UTF-8'));
			}
			
			$type = in_array($this->getRequestParameter('page_type'), array('NORMAL', 'ITEMCLASS', 'LIST', 'TAG')) ? $this->getRequestParameter('page_type') : 'NORMAL';
			$this->send($this->getRequestParameter('page_parent'), 'page_parent');
			
			SmartestSession::get('__newPage')->setParent($this->getRequestParameter('page_parent'));
			$suggested_url = SmartestSession::get('__newPage')->getStrictUrl();
			
			if(!$this->getSite()->urlExists($suggested_url)){
			    $this->send($suggested_url, 'suggested_url');
			}
			
			$page_presets = $helper->getPagePresets($this->getSite()->getId());
			
			$template = "addPage.stage2.tpl";
			
			if(!SmartestSession::get('__newPage')->getType()){
				SmartestSession::get('__newPage')->setType(strtoupper($type));
			}
			
			$this->send((bool) SmartestSession::get('__newPage_preset_id'), 'disable_template_dropdown');
			
			$pages = $helper->getSerialisedPageTree($helper->getPagesTree($site_info['id']));
			$this->send('TRUE', 'chooseParent');
			$this->send($pages, 'pages');
			
			if(!SmartestSession::get('__newPage')->getCacheAsHtml()){
			    SmartestSession::get('__newPage')->setCacheAsHtml('TRUE');
			}
			
			$page_type = SmartestSession::get('__newPage')->getType();
			
			if($page_type == 'ITEMCLASS'){
				
				$this->send($this->getSite()->getModels(), 'models');
				
			}else if(SmartestSession::get('__newPage')->getType() == 'TAG'){
			    
			    $du = new SmartestDataUtility;
			    $tags = $du->getTagsAsArrays();
			    $this->send($tags, 'tags');
			    
			}
			
			$this->send($parent_info, 'parentInfo');
 			$this->send($site_info, 'siteInfo');
 			
 			$this->send($templates, 'templates');
 			$this->send($page_presets, 'presets');
 			
 			$newPage = SmartestSession::get('__newPage');
 			
 			$preset = new SmartestPagePreset;
 			
 			if($preset_id = SmartestSession::get('__newPage_preset_id') && $preset->hydrate(SmartestSession::get('__newPage_preset_id'))){
 			    $newPage['preset'] = $preset->getId();
 			    $newPage['draft_template'] = $preset->getMasterTemplateName();
		    }else{
		        $newPage['preset'] = '';
		    }
            
            $this->send($newPage, 'newPage');
			
			break;
			
			////////////// STAGE 3 //////////////
			
			case "3":
			
			// verify the page details
			if($this->getRequestParameter('page_title') && SmartestSession::get('__newPage')->getTitle() == "Untitled Page"){
			    SmartestSession::get('__newPage')->setTitle($this->getRequestParameter('page_title'));
			}
			
			SmartestSession::get('__newPage')->setName(strlen(SmartestSession::get('__newPage')->getTitle()) ? SmartestStringHelper::toSlug(SmartestSession::get('__newPage')->getTitle()) : SmartestStringHelper::toSlug('Untitled Smartest Web Page'));
			SmartestSession::get('__newPage')->setCacheAsHtml($this->getRequestParameter('page_cache_as_html'));
			SmartestSession::get('__newPage')->setCacheInterval($this->getRequestParameter('page_cache_interval'));
			SmartestSession::get('__newPage')->setIsPublished('FALSE');
			SmartestSession::get('__newPage')->setChangesApproved(0);
			SmartestSession::get('__newPage')->setSearchField(htmlentities(strip_tags($this->getRequestParameter('page_search_field')), ENT_COMPAT, 'UTF-8'));
			
			if(strlen($this->getRequestParameter('page_url')) && substr($this->getRequestParameter('page_url'), 0, 18) != 'website/renderPage'){
			    SmartestSession::get('__newPage')->addUrl($this->getRequestParameter('page_url')); 
			    $url = $this->getRequestParameter('page_url');
		    }else{
		        
		        if(SmartestSession::get('__newPage')->getType() == 'ITEMCLASS'){
		            // $default_url = 'website/renderPageFromId?page_id='.SmartestSession::get('__newPage')->getWebId().'&item_id=:long_id';
		            // SmartestSession::get('__newPage')->getWebId().'.html');
	            }else{
	                // $default_url = SmartestSession::get('__newPage')->getWebId().'.html';
	            }
	            
		    } 
			
			SmartestSession::get('__newPage')->setDraftTemplate($this->getRequestParameter('page_draft_template'));
			SmartestSession::get('__newPage')->setDescription(strip_tags($this->getRequestParameter('page_description')));
			SmartestSession::get('__newPage')->setMetaDescription(strip_tags($this->getRequestParameter('page_meta_description')));
			SmartestSession::get('__newPage')->setKeywords(strip_tags($this->getRequestParameter('page_keywords')));
			
			if($this->getRequestParameter('page_id')){
				SmartestSession::get('__newPage')->setParent($this->getRequestParameter('page_id'));
			}
			
			if($this->getRequestParameter('page_preset')){
				SmartestSession::set('__newPage_preset_id', $this->getRequestParameter('page_preset'));
			}
			
			if($this->getRequestParameter('page_model')){
				SmartestSession::get('__newPage')->setDatasetId($this->getRequestParameter('page_model'));
				$model = new SmartestModel;
				$model->hydrate($this->getRequestParameter('page_model'));
			}
			
			if($this->getRequestParameter('page_tag')){
				SmartestSession::get('__newPage')->setDatasetId($this->getRequestParameter('page_tag'));
				$tag = new SmartestTag;
				$tag->hydrate($this->getRequestParameter('page_tag'));
			}
			
			$type_template = strtolower(SmartestSession::get('__newPage')->getType());
			
			$newPage = SmartestSession::get('__newPage')->__toArray();
			
			$urlObj = new SmartestPageUrl;
			
			if(isset($url) && !$urlObj->hydrateBy('url', $url)){
			    $newPage['url'] = $url;
		    }else{
		        $newPage['url'] = $this->getRequest()->getDomain().'website/renderPageById?page_id='.SmartestSession::get('__newPage')->getWebid();
		    }
			
			// should the page have a preset?
            if($preset_id = SmartestSession::get('__newPage_preset_id')){
                
                $preset = new SmartestPagePreset;
                
                // if so, apply those definitions
                if($preset->find($preset_id)){
                    SmartestSession::get('__newPage')->setDraftTemplate($preset->getMasterTemplateName());
                    $newPage['preset_label'] = $preset->getLabel();
    				$newPage['draft_template'] = SmartestSession::get('__newPage')->getDraftTemplate();
                }
            }
			
			/* if(SmartestSession::get('__newPage')->getPreset()){
				
				$newPage['preset'] = SmartestSession::get('__newPage')->getPreset();
				$preset = new SmartestPagePreset;
				$preset->hydrate(SmartestSession::get('__newPage')->getPreset());
				// SmartestSession::get('__newPage')->setPresetLabel($preset->getLabel());
				SmartestSession::get('__newPage')->setDraftTemplate($preset->getMasterTemplateName());
				$newPage['preset_label'] = SmartestSession::get('__newPage')->getPresetLabel();
				$newPage['draft_template'] = SmartestSession::get('__newPage')->getDraftTemplate();
				
			} */
			
			// print_r($newPage);
			
 			$this->send($newPage, 'newPage');
			
			$template = "addPage.stage3.tpl";
			break;
			
			
			////////////// DEFAULT //////////////
			
			default:
			
			if($this->getRequestParameter('page_id')){
    			
    			$page_id = $this->getRequestParameter('page_id');
    			$parent = new SmartestPage;
    			
    			if($parent->findby('webid', $page_id)){
    			    $this->send($parent, 'parent_page');
			    }
			    
    		}else{
    		    
    		    if(is_object(SmartestSession::get('__newPage')) && SmartestSession::get('__newPage')->getParent()){
    			    $parent = new SmartestPage;
    			    $parent->find(SmartestSession::get('__newPage')->getParent());
    			    $this->send($parent, 'parent_page');
    			}else{
    			    // fetch list of site pages for dropdown
    			    $parent_pages = $this->getSite()->getPagesList(true);
    			    $this->send($parent_pages, 'parent_pages');
    			}
    		}
			
			/* $parent = new SmartestPage;
			
			if($this->getRequestParameter('page_id') && !isset($site_id)){
			    
			    $parent_id = $this->getRequestParameter('page_id');
			    $parent->findBy('webid', $parent_id);
				
				$site_id = $parent->getSiteId();
				
			} */
			
			$type = 'start';
			
			SmartestSession::set('__newPage', new SmartestPage);
			SmartestSession::get('__newPage')->setWebId(SmartestStringHelper::random(32));
			SmartestSession::get('__newPage')->setCreatedbyUserid($user_id);
			SmartestSession::get('__newPage')->setSiteId($site_info['id']);
			SmartestSession::get('__newPage')->setParent($parent_info['id']);
			
			$template = "addPage.start.tpl";
			
			break;
		}
		
		$this->send($template, "_stage_template");
		
		$this->setTitle("Create A New Page");
		
 		
 		
	}
	
	public function insertPage($get, $post){
	    
	    if($this->getSite() instanceof SmartestSite){
	        
	        if(SmartestSession::get('__newPage') instanceof SmartestPage){
	            
	            $page =& SmartestSession::get('__newPage');
	            
	            $page->setOrderIndex($page->getParentPage()->getNextChildOrderIndex());
	            $page->setCreated(time());
	            
	            $page->save();
	            
	            if($page->getType() == 'NORMAL'){
	                $page->addAuthorById($this->getUser()->getId());
                }
	            
	            // should the page have a preset?
	            if($preset_id = SmartestSession::get('__newPage_preset_id')){
	                
	                $preset = new SmartestPagePreset;
	                // if so, apply those definitions
	                if($preset->hydrate($preset_id)){
	                    $preset->applyToPage($page);
	                }
	            }
	            
	            $page_webid = $page->getWebId();
    		    $site_id = $page->getSiteId();
    		    
    		    // clear session and cached page tree
    		    SmartestCache::clear('site_pages_tree_'.$site_id, true);
	            SmartestSession::clear('__newPage');
	    
	            switch($this->getRequestParameter('destination')){
			
        			case "SITEMAP":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/smartest/pages');
        			break;
			
        			case "ELEMENTS":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/websitemanager/pageAssets?page_id='.$page_webid);
        			break;
			
        			case "EDIT":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/websitemanager/openPage?page_id='.$page_webid);
        			break;
			
        			case "PREVIEW":
        			$this->addUserMessageToNextRequest("Your page was successfully added.", SmartestUserMessage::SUCCESS);
        			$this->redirect('/websitemanager/preview?page_id='.$page_webid);
    			    break;
    			
    		    }
    		
		    }else{
		        
		        $this->addUserMessageToNextRequest("The new page expired from the session.", SmartestUserMessage::WARNING);
    		    $this->redirect('/smartest');
		        
		    }
		
		}else{
		    
		    $this->addUserMessageToNextRequest("You must select a site before adding pages.", SmartestUserMessage::INFO);
		    $this->redirect('/smartest');
		    
		}
		
	}
	
	public function updatePage($get, $post){    
        
        $page = new SmartestPage;
        
        if($page->hydrate($this->getRequestParameter('page_id'))){
            
            $page->setTitle($this->getRequestParameter('page_title'));
            
            if($this->getRequestParameter('page_name') && strlen($this->getRequestParameter('page_name')) && $this->getUser()->hasToken('edit_page_name')){
                $page->setName(SmartestStringHelper::toSlug($this->getRequestParameter('page_name')));
            }
            
            $page->setParent($this->getRequestParameter('page_parent'));
            $page->setForceStaticTitle(($this->getRequestParameter('page_force_static_title') && ($this->getRequestParameter('page_force_static_title') == 'true')) ? 1 : 0);
            $page->setIsSection(($this->getRequestParameter('page_is_section') && ($this->getRequestParameter('page_is_section') == 'true')) ? 1 : 0);
            $page->setCacheAsHtml($this->getRequestParameter('page_cache_as_html'));
            $page->setCacheInterval($this->getRequestParameter('page_cache_interval'));
            $page->setIconImage($this->getRequestParameter('page_icon_image'));
            
            if($page->getType() == 'NORMAL'){
                $page->setSearchField(strip_tags($this->getRequestParameter('page_search_field')));
                $page->setKeywords(strip_tags($this->getRequestParameter('page_keywords')));
                $page->setDescription(strip_tags($this->getRequestParameter('page_description')));
                $page->setMetaDescription(strip_tags($this->getRequestParameter('page_meta_description')));
            }
            
            if($page->getType() == 'ITEMCLASS'){
                if($this->getRequestParameter('page_parent_data_source') && strlen($this->getRequestParameter('page_parent_data_source'))){
                    $page->setParentMetaPageReferringPropertyId($this->getRequestParameter('page_parent_data_source'));
                }
            }
            
            $page->save();
            SmartestCache::clear('site_pages_tree_'.$page->getSiteId(), true);
            $this->addUserMessageToNextRequest('The page was successfully updated.', SmartestUserMessage::SUCCESS);
            
        }else{
            $this->addUserMessageToNextRequest('There was an error updating page ID '.$this->getRequestParameter('page_id').'.', SmartestUserMessage::ERROR);
        }
        
		$this->formForward();

	}

	public function pageAssets($get){
	    
	    if($this->getUser()->hasToken('modify_draft_pages')){
	        
	        $page_webid = $this->getRequestParameter('page_id');
	        
	        $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());

    		if(isset($type_index[$page_webid])){
    		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
    		        $page = new SmartestItemPage;
    		    }else{
    		        $page = new SmartestPage;
    		    }
    		}else{
    		    $page = new SmartestPage;
    		}
    		
    		if($page->hydrate($page_webid)){
	            
	            if($page->getDeleted() == 'TRUE'){
	                $this->send(true, 'show_deleted_warning');
	            }
	            
	            $editable = $page->isEditableByUserId($this->getUser()->getId());
        		$this->send($editable, 'page_is_editable');
	            
	            if($page->getType() == 'ITEMCLASS' && (!$this->getRequestParameter('item_id') || !is_numeric($this->getRequestParameter('item_id')))){
	            
    	            $model = new SmartestModel;
            
                    if($model->hydrate($page->getDatasetId())){
                        $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
                        $this->send($items, 'items');
                        $this->send($model, 'model');
                        $this->send('Please choose an item to edit the elements on this page.', 'chooser_message');
                        $this->send('websitemanager/pageAssets', 'continue_action');
                        $this->send(true, 'allow_edit');
                        $this->send($page, 'page');
                    }else{
                        $this->send(array(), 'items');
                    }
                
                    $this->send(true, 'require_item_select');
	            
    	        }else{
	                
	                $this->send(false, 'require_item_select');
	                
	                if($page->getType() == 'ITEMCLASS'){
        	            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
        	                
        	                $page->setPrincipalItem($item);
        	                $recent_items = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $item->getItem()->getItemclassId());
        	                $model = $item->getItem()->getModel();
        	                $metapages = $item->getItem()->getModel()->getMetaPages();
        	                
        	                $default_metapage_id = $item->getItem()->getMetapageId();
        	                
        	                if($default_metapage_id){
        	                    if($default_metapage_id != $page->getId()){
        	                        $page = new SmartestPage;
            	                    $page->find($default_metapage_id);
            	                    $default_metapage_webid = $page->getWebId();
            	                    $this->send($default_metapage_webid, 'default_metapage_webid');
        	                        $this->send(true, 'show_metapage_warning');
        	                    }else{
        	                        $this->send(false, 'show_metapage_warning');
    	                        }
        	                }else{
        	                    $this->send(false, 'show_metapage_warning');
        	                }
        	                
        	                $this->send((bool) count($metapages), 'show_metapages');
        	                $this->send($metapages, 'metapages');
        	                $this->send($recent_items, 'recent_items');
        	                $this->send($model, 'model');
        	                $this->send(true, 'show_recent_items');
        	                $this->send($item, 'item');
        	            }
    	            }
	                
    		        $this->setFormReturnUri();
    		        $this->setFormReturnDescription('page elements tree');
		
            		$version = ($this->getRequestParameter('version') && $this->getRequestParameter('version') == "live") ? "live" : "draft";
            		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		            
		            if($page->getType() == 'ITEMCLASS'){
        		        $assetClasses = $this->manager->getPageTemplateAssetClasses($this->getRequestParameter('page_id'), $version, $item->getId());
    		        }else{
    		            $assetClasses = $this->manager->getPageTemplateAssetClasses($this->getRequestParameter('page_id'), $version);
    		        }
            		
            		$site_id = $this->getSite()->getId();
            		$tlh = new SmartestTemplatesLibraryHelper;
            		$templates = $tlh->getMasterTemplates($this->getSite()->getId());
            		
            		$this->setTitle("Page Elements");
    		
    		        if($version == 'live'){
            		    $template_name = $page->getLiveTemplate();
            		}else{
            		    $template_name = $page->getDraftTemplate();
            		}
            		
            		$template_object = $tlh->hydrateMasterTemplateByFileName($template_name, $this->getSite()->getId());
            		
            		$this->send((!$tlh->getMasterTemplateHasBeenImported($page->getDraftTemplate()) && $version == 'draft' && strlen($page->getDraftTemplate())), 'show_template_warning');
    		
            		if($page->getIsHeld() == '1' && $page->getHeldBy() == $this->getUser()->getId()){
            		    $allow_release = true;
            		}else{
            		    $allow_release = false;
            		}
    		
            		$this->send($allow_release, 'allow_release');
		
            		$mode = 'advanced';
    		
            		$sub_template = "getPageAssets.advanced.tpl";
		            
		            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
            		$this->send($assetClasses["tree"], "assets");
            		$this->send($definedAssets, "definedAssets");
            		$this->send($page, "page");
            		$this->send($template_object, "page_template");
            		$this->send($templates, "templates");
            		$this->send($template_name, "templateMenuField");
            		$this->send($site_id, "site_id");
            		$this->send($version, "version");
            		$this->send($sub_template, "sub_template");
            		$this->send(true, 'allow_edit');
    		
    		    }
		    
	        }
		
	    }else{
	        
	        $this->addUserMessage('You don\'t have permission to modify pages.', SmartestUserMessage::ACCESS_DENIED);
	        $this->send(false, 'allow_edit');
	        
	    }
	}
	
	public function pageTags($get){
	    
	    $this->setFormReturnUri();
	    
	    $this->setTitle('Page Tags');
	    
	    $page_id = $this->getRequestParameter('page_id');
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        if($page->getType() == 'ITEMCLASS'){
	            
	            // Page is an Object meta page - force them to pick a specific item
	            $this->send(false, 'show_tags');
	            
	            $model = new SmartestModel;

                if($model->hydrate($page->getDatasetId())){
                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model->__toArray(), 'model');
                    $this->send('Please choose which '.$model->getName().' you would like to tag:', 'chooser_message');
                    $this->send('datamanager/itemTags', 'continue_action');
                }else{
                    $this->send(array(), 'items');
                }
                
                $this->send($page->__toArray(), 'page');
                
                $this->setTitle('Meta-Page Tags | Choose '.$model->getName().' to Continue');
	            
	        }else{
	            
	            // Page is a normal web page
	            $du  = new SmartestDataUtility;
	            $tags = $du->getTags();
	        
	            $page_tags = array();
	            $i = 0;
	        
	            foreach($tags as $t){
	            
	                $page_tags[$i] = $t->__toArray();
	            
	                if($t->hasPage($page->getId())){
	                    $page_tags[$i]['attached'] = true;
	                }else{
	                    $page_tags[$i]['attached'] = false;
	                }
	            
	                $i++;
	            }
	            
	            $tag_ids = $page->getTagIdsArray();
	            
	            $this->send($tag_ids, 'used_tags_ids');
	            $this->send($tags, 'tags');
	            $this->send(true, 'show_tags');
	            $this->send($page, 'page');
	            
	            $this->setTitle($page->getTitle().' | Tags');
	        
            }
            
            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
	        
	    }else{
	        $this->addUserMessage('The page ID has not been recognized.', SmartestUserMessage::ERROR);
	    }
	    
	}
	
	public function updatePageTags($get, $post){
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($this->getRequestParameter('page_id'))){
	    
	        $du  = new SmartestDataUtility;
            $tags = $du->getTags();
        
            if(is_array($this->getRequestParameter('tags'))){
                
                $page_new_tag_ids = array_keys($this->getRequestParameter('tags'));
                $page_current_tag_ids = $page->getTagIdsArray();
                
                foreach($tags as $t){
                    
                    if(in_array($t->getId(), $page_new_tag_ids) && !in_array($t->getId(), $page_current_tag_ids)){
                        $page->tag($t->getId());
                    }
                    
                    if(in_array($t->getId(), $page_current_tag_ids) && !in_array($t->getId(), $page_new_tag_ids)){
                        $page->untag($t->getId());
                    }
                    
                }
                
                $this->addUserMessageToNextRequest('The tags on this page were successfully updated.', SmartestUserMessage::SUCCESS);
                
            }else{
                // clear all page tags
                $page->clearTags();
                $this->addUserMessageToNextRequest('The tags on this page were successfully removed.', SmartestUserMessage::SUCCESS);
            }
        
        }else{
            
            // page ID wasn't recognised
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
            
        }
	    
	    $this->formForward();
	    
	}
	
	public function relatedContent($get){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_webid)){
	        
	        $this->setFormReturnUri();
	        
	        if($page->getType() == 'ITEMCLASS'){
	            
	            $model = new SmartestModel;
            
                if($model->hydrate($page->getDatasetId())){
                    $items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send('Please choose an item to attache related content.', 'chooser_message');
                    $this->send('datamanager/relatedContent', 'continue_action');
                    $this->send($page, 'page');
                }else{
                    $this->send(array(), 'items');
                }
                
                $this->send(true, 'require_item_select');
	            
	        }else{
	        
	            $this->setTitle($page->getTitle()." | Related Content");
    	        $related_pages = $page->getRelatedPages();
	        
    	        $du = new SmartestDataUtility;
    	        $models = $du->getModels(false, $this->getSite()->getId());
	        
    	        foreach($models as &$m){
    	            $m['related_items'] = $page->getRelatedItems($m['id'], true);
    	        }
	        
    	        $this->send($page, 'page');
    	        $this->send($related_pages, 'related_pages');
        	    $this->send($models, 'models');
        	    $this->send(false, 'require_item_select');
    	    
	        }
	        
	        $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
    	    
	    }else{
	        $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/pages');
	    }
	    
	}
	
	public function editRelatedContent($get){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_webid)){
	        
	        if($this->getRequestParameter('model_id')){
	            
	            $model_id = (int) $this->getRequestParameter('model_id');
	            $model = new SmartestModel;
	            
	            if($model->hydrate($model_id)){
	                $mode = 'items';
	            }else{
	                $mode = 'pages';
	            }
            }
	        
	        $this->send($mode, 'mode');
	        
	        if($mode == 'items'){
	            $this->setTitle($page->getTitle()." | Related ".$model->getPluralName());
	            $this->send($page->__toArray(), 'page');
	            $this->send($model->__toArray(), 'model');
	            $related_ids = $page->getRelatedItemIds($model->getId());
	            $all_items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
	            $this->send($all_items, 'items');
	            $this->send($related_ids, 'related_ids');
            }else{
                $this->setTitle($page->getTitle()." | Related pages");
    	        $this->send($page->__toArray(), 'page');
    	        $related_ids = $page->getRelatedPageIds(true);
    	        $helper = new SmartestPageManagementHelper;
    	        $pages = $helper->getPagesList($this->getSite()->getId());
    	        $this->send($pages, 'pages');
    	        $this->send($related_ids, 'related_ids');
            }
	        
	        $related_pages = $page->getRelatedPagesAsArrays();
    	    
	    }else{
	        $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/pages');
	    }
	    
	}
	
	public function updateRelatedPageConnections($get, $post){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_webid)){
	        
	        if($this->getRequestParameter('pages') && is_array($this->getRequestParameter('pages'))){
	            
	            $new_related_ids = array_keys($this->getRequestParameter('pages'));
	            
	            if(count($new_related_ids)){
	            
	                $old_related_ids = $page->getRelatedPageIds(true);
        	        $helper = new SmartestPageManagementHelper;
        	        $pages = $helper->getPagesList($this->getSite()->getId());
    	        
        	        foreach($pages as $p){
    	            
        	            if(in_array($p['id'], $new_related_ids) && !in_array($p['id'], $old_related_ids)){
        	                // add connection
        	                $page->addRelatedPage($p['id']);
        	            }
    	            
        	            if(in_array($p['id'], $old_related_ids) && !in_array($p['id'], $new_related_ids)){
        	                // remove connection
        	                $page->removeRelatedPage($p['id']);
        	            }
        	        }
    	        
	            }else{
	                
	                $page->removeAllRelatedPages();
	                
	            }
    	        
            }else{
                $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of pages', SmartestUserMessage::ERROR);
            }
        }else{
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	public function updateRelatedItemConnections($get, $post){
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_webid)){
	        
	        if($this->getRequestParameter('items') && is_array($this->getRequestParameter('items'))){
	            
	            $new_related_ids = array_keys($this->getRequestParameter('items'));
	            
	            $model = new SmartestModel;
	            
	            if($model->hydrate($this->getRequestParameter('model_id'))){
	            
	                if(count($new_related_ids)){
	            
    	                $old_related_ids = $page->getRelatedItemIds($model->getId());
            	        $items = $model->getSimpleItemsAsArrays($this->getSite()->getId());
            	        
            	        foreach($items as $item){
    	            
            	            if(in_array($item['id'], $new_related_ids) && !in_array($item['id'], $old_related_ids)){
            	                // add connection
            	                $page->addRelatedItem($item['id']);
            	            }
    	            
            	            if(in_array($item['id'], $old_related_ids) && !in_array($item['id'], $new_related_ids)){
            	                // remove connection
            	                $page->removeRelatedItem($item['id']);
            	            }
            	        }
    	        
    	            }else{
	                
    	                $page->removeAllRelatedItems($model->getId());
	                
    	            }
	            
                }
    	        
            }else{
                $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of pages', SmartestUserMessage::ERROR);
            }
        }else{
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	public function authors($get){
	    
	    if(!$this->getRequestParameter('from')){
	        $this->setFormReturnUri();
	    }
	    
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    
	    if($page->hydrate($page_webid)){
	        
	        $uhelper = new SmartestUsersHelper;
	        // $users = $uhelper->getUsersOnSiteAsArrays($this->getSite()->getId());
	        $uhelper->distributeAuthorCreditTokenFromPage($page);
	        $users = $uhelper->getCreditableUsersOnSite($this->getSite()->getId());
	        $this->send($users, 'users');
	        $author_ids = $page->getAuthorIds();
	        $this->send($author_ids, 'author_ids');
	        $this->send($page, 'page');
	        $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
	        $this->send($this->getUser()->hasToken('modify_user_permissions'), 'provide_tokens_link');
	        
	    }else{
            $this->addUserMessage('The page ID was not recognized', SmartestUserMessage::ERROR);
        }
	    
	}
	
	public function updateAuthors($get, $post){
	    
	    $page_id = (int) $this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    $page->setDraftMode(true);
	    
	    if($page->hydrate($page_id)){
	        
	        if($this->getRequestParameter('users') && count($this->getRequestParameter('users'))){
	            
	            $uhelper = new SmartestUsersHelper;
                $users = $uhelper->getCreditableUsersOnSite($this->getSite()->getId());
            
                $new_author_ids = array_keys($this->getRequestParameter('users'));
                $old_author_ids = $page->getAuthorIds();
                
                foreach($users as $u){
                    
                    if(in_array($u->getId(), $old_author_ids) && !in_array($u->getId(), $new_author_ids)){
                        // remove connection
                        $page->removeAuthorById($u->getId());
                    }
                    
                    if(in_array($u->getId(), $new_author_ids) && !in_array($u->getId(), $old_author_ids)){
                        // add connection
                        $page->addAuthorById($u->getId());
                    }
                }
                
                $this->addUserMessageToNextRequest('The authors of this page were sucessfully updated.', SmartestUserMessage::SUCCESS);
            
            }else{
                
                $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_PAGE_AUTHORS');
        	    $q->setTargetEntityByIndex(1);
        	    $q->addQualifyingEntityByIndex(2, $page->getId());

        	    $q->addSortField('Users.user_lastname');

        	    $q->delete();
        	    
        	    $this->addUserMessageToNextRequest('The authors of this page were sucessfully removed.', SmartestUserMessage::SUCCESS);
                
            }
	        
	    }else{
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
        }
	    
	    $this->formForward();
	    
	}
	
	public function structure($get){
	
		$this->setFormReturnUri();
		
		$version = ($this->getRequestParameter('version') == "live") ? "live" : "draft";
		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		
		$elements = $this->manager->getPageElements($this->getRequestParameter('page_id'), $version);
		
	}
	
	public function layoutPresetForm($get){
		
		$page_webid = $this->getRequestParameter('page_id');
		
		$helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());

		if(isset($type_index[$page_webid])){
		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		if($page->hydrate($page_webid)){
		    
		    if($page->getType() == 'ITEMCLASS'){
	            if($this->getRequestParameter('item_id') && $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
	                $page->setPrincipalItem($item);
	                $this->send($item, 'item');
	                $item_id = $this->getRequestParameter('item_id');
	            }else{
	                $item_id = false;
	            }
            }
		    
		    $page->setDraftMode(true);
		    
		    $this->setTitle('Create Preset');
		    
		    $assetClasses = $this->manager->getPageTemplateAssetClasses($page_webid, "draft", $item_id);
		    $assetClasseslist = $this->manager->getSerialisedAssetClassTree($assetClasses['tree']);
 		    
 		    $this->send($assetClasseslist, 'elements');
 		    $this->send($page, 'page');
 		
	    }
	}
	
	public function createLayoutPreset($get, $post){
	
		/* $page_id = $this->getRequestParameter('page_id');
		$user_id = $_SESSION['user']['user_id'];
		$plp_name = $this->getRequestParameter('layoutpresetname');
		$master_template =  $this->database->specificQuery("page_live_template", "page_id", $page_id, "Pages");
		$assets = $this->getRequestParameter('asset');
		
		$this->manager->setupLayoutPreset($plp_name, $assets, $master_template, $user_id, $page_id); */
		
		$num_elements = 0;
		
		$preset = new SmartestPagePreset;
		
		$preset->setOrigFromPageId($this->getRequestParameter('page_id'));
		$preset->setMasterTemplateName($preset->getOriginalPage()->getDraftTemplate());
		$preset->setCreatedByUserId($this->getUser()->getId());
		$preset->setLabel($this->getRequestParameter('preset_name'));
		$preset->setSiteId($this->getSite()->getId());
		$shared = $this->getRequestParameter('preset_shared') ? 1 : 0;
		$preset->setShared($shared);
		
		if($this->getRequestParameter('placeholder') && is_array($this->getRequestParameter('placeholder'))){
		    
		    $num_elements += count($this->getRequestParameter('placeholder'));
		    
		    foreach($this->getRequestParameter('placeholder') as $placeholder_id){
		        $preset->addPlaceholderDefinition($placeholder_id);
		    }
		    
		}
		
		if($this->getRequestParameter('container') && is_array($this->getRequestParameter('container'))){
		    $num_elements += count($this->getRequestParameter('container'));
		    
		    foreach($this->getRequestParameter('container') as $container_id){
		        $preset->addContainerDefinition($container_id);
		    }
		    
		}
		
		if($this->getRequestParameter('field') && is_array($this->getRequestParameter('field'))){
		    
		    $num_elements += count($this->getRequestParameter('field'));
		    
		    foreach($this->getRequestParameter('field') as $field_id){
		        $preset->addFieldDefinition($field_id);
		    }
		    
		}
		
		if($num_elements > 0){
		    $preset->save();
		    $this->addUserMessageToNextRequest("The new preset has been created.", SmartestUserMessage::SUCCESS);
		}
		
		$this->formForward();
	}
	
	public function defineContainer($get){
	    
	    $container_name = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    $this->setTitle('Define Container');
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		$this->send($this->getApplicationPreference('define_container_list_view', 'grid'), 'list_view');
	    
	    if(isset($type_index[$page_webid])){
		    
		    if($type_index[$page_webid] == 'ITEMCLASS'){
		        
		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
		            $item_id = (int) $this->getRequestParameter('item_id');
		            
    		        $page = new SmartestItemPage;
		        
    		        if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
    	                $page->setPrincipalItem($item);
    	                $this->send($item, 'item');
    	                // rint_r($item['_model']);
    	                $this->send(true, 'show_item_options');
    	                $this->send(false, 'require_choose_item');
    	            }else{
    	                $this->send(true, 'require_choose_item');
    	                $require_item = true;
    	            }
    	            
	            
                }else{
                    // this is a meta page, but the item id is problematic
                    $page = new SmartestItemPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
                    $this->send(true, 'require_choose_item');
                    $require_item = true;
                }
		        
		    }else{
		        // this is just a normal static page
		        $item_id = '';
		        $page = new SmartestPage;
		        $this->send(false, 'require_choose_item');
		    }
		}else{
		    $page = new SmartestPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
		}
	    
	    if($page->hydrateBy('webid', $page_webid)){
	        
	        $page->setDraftMode(true);
	        
	        if(isset($require_item) && $require_item){
                
                $model = new SmartestModel;
                
                if($model->hydrate($page->getDatasetId())){
                    $items = $model->getSimpleItems($this->getSite()->getId());
                    $this->send($items, 'items');
                    $this->send($model, 'model');
                    $this->send($page, 'page');
                }
                
            }
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrateBy('name', $container_name)){
	            
	            $this->setTitle('Define Container | '.$container_name);
	            
	            $page_definition = new SmartestContainerDefinition;
	            
	            if($page_definition->load($container_name, $page, true)){
	                
	                if($type_index[$page_webid] == 'ITEMCLASS'){
	                    
	                    $item_definition = new SmartestContainerDefinition;
	                    
	                    if($item_definition->load($container_name, $page, true, $item_id)){
	                        
	                        if($page_definition->getDraftAssetId() == $item_definition->getDraftAssetId()){
	                            $item_uses_default = true;
	                        }else{
	                            $item_uses_default = false;
	                        }
	                        
	                        $this->send($item_definition->getDraftAssetId(), 'selected_template_id');
	                        
	                    }else{
	                        
	                        $this->send($page_definition->getDraftAssetId(), 'selected_template_id');
	                        $item_uses_default = true;
	                        
	                    }
	                    
	                    $this->send($item_uses_default, 'item_uses_default');
	                    
	                }else{
	                
	                    // container has live definition
    	                $this->send($page_definition->getDraftAssetId(), 'selected_template_id');
    	                $this->send(true, 'is_defined');
	                
                    }
	                
	            }else{
	                // container has no live definition
	                $this->send(0, 'selected_template_id');
	                $this->send(false, 'is_defined');
	            }
	            
	            $assets = $container->getPossibleAssets();
	            
	            $this->send($assets, 'templates');
	            $this->send($page, 'page');
	            $this->send($container, 'container');
	            
	        }
	    
        }else{
            // page not found
            $this->addUserMessageToNextRequest('The page ID was not recognized', SmartestUserMessage::ERROR);
            $this->redirect('/smartest/pages');
        }
	    
	}
	
	public function updateContainerDefinition($get, $post){
	    
	    $container_id = $this->getRequestParameter('container_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $asset_id = $this->getRequestParameter('asset_id');
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
	    if(isset($type_index[$page_id])){
		    if($type_index[$page_id] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrate($container_id)){
	            
	            $definition = new SmartestContainerDefinition;
	            
	            /* if($definition->loadForUpdate($container->getName(), $page)){
	                
	                // update container
	                $definition->setDraftAssetId($asset_id);
	                $definition->save();
	                
	            }else{
	                
	                // wasn't already defined
	                $definition->setDraftAssetId($asset_id);
	                $definition->setAssetclassId($container_id);
	                $definition->setInstanceName('default');
	                $definition->setPageId($page->getId());
	                $definition->save();
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time()); */
                
                if($type_index[$page_id] == 'NORMAL' || ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') != 'THIS')){
	                
	                if($definition->loadForUpdate($container->getName(), $page, true)){
	                    
	                    // update container
	                    $definition->setDraftAssetId($asset_id);
	                    $log_message = $this->getUser()->__toString()." updated container '".$container->getName()."' on page '".$page->getTitle(true)."' to use asset ID ".$asset_id.".";
	                
	                }else{
	                    
	                    // wasn't already defined
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($container_id);
	                    $definition->setInstanceName('default');
	                    $definition->setPageId($page->getId());
	                    $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on page '".$page->getTitle(true)."' with asset ID ".$asset_id.".";
	                
	                }
	            
	                if($this->getRequestParameter('definition_scope') == 'ALL'){
	                    
	                    // DELETE ALL PER-ITEM DEFINITIONS
	                    $pmh = new SmartestPageManagementHelper;
	                    $pmh->removePerItemDefinitions($page->getId(), $container_id);
	                    
	                }
	                
	                $definition->save();
	            
                }else if($type_index[$page_id] == 'ITEMCLASS' && ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') == 'THIS')){
                    
                    if($definition->loadForUpdate($container->getName(), $page, true)){ // looks for all-items definition
	                    
	                    $item_def = new SmartestContainerDefinition;
	                    
	                    // item chosen is same as all-items definition
	                    if($definition->getDraftAssetId() == $asset_id){ 
	                        
	                        // if there is already a per-item definitions for this item
	                        if($item_def->loadForUpdate($container->getName(), $page, false, $this->getRequestParameter('item_id'))){
	                            
	                            $item_def->delete();
                                
	                        }
	                        
	                        $log_message = $this->getUser()->__toString()." set container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." (which is the same as the all-items definition) when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                    }else{
	                        
	                        if($item_def->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'))){
	                            // just update container
	                            $item_def->setDraftAssetId($asset_id);
	                            $log_message = $this->getUser()->__toString()." updated container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }else{
	                            $item_def->setDraftAssetId($asset_id);
        	                    $item_def->setAssetclassId($container_id);
        	                    $item_def->setItemId($this->getRequestParameter('item_id'));
        	                    $item_def->setInstanceName('default');
        	                    $item_def->setPageId($page->getId());
	                            $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }
	                        
	                        $item_def->save();
	                        
	                    }
	                
	                }else if($definition->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') == 'THIS'){
	                    
	                    // all-items definition doesn't exist but per-item for this item does
	                    $definition->setDraftAssetId($asset_id);
	                    
	                    if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
    	                
    	                $definition->save();
    	                $log_message = $this->getUser()->__toString()." updated container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }else{
	                    
	                    // wasn't already defined for any items at all. Define for this item
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($container_id);
	                    if($this->getRequestParameter('definition_scope') == 'THIS'){$definition->setItemId($this->getRequestParameter('item_id'));}
	                    $definition->setInstanceName('default');
	                    $definition->setPageId($page->getId());
	                    
	                    if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
    	                
    	                $definition->save();
    	                $log_message = $this->getUser()->__toString()." defined container '".$container->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }
	                
                }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
                SmartestLog::getInstance('site')->log($log_message, SM_LOG_USER_ACTION);
	            
	            $this->addUserMessageToNextRequest('The container was updated.', SmartestUserMessage::SUCCESS);
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified container doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function definePlaceholder($get){
	    
	    if($this->getUser()->hasToken('modify_draft_pages')){
	    
    	    $placeholder_name = $this->getRequestParameter('assetclass_id');
    	    $page_webid = $this->getRequestParameter('page_id');
	    
    	    $this->setTitle('Define Placeholder');
	    
    	    $helper = new SmartestPageManagementHelper;
    		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
	    
    	    if(isset($type_index[$page_webid])){
		    
    		    if($type_index[$page_webid] == 'ITEMCLASS'){
		        
    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
    		            $item_id = (int) $this->getRequestParameter('item_id');
		            
        		        $page = new SmartestItemPage;
		        
        		        if($item = SmartestCmsItem::retrieveByPk($item_id)){
        	                $page->setPrincipalItem($item);
        	                $this->send($item, 'item');
        	                $this->send(true, 'show_item_options');
        	                $this->send(false, 'require_choose_item');
        	            }else{
        	                $this->send(true, 'require_choose_item');
        	                $require_item = true;
        	            }
	            
                    }else{
                        // this is a meta page, but the item id is problematic
                        $page = new SmartestItemPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
                        $this->send(true, 'require_choose_item');
                        $require_item = true;
                    }
		        
    		    }else{
    		        // this is just a normal static page
    		        $item_id = '';
    		        $page = new SmartestPage;
    		        $this->send(false, 'require_choose_item');
    		    }
    		}else{
    		    $page = new SmartestPage; // this is needed to prevent a fatal error when page is looked up via hydrateBy
    		}
		
    		if($page->hydrateBy('webid', $page_webid)){
	        
    	        $page->setDraftMode(true);
	        
    	        if(isset($require_item) && $require_item){
                
                    $model = new SmartestModel;
                
                    if($model->hydrate($page->getDatasetId())){
                        $items = $model->getSimpleItems($this->getSite()->getId());
                        $this->send($items, 'items');
                        $this->send($model, 'model');
                        $this->send($page, 'page');
                    }
                
                }
	        
    	        $placeholder = new SmartestPlaceholder;
	        
    	        if($placeholder->hydrateBy('name', $placeholder_name)){
	            
    	            $this->setTitle('Define Placeholder | '.$placeholder_name);
	            
    	            $types_array = SmartestDataUtility::getAssetTypes();
                
                    $page_definition = new SmartestPlaceholderDefinition;
                
                    if($page_definition->load($placeholder_name, $page, true, $this->getRequestParameter('item_id'))){
	                
    	                $is_defined = true;
	                
    	                if($type_index[$page_webid] == 'ITEMCLASS'){
	                    
    	                    $item_definition = new SmartestPlaceholderDefinition;
    	                    if($item_definition->load($placeholder_name, $page, true, $item_id)){
    	                        if($page_definition->getDraftAssetId() == $item_definition->getDraftAssetId()){
    	                            $item_uses_default = true;
    	                        }else{
    	                            $item_uses_default = false;
    	                        }
    	                    }else{
    	                        $item_uses_default = true;
    	                    }
    	                }
	                
    	                if($existing_render_data = unserialize($page_definition->getDraftRenderData())){
    	                    if(is_array($existing_render_data) && is_array($params)){
	                        
    	                        foreach($params as $key => $value){
    	                            if(isset($existing_render_data[$key])){
    	                                $params[$key] = $existing_render_data[$key];
    	                            }
    	                        }
                            }
                        }
	                
    	                $this->send($page_definition->getDraftAssetId(), 'draft_asset_id');
    	                $this->send($page_definition->getLiveAssetId(), 'live_asset_id');
	                
    	            }else{
    	                $item_uses_default = false;
    	                $is_defined = false;
    	                $this->send($page_definition->getDraftAssetId(), 'draft_asset_id');
    	                $existing_render_data = array();
    	            }
	            
    	            $this->send($item_uses_default, 'item_uses_default');
    	            $this->send($is_defined, 'is_defined');
                
                    $asset = new SmartestAsset;
                
                    if($this->getRequestParameter('chosen_asset_id')){
                    
                        $chosen_asset_id = (int) $this->getRequestParameter('chosen_asset_id');
                        $chosen_asset_exists = $asset->hydrate($chosen_asset_id);
                    
            	    }else{
        	        
            	        if($is_defined){
        	            
            	            // if asset is chosen
            	            if($type_index[$page_webid] == 'ITEMCLASS' && $item_definition->load($placeholder_name, $page, true, $item_id)){
            	                $chosen_asset_id = $item_definition->getDraftAssetId();
            	            }else{
            	                $chosen_asset_id = $page_definition->getDraftAssetId();
        	                }
    	                
            	            $chosen_asset_exists = $asset->hydrate($chosen_asset_id);
            	        }else{
            	            // No asset choasen. don't show params or 'continue' button
            	            $chosen_asset_id = 0;
            	            $chosen_asset_exists = false;
            	        }
            	    }
        	    
            	    if($chosen_asset_exists){
        	        
            	        $this->send($asset, 'asset');
        	        
            	        $type = $types_array[$asset->getType()];
        	        
            	        // Merge values for render data
        	        
            	        if(isset($type['param'])){

                	        $raw_xml_params = $type['param'];
                            $params = array();
                	        foreach($raw_xml_params as $rxp){
            	            
                	            if(isset($rxp['default'])){
                	                $params[$rxp['name']]['xml_default'] = $rxp['default'];
                	                $params[$rxp['name']]['value'] = $rxp['default'];
                                }else{
                                    $params[$rxp['name']]['xml_default'] = '';
                                    $params[$rxp['name']]['value'] = '';
                                }
                            
                                $params[$rxp['name']]['type'] = $rxp['type'];
                                $params[$rxp['name']]['asset_default'] = '';
                	        }
            	        
                	        $this->send($type, 'asset_type');

                	    }else{
                	        $params = array();
                	    }
            	    
                	    $asset_params = $asset->getDefaultParameterValues();
            	    
                	    $this->send($asset_params, 'asset_params');
            	    
                	    foreach($params as $key=>$p){
                	        // default values from xml are set above.
            	        
                	        // next, set values from asset
                	        if(isset($asset_params[$key]) && strlen($asset_params[$key])){
                	            // $params[$key]['value'] = $asset_params[$key];
                	            // $params[$key]['asset_default'] = $asset_params[$key];
                	        }
            	        
                	        // then, override any values that already exist
                	        if(isset($existing_render_data[$key]) && strlen($existing_render_data[$key])){
                	            $params[$key]['value'] = $existing_render_data[$key];
                	        }
            	        }
        	        
                	    $this->send(true, 'valid_definition');
            	    
        	        }else{
    	            
        	            $this->send(false, 'valid_definition');
    	            
        	        }
	            
    	            $this->send($params, 'params');
	            
    	            $assets = $placeholder->getPossibleAssets($this->getSite()->getId());
	            
    	            $this->send($assets, 'assets');
    	            $this->send($page, 'page');
    	            $this->send($placeholder, 'placeholder');
	            
    	        }
	    
            }else{
                $this->addUserMessageToNextRequest("The page ID was not recognized", SM_USER_MESSAGE_WARNING);
                $this->redirect('/smartest/pages');
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to update placeholders.", SmartestUserMessage::ACCESS_DENIED);
            $this->formForward();
            
        }
        
	}
	
	public function updatePlaceholderDefinition($get, $post){
	    
	    $placeholder_id = $this->getRequestParameter('placeholder_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $asset_id = $this->getRequestParameter('asset_id');
	    
	    $helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		
	    if(isset($type_index[$page_id])){
		    if($type_index[$page_id] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        $placeholder = new SmartestPlaceholder;
	        
	        if($placeholder->hydrate($placeholder_id)){
	            
	            $definition = new SmartestPlaceholderDefinition;
	            
	            if($type_index[$page_id] == 'NORMAL' || ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') != 'THIS')){
	                
	                if($definition->loadForUpdate($placeholder->getName(), $page)){
	                
	                    // update placeholder
	                    $definition->setDraftAssetId($asset_id);
	                    $log_message = $this->getUser()->__toString()." updated placeholder '".$placeholder->getName()."' on page '".$page->getTitle(true)."' to use asset ID ".$asset_id.".";
	                
	                }else{
	                
	                    // wasn't already defined
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($placeholder_id);
	                    $definition->setInstanceName('default');
	                    $definition->setPageId($page->getId());
	                    $log_message = $this->getUser()->__toString()." defined placeholder '".$placeholder->getName()."' on page '".$page->getTitle(true)."' with asset ID ".$asset_id.".";
	                
	                }
	            
	                if(is_array($this->getRequestParameter('params'))){
	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
	                }
	                
	                if($this->getRequestParameter('definition_scope') == 'ALL'){
	                    
	                    // DELETE ALL PER-ITEM DEFINITIONS
	                    $pmh = new SmartestPageManagementHelper;
	                    $pmh->removePerItemDefinitions($page->getId(), $placeholder->getId());
	                    
	                }
	                
	                $definition->save();
	            
                }else if($type_index[$page_id] == 'ITEMCLASS' && ($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id')) && $this->getRequestParameter('definition_scope') == 'THIS')){
                    
                    if($definition->loadForUpdate($placeholder->getName(), $page)){ // looks for all-items definition
	                    
	                    $item_def = new SmartestPlaceholderDefinition;
	                    
	                    // item chosen is same as all-items definition
	                    if($definition->getDraftAssetId() == $asset_id){ 
	                        
	                        if(is_array($this->getRequestParameter('params'))){
	                            $now_prms = $this->getRequestParameter('params'); // copy needs to be made here because ksort() does not return
	                            $ex_prms = $definition->getRenderData(true);
	                            $default_def_params_hash = md5(serialize($ex_prms));
	                            $this_item_params_hash = md5(serialize($now_prms));
	                            $has_params = true;
                            }else{
                                $has_params = false;
                            }
	                        
	                        // if there is already a per-item definitions for this item
	                        if($item_def->loadForUpdate($placeholder->getName(), $page, $this->getRequestParameter('item_id'))){
	                            
	                            if($has_params && ($default_def_params_hash != $this_item_params_hash)){
	                                // don't delete, because display params are different to default.
	                                $item_def->setDraftRenderData(serialize($this->getRequestParameter('params')));
	                                $item_def->save();
	                            }else{
	                                $item_def->delete();
                                }
                                
	                        }else{ // No per-item definition found for this one so create *IF* the params are different.
	                            if($has_params && ($default_def_params_hash != $this_item_params_hash)){
	                                $item_def->setDraftAssetId($asset_id);
        	                        $item_def->setAssetclassId($placeholder_id);
        	                        $item_def->setItemId($this->getRequestParameter('item_id'));
        	                        $item_def->setInstanceName('default');
        	                        $item_def->setPageId($page->getId());
	                                $item_def->setDraftRenderData(serialize($this->getRequestParameter('params')));
                                    $item_def->save();
                                }
                                
	                        }
	                        
	                        $log_message = $this->getUser()->__toString()." set placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." (which is the same as the all-items definition) when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                    }else{
	                        
	                        if($item_def->loadForUpdate($placeholder->getName(), $page, $this->getRequestParameter('item_id'))){
	                            // just update placeholder
	                            $item_def->setDraftAssetId($asset_id);
	                            $log_message = $this->getUser()->__toString()." updated placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }else{
	                            $item_def->setDraftAssetId($asset_id);
        	                    $item_def->setAssetclassId($placeholder_id);
        	                    $item_def->setItemId($this->getRequestParameter('item_id'));
        	                    $item_def->setInstanceName('default');
        	                    $item_def->setPageId($page->getId());
	                            $log_message = $this->getUser()->__toString()." defined placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                        }
	                        
	                        if(is_array($this->getRequestParameter('params'))){
        	                    $item_def->setDraftRenderData(serialize($this->getRequestParameter('params')));
        	                }
        	                
        	                $item_def->save();
	                        
	                    }
	                
	                }else if($definition->loadForUpdate($placeholder->getName(), $page, $this->getRequestParameter('item_id'))){
	                    
	                    // all-items definition doesn't exist but per-item for this item does
	                    $definition->setDraftAssetId($asset_id);
	                    
	                    if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
    	                
    	                $definition->save();
    	                $log_message = $this->getUser()->__toString()." updated placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }else{
	                    
	                    // wasn't already defined for any items at all. Define for this item
	                    $definition->setDraftAssetId($asset_id);
	                    $definition->setAssetclassId($placeholder_id);
	                    $definition->setItemId($this->getRequestParameter('item_id'));
	                    $definition->setInstanceName('default');
	                    $definition->setPageId($page->getId());
	                    
	                    if(is_array($this->getRequestParameter('params'))){
    	                    $definition->setDraftRenderData(serialize($this->getRequestParameter('params')));
    	                }
    	                
    	                $definition->save();
    	                $log_message = $this->getUser()->__toString()." defined placeholder '".$placeholder->getName()."' on meta-page '".$page->getTitle(true)."' to use asset ID ".$asset_id." when displaying item ID ".$this->getRequestParameter('item_id').".";
	                    
	                }
	                
                }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	            $this->addUserMessageToNextRequest('The placeholder was updated.', SmartestUserMessage::SUCCESS);
	            SmartestLog::getInstance('site')->log($log_message, SM_LOG_USER_ACTION);
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified placeholder doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefinePlaceholder($get, $post){
	    
	    $placeholder_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $item_id = $this->getRequestParameter('item_id') ? $this->getRequestParameter('item_id') : false;
	    
	    $this->setTitle('Un-Define Placeholder');
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $placeholder = new SmartestPlaceholder;
	        
	        if($placeholder->hydrateBy('name', $placeholder_id)){
	            
	            $definition = new SmartestPlaceholderDefinition;
	            
	            if($definition->loadForUpdate($placeholder->getName(), $page, $item_id)){
	                
	                // update placeholder
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The placeholder definition was removed for this item.', SmartestUserMessage::SUCCESS);
	            
	            }else if($definition->loadForUpdate($placeholder->getName(), $page)){
	                
	                // update placeholder
	                $definition->setDraftAssetId('');
	                $definition->save();
	                $this->addUserMessageToNextRequest('The placeholder definition was removed.', SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The placeholder wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified placeholder doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefinePlaceholderOnItemPage($get, $post){
	    
	    $placeholder_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
	    $item_id = $this->getRequestParameter('item_id');
	    
	    $this->setTitle('Un-Define Placeholder');
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $placeholder = new SmartestPlaceholder;
	        
	        if($placeholder->hydrateBy('name', $placeholder_id)){
	            
	            $definition = new SmartestPlaceholderDefinition;
	            
	            if($definition->loadForUpdate($placeholder->getName(), $page, $item_id)){
	                
	                // update placeholder
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The placeholder definition was removed for this item.', SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The placeholder wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified placeholder doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefineContainer($get, $post){
	    
	    $container_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrateBy('name', $container_id)){
	            
	            $definition = new SmartestContainerDefinition;
	            
	            if($this->getRequestParameter('item_id') && $definition->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'))){
	            
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The container definition was removed.', SmartestUserMessage::SUCCESS);
	            
	            }else if($definition->loadForUpdate($container->getName(), $page, true)){
	                
	                // update placeholder
	                // $definition->delete();
	                $definition->setDraftAssetId('');
	                $definition->save();
	                $this->addUserMessageToNextRequest('The container definition was removed.', SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The container wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified container doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function undefineContainerOnItemPage($get, $post){
	    
	    $container_id = $this->getRequestParameter('assetclass_id');
	    $page_id = $this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $container = new SmartestContainer;
	        
	        if($container->hydrateBy('name', $container_id)){
	            
	            $definition = new SmartestContainerDefinition;
	            
	            if($this->getRequestParameter('item_id') && $definition->loadForUpdate($container->getName(), $page, true, $this->getRequestParameter('item_id'))){
	            
	                $definition->delete();
	                $this->addUserMessageToNextRequest('The container definition was removed.', SmartestUserMessage::SUCCESS);
	            
	            }else{
	                
	                // wasn't already defined
	                $this->addUserMessageToNextRequest('The container wasn\'t defined to start with.', SmartestUserMessage::INFO);
	                
	                
	            }
	            
	            $page->setChangesApproved(0);
                $page->setModified(time());
                $page->save();
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The specified container doesn\'t exist', SmartestUserMessage::ERROR);
	            
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('The specified page doesn\'t exist', SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function editAttachment($get){
	    
	    $id = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    $parts = explode(':', $id);
	    $asset_stringid = $parts[0];
	    $attachment = $parts[1];
	    $asset = new SmartestAsset;
	    
	    if($asset->hydrateBy('stringid', $asset_stringid)){
	        $this->redirect('/assets/defineAttachment?attachment='.$attachment.'&asset_id='.$asset->getId());
	    }else{
	        
	        if(strlen($page_webid) == 32){
	            $this->addUserMessageToNextRequest("The attachment ID was not recognized.", SmartestUserMessage::ERROR);
	            $this->redirect('/websitemanager/pageAssets?page_id='.$page_webid); 
	        }else{
	            $this->redirect('/smartest/pages');
	        }
	    }
	}
	
	public function editFile($get){
	    
	    $id = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    $asset = new SmartestAsset;
	    
	    if($asset->hydrateBy('stringid', $id, $this->getSite()->getId())){
            $this->redirect('/assets/editAsset?assettype_code='.$asset->getType().'&asset_id='.$asset->getId().'&from=pageAssets');
        }else{
            if(strlen($page_webid) == 32){
	            $this->redirect('/websitemanager/pageAssets?page_id='.$page_webid);
	            $this->addUserMessageToNextRequest("The file ID was not recognized.", SmartestUserMessage::ERROR);
	        }else{
	            $this->redirect('/smartest/pages');
	        }
        }
	}
	
	public function editTemplate($get){
	    
	    $id = $this->getRequestParameter('assetclass_id');
	    $page_webid = $this->getRequestParameter('page_id');
	    $asset = new SmartestTemplateAsset;
	    
	    if($asset->findBy('stringid', $id)){
            $this->redirect('/templates/editTemplate?type=SM_ASSETTYPE_CONTAINER_TEMPLATE&template='.$asset->getId().'&from=pageAssets');
        }else{
            if(strlen($page_webid) == 32){
	            $this->redirect('/websitemanager/pageAssets?page_id='.$page_webid);
	            $this->addUserMessageToNextRequest("The template ID was not recognized.", SmartestUserMessage::ERROR);
	        }else{
	            $this->redirect('/smartest/pages');
	        }
        }
	}
	
	public function setPageTemplate($get){
		
		$template_name = $this->getRequestParameter('template_name');
		$page_id = $this->getRequestParameter('page_id');
		
		if(is_file(SM_ROOT_DIR.'Presentation/Masters/'.$template_name)){
		    SmartestDatabase::getInstance('SMARTEST')->query("UPDATE Pages SET Pages.page_draft_template='$template_name' WHERE Pages.page_webid='$page_id'");
	    }else if(!strlen($template_name)){
	        SmartestDatabase::getInstance('SMARTEST')->query("UPDATE Pages SET Pages.page_draft_template='' WHERE Pages.page_webid='$page_id'");
	    }
	    
	    $this->formForward();
		
	}
	
	/* function setPageTemplateForLists($get){
		$template_name = $get["template_name"];
		$version = ($get["version"] == "live") ? "live" : "draft";
		$field = ($get["version"] == "live") ? "page_live_template" : "page_draft_template";
		$page_id = $get["page_id"];
		$this->database->query("UPDATE Pages SET $field='$template_name' WHERE page_webid='$page_id'");
		header("Location:".$this->domain.$this->module."/getPageLists?page_id=$page_id&version=$version");
	} */
	
	public function setDraftAsset($get){

		$this->manager->setDraftAsset($this->getRequestParameter('page_id'), $this->getRequestParameter('assetclass_id'), $this->getRequestParameter('asset_id'));
		$this->formForward();
		
	}
	
	function setLiveAsset($get){
		
		$this->manager->setLiveAsset($this->getRequestParameter('page_id'), $this->getRequestParameter('assetclass_id'));
		
		$page_pk = $this->manager->database->specificQuery("page_id", "page_webid", $this->getRequestParameter('page_id'), "Pages");
		
		if(is_numeric($this->getRequestParameter('assetclass_id')) && $this->getRequestParameter('assetclass_id')){
			$assetclass = $this->manager->database->specificQuery("assetclass_name", "assetclass_id", $this->getRequestParameter('assetclass_id'), "AssetClasses");
		}else{
			$assetclass = $this->getRequestParameter('assetclass_id');
		}
		
		
		// This code clears the cached placeholders
		$cache_filename = "System/Cache/SmartestEngine/"."ac_".md5($assetclass)."-".$page_pk.".tmp";
		
		if(is_file($cache_filename) && SM_OPTIONS_CACHE_ASSETCLASSES){
			@unlink($cache_filename);
		}
		
		$this->formForward();
	}
	
	/* function publishPageContainersConfirm($get){
		$page_webid=$this->getRequestParameter('page_id');
		$version="draft";
		$undefinedContainerClasses=$this->manager->publishPageContainersConfirm($page_webid,$version);
		$count=count($undefinedContainerClasses);
		return array ("undefinedContainerClasses"=>$undefinedContainerClasses,"page_id"=>$page_webid,"count"=>$count);
	}
	
	function publishPageContainers($get){
		$page_webid=$this->getRequestParameter('page_id');
// 		echo $page_webid;
		$this->manager->publishPageContainers($page_webid);
		$this->formForward();
	}
	
	function publishPagePlaceholdersConfirm($get){
		$page_webid=$this->getRequestParameter('page_id');
		$version="draft";
		$undefinedPlaceholderClasses=$this->manager->publishPagePlaceholdersConfirm($page_webid,$version);
		$count=count($undefinedPlaceholderClasses);
		return array ("undefinedPlaceholderClasses"=>$undefinedPlaceholderClasses,"page_id"=>$page_webid,"count"=>$count);
			
	}
	
	function publishPagePlaceholders($get){
		$page_webid=$this->getRequestParameter('page_id');
		$this->manager->publishPagePlaceholders($page_webid);
		$this->formForward();
	} */
	
	function publishPageConfirm($get){
		
		// display to the user a list of any placeholders or containers that are undefined in the draft page that is about to be published,
		// so that the user is warned before publishing undefined placeholders or containers that may cause the page to display incorrectly
		// the user should be able to publish either way - the notice will be just a warning.
		
		$helper = new SmartestPageManagementHelper;
		$type_index = $helper->getPageTypesIndex($this->getSite()->getId());
		$page_webid = $this->getRequestParameter('page_id');
		
	    if(isset($type_index[$page_webid])){
		    if($type_index[$page_webid] == 'ITEMCLASS' && $this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		        $page = new SmartestItemPage;
		    }else{
		        $page = new SmartestPage;
		    }
		}else{
		    $page = new SmartestPage;
		}
		
		if($page->hydrate($page_webid)){
		    
		    if($page->getType() == 'ITEMCLASS'){
                if($this->getRequestParameter('item_id') && $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                    
                    $page->setPrincipalItem($item);
                    $this->send($item, 'item');
                    $item_id = $this->getRequestParameter('item_id');
                    
                    $user_can_publish_item = ($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items');
                    
                    $this->send($user_can_publish_item, 'user_can_publish_item');
                    
                }else{
                    $item_id = false;
                }
            }
            
            $page->setDraftMode(true);
		    
		    if(( (boolean) $page->getChangesApproved() && $this->getUser()->hasToken('publish_approved_pages')) || $this->getUser()->hasToken('publish_all_pages')){
		        
		        $version = "draft";
		        $undefinedAssetsClasses = $this->manager->getUndefinedElements($page_webid, 'draft', $item_id);
		        
		        $count = count($undefinedAssetsClasses);
		        $this->send(true, 'allow_publish');
		        $this->send($undefinedAssetsClasses, "undefined_asset_classes");
		        $this->send($page->getWebId(), "page_id");
		        $this->send(count($undefinedAssetsClasses), "count");
		    
	        }else{
	            
	            $this->send(false, 'allow_publish');
	            $this->send($page->getWebId(), "page_id");
	            
	            if((boolean) $page->getChangesApproved()){
		            $this->addUserMessage('You can\'t publish this page because you don\'t have permission to publish pages.', SmartestUserMessage::ACCESS_DENIED);
		        }else{
		            $this->addUserMessage('You can\'t publish this page because the changes on it haven\'t yet been approved and you don\'t have permission to override approval.', SmartestUserMessage::ACCESS_DENIED);
		        }
	            
	        }
		
	    }else{
	        
	        $this->addUserMessage('The page could not be found');
	        
	    }
			
	}
	
	public function publishPage($get, $post){
	    
	    $page = new SmartestPage;
	    $page_webid = $this->getRequestParameter('page_id');
	    if($this->getRequestParameter('item_id')){$item_id = $this->getRequestParameter('item_id');}else{$item_id = false;}
	    
	    if($page->hydrate($page_webid)){
	        
	        $page->setDraftMode(true);
	        
	        if(((boolean) $page->getChangesApproved() || $this->getUser()->hasToken('approve_page_changes')) && ($this->getUser()->hasToken('publish_approved_pages')) || $this->getUser()->hasToken('publish_all_pages')){
		        
		        $page->publish($item_id);
		        SmartestLog::getInstance('site')->log("{$this->getUser()} published page: {$page->getTitle()}.", SmartestLog::USER_ACTION);
		        
		        if($this->getRequestParameter('item_id') && $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                    
                    $user_can_publish_item = ($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items');
                    
                    if($user_can_publish_item){
                        if($this->getRequestParameter('publish_item') == 'PUBLISH'){
                            $item->publish();
                            $this->addUserMessageToNextRequest('The page and the item '.$item->getName().' have both been successfully published.', SmartestUserMessage::SUCCESS);
                        }else{
                            $this->addUserMessageToNextRequest('The page has been successfully published.', SmartestUserMessage::SUCCESS);
                        }
                    }else{
                        $this->addUserMessageToNextRequest('The page has been successfully published, but the item could not be published.', SmartestUserMessage::INFO);
                    }
                    
                }else{
                    
                    $this->addUserMessageToNextRequest('The page has been successfully published.', SmartestUserMessage::SUCCESS);
                    
                }
		        
	        }else{
	            
	            if((boolean) $page->getChangesApproved()){
		            $this->addUserMessageToNextRequest('The page could not be published because you don\'t have permission to publish pages', SmartestUserMessage::ACCESS_DENIED);
		        }else{
		            $this->addUserMessageToNextRequest('The page could not be published because the changes on it haven\'t yet been approved and you don\'t have permission to approve pages', SmartestUserMessage::ACCESS_DENIED);
		        }
	            
	        }
        }
        
        $this->formForward();
	}
	
	public function unPublishPage($get){
	    
	    $page_webid = $this->getRequestParameter('page_id');
		$page = new SmartestPage;
		
		if($page->hydrate($page_webid)){
		    
		    $page->setDraftMode(true);
		    $page->unpublish();
		    
		}
		
		$this->addUserMessageToNextRequest('The page has been un-published. No other changes have been made.', SmartestUserMessage::SUCCESS);
		$this->formForward();
		
	}

	public function getPageLists($get){
		
		$this->setFormReturnUri();
		
		$page_webid = $this->getRequestParameter('page_id');
		$version = ($this->getRequestParameter('version') == "live") ? "live" : "draft";
		$field = ($version == "live") ? "page_live_template" : "page_draft_template";
		$site_id = $this->database->specificQuery("page_site_id", "page_webid", $this->getRequestParameter('page_id'), "Pages");
		$page = $this->manager->getPage($this->getRequestParameter('page_id'));
		$pageListNames = $this->manager->getPageLists($page_webid, $version);
 		
 		return array("pageListNames"=>$pageListNames,"page"=>$page,"version"=>$version,"templateMenuField"=>$page[$field],"site_id"=>$site_id);	
	}
	
	public function defineList($get){
        
        $templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/ListItems/');
        
        $list_name = $this->getRequestParameter('assetclass_id');
        
        $page_webid = $this->getRequestParameter('page_id');
        
        $page = new SmartestPage;
        
        if($page->hydrate($page_webid)){
            
            $page->setDraftMode(true);
            
            $list = new SmartestCmsItemList;
            
            if($list->load($list_name, $page, true)){
                // this list was already defined
            }else{
                // this is a new list
            }
            
            $this->send($list->getDraftHeaderTemplate(), 'header_template');
            $this->send($list->getDraftFooterTemplate(), 'footer_template');
            $this->send($list->getDraftTemplateFile(), 'main_template');
            $this->send($list->getDraftSetId(), 'set_id');
            $this->send($list, 'list');
            $this->send($list_name, 'list_name');
            
            $alh = new SmartestAssetsLibraryHelper;
            $this->send($alh->getAssetsByTypeCode('SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE', $this->getSite()->getId()), 'compound_list_templates');
            
            $tlh = new SmartestTemplatesLibraryHelper;
            $this->send($tlh->getArticulatedListTemplates($this->getSite()->getId()), 'art_list_templates');
            
            $datautil = new SmartestDataUtility;
            
            $sets = $datautil->getDataSetsAsArrays(false, $this->getSite()->getId());
            $this->send($sets, 'sets');
            $this->send($page, 'page');
            $this->send($templates, 'templates');
            
        }else{
            // page was not found
            $this->addUserMessageToNextRequest("The page ID was not recognised.", SmartestUserMessage::ERROR);
            $this->formForward();
        }
        
		/* $page_id = $this->manager->getPageIdFromPageWebId($this->getRequestParameter('page_id'));
		$list_name = $this->getRequestParameter('list_id');

		$page = $this->manager->getPage($page_id);
		$sets = $this->setsManager->getSets();
		// $path = 'Presentation/ListItems'; 
		// $listitemtemplates = $this->templatesManager->getTemplateNames($path);
		
		$sql = "SELECT * FROM Lists WHERE list_page_id = '$page_id' AND list_name = '$list_name'";
		$result = $this->database->queryToArray($sql);
		$items = $this->manager->managePageData($result);
 		
 		$list_setid = $result[0]['list_draft_set_id'];
		$list_template = $result[0]['list_draft_template_file'];
		$list_header = $result[0]['list_draft_header_template'];
		$list_footer = $result[0]['list_draft_footer_template']; */
		
		// return array("page"=>$page, "sets"=>$sets, "listitemtemplates"=>$templates, "list_setid"=>$list_setid, "list_template"=>$list_template, "list_header"=>$list_header, "list_footer"=>$list_footer,"list_name"=>$list_id);
	
	}
	
	public function saveList($get, $post){
	    
	    $list_name = $this->getRequestParameter('list_name');
        
        $page_id = $this->getRequestParameter('page_id');
        
        $page = new SmartestPage;
        
        if($page->hydrate($page_id)){
            
            $page->setDraftMode(true);
            
            $list = new SmartestCmsItemList;
            
            if($list->load($list_name, $page, true)){
                // this list was already defined
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was updated successfully.", SmartestUserMessage::SUCCESS);
            }else{
                // this is a new list
                $list->setName($this->getRequestParameter('list_name'));
                $list->setPageId($page->getId());
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was defined successfully.", SmartestUserMessage::SUCCESS);
            }
            
            $list_type = in_array($this->getRequestParameter('list_type'), array('SM_LIST_ARTICULATED', 'SM_LIST_SIMPLE')) ? $this->getRequestParameter('list_type') : 'SM_LIST_SIMPLE';
            
            $list->setType($list_type);
            $list->setMaximumLength((int) $this->getRequestParameter('list_maximum_length'));
            $list->setTItle($this->getRequestParameter('list_title'));
            
            if($list_type == 'SM_LIST_ARTICULATED'){
            
                $templates = SmartestFileSystemHelper::load(SM_ROOT_DIR.'Presentation/ListItems/');
            
                if(is_numeric($this->getRequestParameter('dataset_id'))){
                    $list->setDraftSetId($this->getRequestParameter('dataset_id'));
                }
            
                if(in_array($this->getRequestParameter('header_template'), $templates)){
                    $list->setDraftHeaderTemplate($this->getRequestParameter('header_template'));
                }
            
                if(in_array($this->getRequestParameter('footer_template'), $templates)){
                    $list->setDraftFooterTemplate($this->getRequestParameter('footer_template'));
                }
            
                if(in_array($this->getRequestParameter('main_template'), $templates)){
                    $list->setDraftTemplateFile($this->getRequestParameter('main_template'));
                }
            
            }else{
                
                if(is_numeric($this->getRequestParameter('dataset_id'))){
                    $list->setDraftSetId((int) $this->getRequestParameter('dataset_id'));
                }
                
                $list->setDraftTemplateFile($this->getRequestParameter('art_main_template'));
                
            }
            
            $list->save();
            
            $this->formForward();
            
            // print_r($list->__toArray());
            /* $this->send($list->getDraftHeaderTemplate(), 'header_template');
            $this->send($list->getDraftFooterTemplate(), 'footer_template');
            $this->send($list->getDraftTemplateFile(), 'main_template');
            $this->send($list->getDraftSetId(), 'set_id');
            $this->send($list->__toArray(), 'list');
            $this->send($list_name, 'list_name');
            
            $sets = $this->getSite()->getDataSetsAsArrays();
            $this->send($sets, 'sets');
            $this->send($page->__toArray(), 'page');
            $this->send($templates, 'templates'); */
            
        }else{
            // page was not found
            $this->addUserMessageToNextRequest("The page ID was not recognizsed.", SmartestUserMessage::ERROR);
            $this->formForward();
        }
	    
	}
	
	public function clearList($get){
	    
	    $list_name = $this->getRequestParameter('assetclass_id');
        
        $page_id = $this->getRequestParameter('page_id');
        
        $page = new SmartestPage;
        
        if($page->hydrate($page_id)){
            
            $page->setDraftMode(true);
            
            $list = new SmartestCmsItemList;
            
            if($list->load($list_name, $page, true)){
                // this list was already defined
                $list->delete();
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was updated successfully.", SmartestUserMessage::SUCCESS);
            }else{
                $this->addUserMessageToNextRequest("The list \"".$list_name."\" was not defined.", SmartestUserMessage::INFO);
            }
            
            $this->formForward();
            
        }else{
            
            $this->addUserMessageToNextRequest("The page ID was not recognizsed.", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
	    
	}
	
	/* function insertList($get){
		
		$page_webid = $this->getRequestParameter('page_id');
		$page_id=$this->manager->getPageIdfromPageWebId($page_webid);
		$list_name = $this->getRequestParameter('list_name');
		$set_id = $this->getRequestParameter('dataset');
		$list_template = $this->getRequestParameter('listtemplate_name');
		$header_template = $this->getRequestParameter('header_template');
		$footer_template = $this->getRequestParameter('footer_template');
		$this->manager->insertList($page_id,$list_name,$set_id,$list_template,$header_template,$footer_template);
		
		$this->formForward();
			
	} */
	
	public function publishListsConfirm($get){
		$page_webid=$this->getRequestParameter('page_id');
		$version="draft";
		$undefinedLists=$this->manager->publishListsConfirm($page_webid, $version);
		$count=count($undefinedLists);
		return array ("undefinedLists"=>$undefinedLists,"page_id"=>$page_webid,"count"=>$count);
	}
	
	public function publishPageLists($get){
		$page_webid=$this->getRequestParameter('page_id');
		$this->manager->publishPageLists($page_webid);
		$this->formForward();
	}
	
	public function addItemSpace($get){
	    
	    $new_name = SmartestStringHelper::toVarName($this->getRequestParameter('name'));
	    $item_space = new SmartestItemSpace;
	    
	    if($item_space->exists($new_name, $this->getSite()->getId())){
	        // item space already exists with this name
	        $this->send(false, 'allow_continue');
	    }else{
	        
	        // get templates
	        $assetshelper = new SmartestAssetsLibraryHelper;
	        $templates = $assetshelper->getAssetsByTypeCode('SM_ASSETTYPE_ITEMSPACE_TEMPLATE', $this->getSite()->getId());
	        $this->send($templates, 'templates');
	        
	        // get sets
	        $du = new SmartestDataUtility;
	        $sets = $du->getDataSets(false, $this->getSite()->getId());
	        $this->send($sets, 'sets');
	        
	        $this->send($new_name, 'name');
	        
	        $this->send(true, 'allow_continue');
	    }
	    
	}
	
	public function insertItemSpace($get, $post){
	    
	    $new_name = SmartestStringHelper::toVarName($this->getRequestParameter('itemspace_name'));
	    $item_space = new SmartestItemSpace;
	    
	    if(strlen($new_name)){
	    
	        if($item_space->exists($new_name, $this->getSite()->getId())){
	            // item space already exists with this name
	            $this->addUserMessageToNextRequest('An itemspace with that name already exists', SmartestUserMessage::WARNING);
	        }else{
	        
	            $item_space->setName($new_name);
	            $item_space->setLabel($new_name);
	            $item_space->setSiteId($this->getSite()->getId());
	        
	            $dataset_id = (int) $this->getRequestParameter('itemspace_dataset_id');
	            $item_space->setDataSetId($dataset_id);
	        
	            $use_template = $this->getRequestParameter('itemspace_use_template');
	            $item_space->setUsesTemplate($use_template);
	        
	            if($use_template){
	                $template_id = (int) $this->getRequestParameter('itemspace_template_id');
    	            $item_space->setTemplateAssetId($template_id);
	            }
	        
	            $this->addUserMessageToNextRequest('An itemspace called \''.$new_name.'\' has been created.', SmartestUserMessage::SUCCESS);
	            $item_space->save();
	        }
	        
        }else{
            $this->addUserMessageToNextRequest('You didn\'t enter a name for the itemspace. Please try again.', SmartestUserMessage::WARNING);
        }
        
        $this->formForward();
        
	}
	
	public function editItemspace(){
	    
	    $item_space = new SmartestItemSpace;
	    $name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
	    
	    if($item_space->exists($name, $this->getSite()->getId())){
	        
	        // print_r($item_space);
	        $this->send($item_space, 'itemspace');
	        
	        $alh = new SmartestAssetsLibraryHelper;
            $this->send($alh->getAssetsByTypeCode("SM_ASSETTYPE_ITEMSPACE_TEMPLATE", $this->getSite()->getId()), 'templates');
            
            $du = new SmartestDataUtility;
	        $sets = $du->getDataSets(false, $this->getSite()->getId());
	        $this->send($sets, 'sets');
            
	        
	    }else{
	        $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            $this->formForward();
	    }
	    
	}
	
	public function updateItemspace(){
	    
	    $item_space = new SmartestItemSpace;
	    $id = SmartestStringHelper::toVarName($this->getRequestParameter('itemspace_id'));
	    
	    if($item_space->find($id)){
	        if(strlen($this->getRequestParameter('itemspace_label'))){
	            $item_space->setLabel($this->getRequestParameter('itemspace_label'));
	        }
	        if($this->getRequestParameter('itemspace_template_id') == "NONE"){
	            $item_space->setUsesTemplate(false);
	        }else if(is_numeric($this->getRequestParameter('itemspace_template_id'))){
	            $item_space->setUsesTemplate(true);
	            $item_space->setTemplateAssetId($this->getRequestParameter('itemspace_template_id'));
	        }
	        if(is_numeric($this->getRequestParameter('itemspace_dataset_id'))){
	            $item_space->setDataSetId($this->getRequestParameter('itemspace_dataset_id'));
	        }
	        $item_space->save();
	        $this->addUserMessageToNextRequest('The itemspace was sucessfully updated.', SmartestUserMessage::SUCCESS);
	        $this->formForward();
	    }else{
            $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            $this->formForward();
        }
	    
	}
	
	public function defineItemspace($get){
	    
	    $page = new SmartestPage;
	    $page_webid = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_webid)){
	        
	        $page->setDraftMode(true);
	        
	        $name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
	    
    	    $item_space = new SmartestItemSpace;
            
            if($item_space->exists($name, $this->getSite()->getId())){
            
                $definition = new SmartestItemSpaceDefinition;
            
                if($definition->load($name, $page, true)){
                    $definition_id = $definition->getItemId(true);
                }else{
                    $definition_id = 0;
                }
                
                $options = $item_space->getOptions();
                
                $this->send($definition_id, 'definition_id');
                $this->send($options, 'options');
                $this->send($item_space->__toArray(), 'itemspace');
                $this->send($page, 'page');
                
            }else{
                $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
                $this->formForward();
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The page ID wasn't recognized", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
	    
	}
	
	public function clearItemspaceDefinition($get, $post){
	    
	    $page = new SmartestPage;
	    $page_id = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $name = SmartestStringHelper::toVarName($this->getRequestParameter('assetclass_id'));
	    
    	    $item_space = new SmartestItemSpace;
        
            if($exists = $item_space->exists($name, $this->getSite()->getId())){
            
                $definition = new SmartestItemSpaceDefinition;
            
                if($definition->load($name, $page, true)){
                    // $definition->setItemSpaceId($item_space->getId());
                    // $definition->setPageId($page->getId());
                    $definition->delete();
                    $this->addUserMessageToNextRequest("The itemspace was successfully cleared", SmartestUserMessage::SUCCESS);
                }else{
                    $this->addUserMessageToNextRequest("The itemspace wasn't defined in the first place", SmartestUserMessage::INFO);
                }
                
                // $definition->setDraftItemId($this->getRequestParameter('item_id'));
                
                $definition->save();
                
            }else{
                $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The page ID wasn't recognized", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function updateItemspaceDefinition($get, $post){
	    
	    $page = new SmartestPage;
	    $page_id = $this->getRequestParameter('page_id');
	    
	    if($page->hydrate($page_id)){
	        
	        $page->setDraftMode(true);
	        
	        $name = SmartestStringHelper::toVarName($this->getRequestParameter('itemspace_name'));
	    
    	    $item_space = new SmartestItemSpace;
        
            if($exists = $item_space->exists($name, $this->getSite()->getId())){
            
                $definition = new SmartestItemSpaceDefinition;
            
                if(!$definition->load($name, $page, true)){
                    $definition->setItemSpaceId($item_space->getId());
                    $definition->setPageId($page->getId());
                }
                
                $definition->setDraftItemId($this->getRequestParameter('item_id'));
                
                $this->addUserMessageToNextRequest("The itemspace ID was successfully updated", SmartestUserMessage::SUCCESS);
                $definition->save();
                
            }else{
                $this->addUserMessageToNextRequest("The itemspace ID wasn't recognized", SmartestUserMessage::ERROR);
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("The page ID wasn't recognized", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
	    
	}
	
	public function openItem($get){
	    
	    $item = new SmartestItem;
	    
	    if($item->findBy('slug', $this->getRequestParameter('assetclass_id'), $this->getSite()->getId())){
	        
	        $this->redirect('/datamanager/openItem?item_id='.$item->getId());
	        
	    }
	    
	}
	
	public function addPageUrl($get){
	    
	    $page_webid=$this->getRequestParameter('page_id');
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($page_webid)){
		    
		    $page->setDraftMode(true);
		    
		    $page_type = $page->getType();
		    $is_valid_item = false;
		    
		    if($page_type == 'ITEMCLASS' || $page_type == 'SM_PAGETYPE_ITEMCLASS' || $page_type == 'SM_PAGETYPE_DATASET'){
		        
		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){
		            
		            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
		                
		                if($page->getDatasetId() == $item->getModel()->getId()){
		                    $this->send($item, 'item');
		                    $is_valid_item = true;
	                    }
		                
		            }
		            
		        }
		        
		    }
		    
		    // $ishomepage = $this->getRequestParameter('ishomepage');
		    $page_id = $page->getId();
		    $page_info = $page;
		    $site = $page->getSite();
		    $this->send($is_valid_item, 'is_valid_item');
		    // $page_info['site'] = $page->getSite();
		    
		    $this->send($page_info, "pageInfo");
		    $this->send($site, "site");
		    $this->send($page->isHomePage(), "ishomepage");
		
	    }
	    
		// return array("pageInfo"=>$page_info, "msg"=>$msg, "ishomepage"=>$ishomepage );
	}
	
	public function insertPageUrl($get,$post){
		
		$url = new SmartestPageUrl;
		
		if(!$this->getRequestParameter('page_url')){
		    $this->addUserMessage("You didn't enter a URL.", SmartestUserMessage::WARNING);
		    $this->forward('websitemanager', 'addPageUrl');
		}else if($url->existsOnSite($this->getRequestParameter('page_url'), $this->getSite()->getId())){
		    $this->addUserMessage("That URL already exists for another page.", SmartestUserMessage::WARNING);
		    $this->forward('websitemanager', 'addPageUrl');
		}else{
		    
		    $page = new SmartestPage;
		    
		    if($page->hydrate($this->getRequestParameter('page_id'))){
		        
		        $url = new SmartestPageUrl;
		        $url->setPageId($page->getId());
		        $url->setIsDefault(0);
		        
		        $page_type = $page->getType();

    		    if($page_type == 'ITEMCLASS' || $page_type == 'SM_PAGETYPE_ITEMCLASS' || $page_type == 'SM_PAGETYPE_DATASET'){

    		        if($this->getRequestParameter('item_id') && is_numeric($this->getRequestParameter('item_id'))){

    		            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){

    		                if($page->getDatasetId() == $item->getModel()->getId()){
    		                    
    		                    $url_string = SmartestStringHelper::sanitize($this->getRequestParameter('page_url'));
    		                    
    		                    if($this->getRequestParameter('page_url_type') == 'SINGLE_ITEM'){
    		                        $url_string = str_replace(':name', $item->getSlug(), $url_string);
    		                        $url_string = str_replace(':id', $item->getId(), $url_string);
    		                        $url_string = str_replace(':webid', $item->getWebid(), $url_string);
    		                        $url->setType($this->getRequestParameter('forward_to_default') ? 'SM_PAGEURL_ITEM_FORWARD' : 'SM_PAGEURL_SINGLE_ITEM');
		                        }else{
		                            $url_string = str_replace($item->getSlug(), ':name', $url_string);
    		                        $url_string = str_replace($item->getId(), ':id', $url_string);
    		                        $url_string = str_replace($item->getWebid(), ':webid', $url_string);
		                            $url->setType($this->getRequestParameter('forward_to_default') ? 'SM_PAGEURL_INTERNAL_FORWARD' : 'SM_PAGEURL_NORMAL');
		                        }
		                        
		                        if($this->getRequestParameter('forward_to_default') && $this->getRequestParameter('forward_to_default') == '1'){
		                            $url->setRedirectType($this->getRequestParameter('url_redirect_type'));
		                        }
		                        
		                        $url->setItemId($item->getId());
		                        $url->setUrl($url_string);
    		                    
    	                    }

    		            }

    		        }

    		    }else{
		            
		            $url->setUrl(SmartestStringHelper::sanitize($this->getRequestParameter('page_url')));
		            $url->setType($this->getRequestParameter('forward_to_default') ? 'SM_PAGEURL_INTERNAL_FORWARD' : 'SM_PAGEURL_NORMAL');
		        
	            }
	               
		        $url->save();
		        SmartestLog::getInstance('site')->log("{$this->getUser()} added URL '{$this->getRequestParameter('page_url')}' to page: {$page->getTitle()}.", SmartestLog::USER_ACTION);
		        $this->addUserMessageToNextRequest("The new URL was successully added.", SmartestUserMessage::SUCCESS);
		        
		    }else{
		        $this->addUserMessageToNextRequest("The page ID was not recognized.", SmartestUserMessage::ERROR);
		    }
		    
		}
		
		$this->formForward();
		
		/* $page_webid=$this->getRequestParameter('page_webid');
		$page_id = $this->manager->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
		$page_url=$this->getRequestParameter('page_url');
		$url_count = $this->manager->checkUrl($page_url);
		
		if($url_count > 0){
			header("Location:".$this->domain.$this->module."/addPageUrl?page_id=$page_webid&msg=1");
		}else{
			$this->manager->insertNewUrl($page_id,$page_url);
			$this->formForward();
		} */
	}
	
	public function editPageUrl($get){
		
		$page_webid = $this->getRequestParameter('page_id');
		
		$page = new SmartestPage;
		$url = new SmartestPageUrl;
		
		if($url->find($this->getRequestParameter('url_id'))){
		    
		    $this->send($url, "url");
		
    		if($page->find($url->getPageId())){
    		    
    		    if($page->getType() == "ITEMCLASS"){
    		        $model = new SmartestModel;
    		        $model->find($page->getDatasetId());
    		        $this->send($model, "model");
    		    }
    		    
    		    $page->setDraftMode(true);
    		    $site = $page->getSite();
    		    $this->send($site, 'site');
    		    $this->send($page->isHomepage(), "ishomepage");
    		    $this->send($page, "pageInfo");
    	    }
	    }
	}
	
	public function updatePageUrl($get,$post){
		
		$page_webid = $this->getRequestParameter('page_webid');
		$page_url = $this->getRequestParameter('page_url');
		$url_id = $this->getRequestParameter('url_id');
		
		$url = new SmartestPageUrl;
		$url->find($url_id);
		
		if($this->getRequestParameter('forward_to_default') && $this->getRequestParameter('forward_to_default') == 1){
		    
		    if(in_array($url->getType(), array('SM_PAGEURL_ITEM_FORWARD', 'SM_PAGEURL_SINGLE_ITEM'))){
		        
		        if($url->getIsDefault()){
		            $url->setType('SM_PAGEURL_SINGLE_ITEM');
		            $this->addUserMessageToNextRequest("The default URL cannot also be an internal forward");
		        }else{
		            $url->setType('SM_PAGEURL_ITEM_FORWARD');
		        }
		        
		    }else{
		    
		        if($url->getIsDefault()){
		            $url->setType('SM_PAGEURL_NORMAL');
		            $this->addUserMessageToNextRequest("The default URL cannot also be an internal forward");
		        }else{
		            $url->setType('SM_PAGEURL_INTERNAL_FORWARD');
		        }
		    
	        }
	        
	        $url->setRedirectType($this->getRequestParameter('url_redirect_type'));
		    
		}else{
		    if(in_array($url->getType(), array('SM_PAGEURL_ITEM_FORWARD', 'SM_PAGEURL_SINGLE_ITEM'))){
		        $url->setType('SM_PAGEURL_SINGLE_ITEM');
	        }else{
	            $url->setType('SM_PAGEURL_NORMAL');
	        }
		}
		
		$url->setUrl($page_url);
		$url->save();
		
		// $pageurl_id = $this->manager->database->specificQuery("pageurl_id", "pageurl_url", $page_oldurl, "PageUrls");
		// $pageurl_id;
		// $page_id = $this->manager->database->specificQuery("page_id", "page_webid", $page_webid, "Pages");
		// $this->manager->updatePageUrl($page_id,$pageurl_id,$page_url);
		
		$this->formForward();
	}
	
	public function deletePageUrl($get){
		
		$url = new SmartestPageUrl;
		$p = new SmartestPage;
		
		if($url->hydrate($this->getRequestParameter('url'))){
		    
		    $p->hydrate($url->getPageId());
		    
		    $u = $url->getUrl();
		    $url->delete();
		    SmartestLog::getInstance('site')->log("{$this->getUser()} deleted URL '$u' from page: {$p->getTitle()}.", SmartestLog::USER_ACTION);
		    $this->addUserMessageToNextRequest("The URL has been successfully deleted. It's recommended that you now clear the pages cache to avoid dead links.", SmartestUserMessage::SUCCESS);
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("The URL ID was not recognized.", SmartestUserMessage::ERROR);
	        
	    }
	    
		$this->formForward();
	}
	
	public function setPageDefaultUrl($get){
	    
	    $page = new SmartestPage;
	    
	    if($page->hydrate($this->getRequestParameter('page_id'))){
	        
	        $page->setDraftMode(true);
	        
	        $result = $page->setDefaultUrl($this->getRequestParameter('url'));
	        
	        if(!$result){
	            if($url == (int) $url){
	                $this->addUserMessageToNextRequest("The URL ID was not recognized.", SmartestUserMessage::ERROR);
                }else{
                    $this->addUserMessageToNextRequest("The URL is already in use for another page.", SmartestUserMessage::ERROR);
                }
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The page ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function editField($get){
		// This is a hack. Sorry.
		$this->redirect($this->getRequest()->getDomain().'metadata/defineFieldOnPage?page_id='.$this->getRequestParameter('page_id').'&assetclass_id='.$this->getRequestParameter('assetclass_id'));
	}
	
	public function setLiveProperty($get){
		// This is a hack. Sorry.
		$this->redirect($this->getRequest()->getDomain().'metadata/setLiveProperty?page_id='.$this->getRequestParameter('page_id').'&assetclass_id='.$this->getRequestParameter('assetclass_id'));
	}
	
	public function undefinePageProperty($get){
		// This is a hack. Sorry.
		$this->redirect($this->getRequest()->getDomain().'metadata/undefinePageProperty?page_id='.$this->getRequestParameter('page_id').'&assetclass_id='.$this->getRequestParameter('assetclass_id'));
	}
	
	public function pageGroups(){
	    
	    $this->setFormReturnUri();
	    $this->setFormReturnDescription('page groups');
	    
	    $pgh = new SmartestPageGroupsHelper;
	    $groups = $pgh->getSiteGroups($this->getSite()->getId());
	    $this->send($groups, 'groups');
	    $this->setTitle('Page groups');
	    
	}
	
	public function addPageGroup(){
	    
	    $this->send($this->getSite()->getNormalPagesList(true, true), 'pages');
	    
	}
	
	public function insertPageGroup(){
	    
	    $label = $this->getRequestParameter('pagegroup_label');
	    
	    if(strlen($label)){
	    
    	    $name = SmartestStringHelper::toVarName($label);
    
    	    $pg = new SmartestPageGroup;
    
    	    if(!$pg->hydrateBy('name', $name) && !$pg->hydrateBy('label', $name)){
	        
    	        $pg->setName($name);
    	        $pg->setLabel($label);
    	        $pg->setSiteId($this->getSite()->getId());
    	        $pg->save();
    	        
    	        $this->addUserMessageToNextRequest('Your new page group menu was saved successfully.', SmartestUserMessage::SUCCESS);
	        
    	        if($this->getRequestParameter('continue_to_pages')){
    	            $this->redirect('/websitemanager/editPageGroup?group_id='.$pg->getId());
                }else{
                    $this->redirect('/smartest/pagegroups');
                }
            
    	    }else{
    	        $this->addUserMessage('A page group with that name already exists.', SmartestUserMessage::INFO);
    	        $this->forward('websitemanager', 'addPageGroup');
    	    }
	    
        }else{

            $this->addUserMessage('You must enter a valid label for your page group.', SmartestUserMessage::ERROR);
            $this->forward('websitemanager','addPageGroup');

        }
	    
	}
	
	public function editPageGroup(){
	    
	    $group = new SmartestPageGroup;
	    
	    if($group->find($this->getRequestParameter('group_id'))){
	        $this->send($group, 'pagegroup');
	    }
	    
	}
	
	public function updatePageGroup(){
	    
	}

}