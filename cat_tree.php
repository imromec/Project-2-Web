<?php 

require_once('./app/Mage.php');
Mage::app();

 $rootCatId = Mage::app()->getStore()->getRootCategoryId();
   $catlistHtml = getTreeCategories($rootCatId, false);
   echo $catlistHtml;

function getTreeCategories($parentId, $isChild){
    $allCats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_active','1')
                ->addAttributeToFilter('include_in_menu','1')
                ->addAttributeToFilter('parent_id',array('eq' => $parentId))
                ->addAttributeToSort('position', 'asc');
               
    $class = ($isChild) ? "sub-cat-list" : "cat-list";
    $html = array();
    foreach($allCats as $category)
    {
        $html["category_id"] = $category->getId();
		 $html["category_name"] = $category->getName();
        $subcats = $category->getChildren();
		
        if($subcats != ''){
            $htmls = getTreeCategories($category->getId(), true);
			$html["cat_subcategory_id"] = $htmls->getId();
			$html["cat_subcategory_name"] = $htmls->getName();
			
        }
//        $html .= '</li>';
    }
  
	echo json_encode($html);
	
}

?> 