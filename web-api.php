<?php
/* License
*
*   Create by App-z.net
*
*   This software is distributed under the [GNU GPL V3](http://www.gnu.org/licenses/gpl.html) License.
*
*/


//
//	Set key for access to functionality this script
//
define('WEB_API_KEY', '3579709e01476881afcd213cd68313b7');




require_once('../app/Mage.php');
Mage::app();


$key = Mage::app()->getRequest()->getParam('key');


if ( !isset($key) || $key != WEB_API_KEY ) {

	$json = array('success' => false, 'code' => 20, 'message' => 'Invalid secret key');
	print_r(json_encode($json));


}elseif( Mage::app()->getRequest()->getParam('route') == "feed/web_api/categories" ){

	# -- $_GET params ------------------------------
	$parent = Mage::app()->getRequest()->getParam('parent', 0);
	$level = Mage::app()->getRequest()->getParam('level', 1);
	# -- End $_GET params --------------------------
	print_r(json_encode(getCategoryTree($parent, $level)));


}elseif( Mage::app()->getRequest()->getParam('route') == "feed/web_api/category" ){

	# -- $_GET params ------------------------------
	$category_id = Mage::app()->getRequest()->getParam('id', 0);
	# -- End $_GET params --------------------------
	
	print_r(json_encode(getCategory($category_id)));


}elseif(Mage::app()->getRequest()->getParam('route') == "feed/web_api/products"){

	# -- $_GET params ------------------------------
	$category_id = Mage::app()->getRequest()->getParam('category', 0);
	# -- End $_GET params --------------------------
	print_r(json_encode(products($category_id)));


}elseif(Mage::app()->getRequest()->getParam('route') == "feed/web_api/product"){

	# -- $_GET params ------------------------------
	$product_id = Mage::app()->getRequest()->getParam('id', 0);
	# -- End $_GET params --------------------------
	print_r(json_encode(product($product_id)));

}elseif(Mage::app()->getRequest()->getParam('route') == "feed/web_api/random"){

	# -- $_GET params ------------------------------
	$limit = Mage::app()->getRequest()->getParam('limit', 4);
	# -- End $_GET params --------------------------
	print_r(json_encode(random_products($limit)));
}elseif(Mage::app()->getRequest()->getParam('route') == "feed/web_api/slider"){

	# -- End $_GET params --------------------------
	print_r(json_encode(sliderImages()));
}




//
//	Random Products Items
//	
//	http://localhost/magento/web-api.php?route=feed/web_api/random&limit=4&key=key1
//
function random_products($limit){
	$json = array('success' => true);

	$products = Mage::getModel('catalog/product')->getCollection();
	$products->addAttributeToSelect(array('name', 'thumbnail', 'price')); //feel free to add any other attribues you need.

	Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
	Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products); 
	$products->getSelect()->order('RAND()')->limit($limit);

	foreach($products as $product){ 
		$json['products'][] = array(
				'id'		=> $product->getId(),
				'name'		=> $product->getName(),
				'href'		=> $product->getProductUrl(),
				'thumb'		=> (string)Mage::helper('catalog/image')->init($product, 'thumbnail'),
				'pirce'		=> Mage::helper('core')->currency($product->getPrice(), true, false) //." ".$currencyCode,
			);
	}
	return $json;
}


//
//	Product Item
//	
//	http://localhost/magento/web-api.php?route=feed/web_api/product&id=800&key=key1
//
function product($product_id){
	$json = array('success' => true);

	$product = Mage::getModel('catalog/product')->load($product_id);
	$json['product'] = array();
	$json['product']['id'] = $product->getId();
	$json['product']['name'] = $product->getName();
	$json['product']['price'] = Mage::helper('core')->currency($product->getPrice(), true, false);
	$json['product']['description'] = $product->getDescription();
	$json['product']['image'] = (string)Mage::helper('catalog/image')->init($product, 'image');

	$mediaGallery = Mage::getModel('catalog/product')->load($product->getId())->getMediaGalleryImages()->getItems();
	$json['product']['images'] = array();
        //loop through the images
        foreach ($mediaGallery as $image){
            $json['product']['images'][] = $image['url'];
        }
	return $json;
}


