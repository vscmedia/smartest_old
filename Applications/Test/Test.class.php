<?php

require_once 'System/Helpers/ItemsHelper.class.php';
// require_once 'Managers/SmartSetManager.class.php';

class Test extends SmartestApplication{

	function __moduleConstruct(){
		// echo 'module constructed<br />';
	}
	
	function testMethod(){

		// echo "before<br>";
		$dataq = new DataQuery(54);
 		
 		$dataq->addCondition(CHANNEL, DataQuery::EQUAL, "testing");
 		$dataq->addCondition(CHANNEL, DataQuery::EQUAL, "channels");
		
		$result = $dataq->select();
		
		$this->saveData('test', $result);
		$result = $this->loadData('test');
		
		print_r($result);
		
		// 		$dataq->selectAsIds();echo "<br>";
		// 		$dataq->selectToArray();echo "<br>";
		/*$data=$dataq->select();echo "<br><br>select() output--<br>";var_dump($data);
		echo "<br><br><br>selectproperty() output--";$data->setProperty(CHANNEL,"testing");
		echo "<br><br><br>save()--";$data->save();*/

		// $array=$dataq->getDataArray($data);echo "<br><br><br>getDataArray()--";print_r($array);
		// $arr2=$dataq->setSortOrder($array,asc);echo "<br><br><br>setSortOrder()in asc --";var_dump($arr2);
		// echo "<br><br><br>";
		// $data->delete();
		// $dataq=new ItemsManager();$dataq->itemClassSettings(51);
		
		echo '<br>not crashed';

	}
	
	function newapi(){
		
		$query = new SmartestQuery(NewsStory);
		$query->add(NewsStory::TITLE, "o", SmartestQuery::CONTAINS);
		$query->add(NewsStory::DESCRIPTION, "e", SmartestQuery::CONTAINS);
		$query->add(NewsStory::NAME, "e", SmartestQuery::CONTAINS);
		
		$result = $query->doSelect(true);
		
		$result->sort(NewsStory::DESCRIPTION);
		
		// print_r($result->getItems());
		
		foreach($result->getItems() as $ns){
			echo $ns->getTitle().': ';
			echo $ns->getDescription().'<br />';
			$ns->setTitle($ns->getTitle().' new');
			// print_r($ns);
		}
		
	}
	
	function libhelper($get){
		// print_r(SmartestLibHelper::getLoaded());
		$apple = SmartestHttpRequestHelper::getWebPage('http://www.whitehouse.gov/news/releases/2007/08/20070821-6.html');
		$apple = str_replace('Defense', 'Mass Murder', $apple);
		$apple = str_replace('President', 'Supreme Emperor', $apple);
		$apple = str_replace('American', 'evil', $apple);
		$apple = str_replace('America', 'Hell', $apple);
		echo $apple;
		
		// $london_page = SmartestHttpRequestHelper::getWebPage('http://en.wikipedia.org/wiki/Special:Export/'.$get['subject']);
		// $data = SmartestXmlHelper::loadString($london_page);
		
		// print_r($data);
		
		// echo '<br />not crashed';
	}
	
	function xmltest(){
	    $page = SmartestHttpRequestHelper::getWebPage('http://en.wikipedia.org/wiki/Special:Export/Dominique_DiPiazza');
	    $data = SmartestXmlHelper::loadString($page);
	    $serializer = new SmartestXmlSerializer('mediawiki', $data);
	    // echo $page;
	    // print_r($data);
	    $xml = $serializer->serialize();
	    echo $xml;
	    // print_r($serializer);
	}
	
	function testjson(){
	    $page = new NewsStory;
	    // $json_page = json_encode($page->__toArray());
	    print_r($page->__toArray());
	    echo '<script language="javascript">var smartest_page = "'.$json_page.'"; alert (smartest_page);</script>';
	}

	var $itemsManager;
	var $smartSetManager;
  
	function testfeedset($get){	
		
		$this->itemsManager = new ItemsHelper();
		$this->smartSetManager = new SmartSetManager();

		$const['EQUALS'] = 0;
		$const['NOT_EQUAL'] = 1;
		$const['CONTAINS'] = 2;
		$const['STARTS_WITH'] = 4;
		$const['ENDS_WITH'] = 5;
	
		$set_id = $get['set_id'];
		$set = $this->smartSetManager->getSet($set_id);
		$model_id = $set['set_itemclass_id'];		
		$rules = $this->smartSetManager->getSetRules($set_id);

		
		$dataquery = new DataQuery($model_id);
		
		foreach($rules as $rule){
			$dataquery->addCondition($rule['setrule_itemproperty_id'], $const[$rule['setrule_rule']], $rule['setrule_value']);		
		}
		
		$data = $dataquery->selectToArray();
		var_dump($data);
	}
	
	function testFunction(){
	    /* $cat = new SmartestAssetTypeCategory;
	    $cat->hydrate(1);
	    $cat->getTypes();
	    print_r($cat); */
	    
	    // echo SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Presentation/Assets/helloworld-1.tpl').'<br />';
	    
	}

}