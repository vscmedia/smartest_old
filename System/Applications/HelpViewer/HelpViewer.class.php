<?php

class HelpViewer extends SmartestSystemApplication{
	
  	public function startPage(){
	
  	}
 
    public function getContent(){
        
    }
    
    public function getAjaxContent(){
        
        $code = $this->getRequestParameter('help_code');
        $p = explode(':', $code);
        $app_shortname = $p[0];
        $node_id = $p[1];
        $apps = SmartestPersistentObject::get('controller')->getAllModulesByShortName();
        
        if(isset($apps[$app_shortname])){
            $help_file = $apps[$app_shortname]['directory'].'Content/LanguagePacks/eng/Help/index.yml';
            // echo $help_file;
            if(is_file($help_file)){
                $help_config = SmartestYamlHelper::fastLoad($help_file);
                if(isset($help_config['help'][$node_id])){
                    $template = $apps[$app_shortname]['directory'].'Content/LanguagePacks/eng/Help/Presentation/'.$help_config['help'][$node_id]['content'];
                    if(is_file($template)){
                        $this->send($help_config['help'][$node_id]['title'], 'title');
                        $this->send($template, 'content');
                    }else{
                        // help content file not found
                        $this->send("Oops!", 'title');
                        $this->send($this->getRequest()->getMeta('_module_dir').'Presentation/Special/notfound.tpl', 'content');
                        $this->send($template, 'lost_file');
                    }
                }else{
                    // help node id not recognised// help content file not found
                    $this->send("Node not recognized", 'title');
                    $this->send($this->getRequest()->getMeta('_module_dir').'Presentation/Special/notfound.tpl', 'content');
                }
            }else{
                // application does not support help system
            }
        }else{
            // unrecognized application code
        }
    }

    public function search($get){
        
    } 
  
}