//
//	Products in category
//
//	http://localhost/magento/web-api.php?route=feed/web_api/products&category=4&key=key1
//
function products($category_id){

	$json = array('success' => true, 'products' => array());

	$category = Mage::getModel ('catalog/category')->load($category_id);

	$products = Mage::getResourceModel('catalog/product_collection')
		          // ->addAttributeToSelect('*')
		          ->AddAttributeToSelect('name')
		          ->addAttributeToSelect('price')
		          ->addFinalPrice()
		          ->addAttributeToSelect('small_image')
		          ->addAttributeToSelect('image')
		          ->addAttributeToSelect('thumbnail')
		          ->addAttributeToSelect('short_description')
		          ->addUrlRewrite()
		          ->AddCategoryFilter($category)
				  ->addAttributeToSelect('*')
					->addAttributeToFilter(
    				'status',
    				array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
					);
				  

	Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

	$currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();


	foreach($products as $product){ 
		$json['products'][] = array(
				'id'                    => $product->getId(),
				'name'                  => $product->getName(), 
				'description'           => $product->getShortDescription(),
				'pirce'                 => Mage::helper('core')->currency($product->getPrice(), true, false), //." ".$currencyCode,
				'href'                  => $product->getProductUrl(),
				'thumb'                 => (string)Mage::helper('catalog/image')->init($product, 'thumbnail')
			);
	}
	return $json;
}



//
//	Categories
//
//	http://localhost/magento/web-api.php?route=feed/web_api/categories&parent=0&level=2&key=key1
//
function getCategoryTree( $parent = 0, $recursionLevel = 1 )
{
    if($parent == 0){
        $parent = Mage::app()->getStore()->getRootCategoryId();
    }else{
	$parent = Mage::getModel('catalog/category')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->addIsActiveFilter()
		 ->load($parent)
		 ->getId();
    }




     $tree = Mage::getResourceModel('catalog/category_tree');
    /* @var $tree Mage_Catalog_Model_Resource_Category_Tree */

    $nodes = $tree->loadNode($parent)
        ->loadChildren($recursionLevel)
        ->getChildren();
    $tree->addCollectionData(null, false, $parent);

    $json = array('success' => true);

    $result = array();

    foreach ($nodes as $node) {
	
        $result[] = array(
			'category_id'   => $node->getData('entity_id'),
			'parent_id'     => $parent,
			'name'          => $node->getName(),
			'categories'    => getNodeChildrenData($node));
    }
	
    $json['categories'] = $result;
    return $json;
}

function getCategory( $id )
{
	$result[] = array(
    "subcat"=>getSubCatDatawithproducts($id)
	);
	$json['categories'] = $result;
    return $json;
}

function getSubCatDatawithproducts($id)
{
$collection = Mage::getResourceModel('catalog/category_collection')
    ->addAttributeToSelect('*')
	->addIsActiveFilter()
   ->addAttributeToFilter('is_active', array('in' => array(0,1)))
  ->addAttributeToFilter('parent_id', $id);
foreach ($collection as $childNode) {
        $result[] = array(
			'category_id'   => $childNode->getData('entity_id'),
			'name'          => $childNode->getData('name'),
			'products'       => products($childNode->getData('entity_id')));
	 }
 return $result;

}



function getNodeChildrenData(Varien_Data_Tree_Node $node)
{
    foreach ($node->getChildren() as $childNode) {
	
	$image=Mage::getModel('catalog/category')->load($childNode->getData('entity_id'))->getThumbnail();
	if($image)
	{
	$image_url=Mage::getBaseUrl('media').'catalog/category/'.$image;
	}
	else
	{
	$image_url="";
	}
	//echo $cat; die;
        $result[] = array(
			'category_id'   => $childNode->getData('entity_id'),
			'parent_id'     => $node->getData('entity_id'),
			'name'          => $childNode->getData('name'),
			'image'          =>  $image_url,
			'categories'    => getSubCatData($childNode->getData('entity_id')));
	 }
    return $result;
}

function getSubCatData($id)
{
$collection = Mage::getResourceModel('catalog/category_collection')
    ->addAttributeToSelect('*')
   ->addAttributeToFilter('is_active', array('in' => array(0,1)))
  ->addAttributeToFilter('parent_id', $id);
foreach ($collection as $childNode) {
        $result[] = array(
			'category_id'   => $childNode->getData('entity_id'),
			'name'          => $childNode->getData('name'));
	 }
 return $result;

}
function sliderImages($id)
{

$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
$sql        = "Select * from image_slider";
$rows       = $connection->fetchAll($sql); //fetchRow($sql), fetchOne($sql),...
$result=array();
foreach($rows as $row)
{
	if($row['image_status']==1)
	{
	$result[]=array(
	'slider_id'=>$row['slider_id'],
	'image_title'=>$row['image_title'],
	'image_name'=>$row['image_name'],
	'image_description'=>$row['image_description'],
	'video'=>$row['video'],
	'imageposition'=>$row['imageposition'],
	'image_link'=>Mage::getBaseUrl('media').$row['image_link'],
	);
	
	}

}
$arr=array(
	"status"=>true,
	"data"=>array(
	"products"=>$result
	)
	);
return $arr;


}


?>