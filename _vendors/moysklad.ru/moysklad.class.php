<?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-03-30 13:34
**/
include_once('macroses/REQUIRED_VAR.php');

include_once('moysklad_element_base.class.php');

class moysklad_exception extends BaseException{};
class moysklad_exception_absentElement extends moysklad_exception{};
class moysklad_exception_unemplimented extends moysklad_exception{};
class moysklad_exception_novalid extends moysklad_exception{};

/**
* Main moysklad XML class implementation.
* WARNING: It is very small part implemented. Only vast majority to goods exchange.
* @TODO Implement full by XML Scheme http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @author Pavel Alexeev aka Pahan-Hubbitus
* @created 2010-03-30 13:37
* @copyright 2010 Pavael Alexeev Aka Pahan-Hubbitus
**/
class moysklad{
	const XML_SCHEMA = 'http://www.moysklad.ru/schema/exchange-1.2.0.xsd';

	private $dom_;		// Main DOMDocument
	private $xpath_;	// DOMXpath object to perfom any queries

	/**
	* If explicit null element converted at start in something like:
	*	<workflow xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
	* @var array
	**/
	protected $root_elements = array(
		'workflow'				=> null
		,'shareModes'				=> null
		,'scripts'				=> null
		,'messages'				=> null
		,'customEntityMetadata'		=> null
		,'embeddedEntityMetadata'	=> null
		,'entityTemplatesMetadata'	=> null
		,'reportTemplatesMetadata'	=> null
		,'customEntity'			=> ''	// Empty
		,'reason'					=> ''
		,'currencies'				=> ''	// Empty
		,'country'				=> ''	// Empty
		,'gtd'					=> ''	// Empty
		,'uoms'					=> ''
		,'goodFolders'				=> ''
		,'goods'					=> ''
		,'service'				=> ''	// Empty
		,'things'					=> ''	// Empty
		,'myCompany'				=> ''	// Empty
		,'agents'					=> ''
		,'companies'				=> ''
		,'persons'				=> ''
		,'places'					=> null
		,'warehouses'				=> ''
		,'project'				=> ''	// Empty
		,'contract'				=> ''	// Empty
		,'processingPlans'			=> ''
		,'consignments'			=> ''
		,'priceLists'				=> null
		,'deliveries-demand'		=> null
		,'deliveries-supply'		=> null
		,'inventories'				=> null
		,'moves'					=> null
		,'losses'					=> null
		,'enters'					=> null
		,'invoicesIn'				=> null
		,'invoicesOut'				=> null
		,'processings'				=> null
		,'customerOrders'			=> null
		,'purchaseOrders'			=> null
		,'connectors'				=> null
	);

	public function __construct(){
		// DTD. http://www.php.net/manual/en/book.dom.php#78929
		$this->dom_ = new DOMDocument;
		$this->dom_->encoding = 'UTF-8';
		$this->dom_->validateOnParse = true;
		$this->dom_->standalone = true;
		$this->xpath_ = new DOMXPath($this->dom_);

		// This is 'constant' part
		$exchange = $this->dom_->appendChild($this->dom_->createElement('exchange'));

		foreach ($this->root_elements as $item => $value){
			include_once('moysklad_' . ($item = str_replace('-', '_', $item)). '.class.php');
			$__class_name = 'moysklad_' . $item;
			$itemObj = new $__class_name($value);
			$this->commonAddRootElement($item, $itemObj);
		}
	}#__c

	private function commonAddRootElement($rootElementName, moysklad_element_base &$elem){
		$this->dom_->documentElement->appendChild($this->dom_->importNode($elem, true));
	}#m commonAddElement

	private function commonAddElement($rootElementName, moysklad_element_base &$elem){
		$this->dom_->getElementsByTagName($rootElementName)->item(0)->appendChild($this->dom_->importNode($elem, true));
	}#m commonAddElement

	/**
	* Add workflow element into document.
	*
	* @param	Object()	$item
	* @return	&$this
	**/
	public function &add_workflow(moysklad_workflow $item){
		$this->commonAddElement('workflow', $item);
		return $this;
	}#m add_workflow

	/**
	* Add shareMode element into document.
	*
	* @param	Object()	$item
	* @return	&$this
	**/
	public function &add_shareMode(moysklad_workflow $item){
		$this->commonAddElement('shareModes', $item);
		return $this;
	}#m add_shareMode

	/**
	* Add script element into document.
	*
	* @param	Object()	$item
	* @return	&$this
	**/
	public function &add_script(moysklad_workflow $item){
		$this->commonAddElement('scripts', $item);
		return $this;
	}#m add_script

	/**
	* Add message element into document.
	*
	* @param	Object(moysklad_message)	$item
	* @return	&$this
	**/
	public function &add_message(moysklad_message $item) {
		$this->commonAddElement('messages', $item);
		return $this;
	}#m add_message

	/**
	* Add customEntityMetadata element into document.
	*
	* @param	Object(moysklad_customEntityMetadata)	$item
	* @return	&$this
	**/
	public function &add_customEntityMetadata(moysklad_customEntityMetadata $item) {
		$this->commonAddElement('customEntityMetadata', $item);
		return $this;
	}#m add_customEntityMetadata

	/**
	* Add embeddedEntityMetadata element into document.
	*
	* @param	Object(moysklad_embeddedEntityMetadata)	$item
	* @return	&$this
	**/
	public function &add_embeddedEntityMetadata(moysklad_embeddedEntityMetadata $item){
		$this->commonAddElement('embeddedEntityMetadata', $item);
		return $this;
	}#m add_embeddedEntityMetadata


	/**
	* Add entityTemplatesMetadata element into document.
	*
	* @param	Object(moysklad_entityTemplatesMetadata)	$item
	* @return	&$this
	**/
	public function &add_entityTemplatesMetadata(moysklad_entityTemplatesMetadata $item) {
		$this->commonAddElement('entityTemplatesMetadata', $item);
		return $this;
	}#m add_entityTemplatesMetadata


	/**
	* Add reportTemplatesMetadata element into document.
	*
	* @param	Object(moysklad_reportTemplatesMetadata)	$item
	* @return	&$this
	**/
	public function &add_reportTemplatesMetadata(moysklad_reportTemplatesMetadata $item){
		$this->commonAddElement('reportTemplatesMetadata', $item);
		return $this;
	}#m add_reportTemplatesMetadata


	/**
	* Add customEntity element into document.
	*
	* @param	Object(moysklad_customEntity)	$item
	* @return	&$this
	**/
	public function &add_customEntity(moysklad_customEntity $item) {
		$this->commonAddElement('customEntity', $item);
		return $this;
	}#m add_customEntity

	/**
	* Add lossReason element into document.
	*
	* @param	Object(moysklad_lossReason)	$item
	* @return	&$this
	**/
	public function &add_lossReason(moysklad_lossReason $item) {
		$this->commonAddElement('reason', $item);
		return $this;
	}#m add_lossReason

	/**
	* Add enterReason element into document.
	*
	* @param	Object(moysklad_enterReason)	$item
	* @return	&$this
	**/
	public function &add_enterReason(moysklad_enterReason $item) {
		$this->commonAddElement('reason', $item);
		return $this;
	}#m add_enterReason

	/**
	* Add currency element into document.
	*
	* @param	Object(moysklad_currency)	$item
	* @return	&$this
	**/
	public function &add_currency(moysklad_currency $item) {
		$this->commonAddElement('currencies', $item);
		return $this;
	}#m add_currency

	/**
	* Add country element into document.
	*
	* @param	Object(moysklad_country)	$item
	* @return	&$this
	**/
	public function &add_country(moysklad_country $item) {
		$this->commonAddElement('country', $item);
		return $this;
	}#m add_country

	/**
	* Add gtd element into document.
	*
	* @param	Object(moysklad_gtd)	$item
	* @return	&$this
	**/
	public function &add_gtd(moysklad_gtd $item) {
		$this->commonAddElement('gtd', $item);
		return $this;
	}#m add_gtd

	/**
	* Add uom (Unit Of Metric?) element into document.
	*
	* @param	Object(moysklad_uom)	$item
	* @return	&$this
	**/
	public function &add_uom(moysklad_uom $item) {
		$this->commonAddElement('uoms', $item);
		return $this;
	}#m add_uom

	/**
	* Add goodFolder element into document.
	*
	* @param	Object(moysklad_goodFolder)	$item
	* @return	&$this
	**/
	public function &add_goodFolder(moysklad_goodFolder $item) {
		$this->commonAddElement('goodFolders', $item);
		return $this;
	}#m add_goodFolder

	/**
	* Add good element into document.
	*
	* @param	Object(moysklad_good)	$item
	* @return	&$this
	**/
	public function &add_good(moysklad_good $item) {
		$this->commonAddElement('goods', $item);
		return $this;
	}#m add_good

	/**
	* Add service element into document.
	*
	* @param	Object(moysklad_service)	$item
	* @return	&$this
	**/
	public function &add_service(moysklad_service $item) {
		$this->commonAddElement('service', $item);
		return $this;
	}#m add_service

	/**
	* Add thing element into document.
	*
	* @param	Object(moysklad_thing)	$item
	* @return	&$this
	**/
	public function &add_thing(moysklad_thing $item) {
		$this->commonAddElement('things', $item);
		return $this;
	}#m add_thing

	/**
	* Add myCompany element into document.
	*
	* @param	Object(moysklad_myCompany)	$item
	* @return	&$this
	**/
	public function &add_myCompany(moysklad_myCompany $item) {
		$this->commonAddElement('myCompany', $item);
		return $this;
	}#m add_myCompany

	/**
	* Add agent (поставщик) element into document.
	*
	* @param	Object(moysklad_agent)	$item
	* @return	&$this
	**/
	public function &add_agent(moysklad_agent $item) {
		$this->commonAddElement('agents', $item);
		return $this;
	}#m add_agent

	/**
	* Add company (контрагент) element into document.
	*
	* @param	Object(moysklad_company)	$item
	* @return	&$this
	**/
	public function &add_company(moysklad_company $item) {
		$this->commonAddElement('companies', $item);
		return $this;
	}#m add_company

	/**
	* Add person element into document.
	*
	* @param	Object(moysklad_person)	$item
	* @return	&$this
	**/
	public function &add_person(moysklad_person $item) {
		$this->commonAddElement('persons', $item);
		return $this;
	}#m add_person

	/**
	* Add place element into document.
	*
	* @param	Object(moysklad_place)	$item
	* @return	&$this
	**/
	public function &add_place(moysklad_place $item) {
		$this->commonAddElement('places', $item);
		return $this;
	}#m add_place

	/**
	* Add warehouse element into document.
	*
	* @param	Object(moysklad_warehouse)	$item
	* @return	&$this
	**/
	public function &add_warehouse(moysklad_warehouse $item) {
		$this->commonAddElement('warehouses', $item);
		return $this;
	}#m add_warehouse

	/**
	* Add project element into document.
	*
	* @param	Object(moysklad_project)	$item
	* @return	&$this
	**/
	public function &add_project(moysklad_project $item) {
		$this->commonAddElement('project', $item);
		return $this;
	}#m add_project

	/**
	* Add contract element into document.
	*
	* @param	Object(moysklad_contract)	$item
	* @return	&$this
	**/
	public function &add_contract(moysklad_contract $item) {
		$this->commonAddElement('contract', $item);
		return $this;
	}#m add_contract

	/**
	* Add processingPlan (сборка-производство) element into document.
	*
	* @param	Object(moysklad_processingPlan)	$item
	* @return	&$this
	**/
	public function &add_processingPlan(moysklad_processingPlan $item) {
		$this->commonAddElement('processingPlans', $item);
		return $this;
	}#m add_processingPlan

	/**
	* Add consignment (сборка, технологическая карта?) element into document.
	*
	* @param	Object(moysklad_consignment)	$item
	* @return	&$this
	**/
	public function &add_consignment(moysklad_consignment $item) {
		$this->commonAddElement('consignments', $item);
		return $this;
	}#m add_consignment

	/**
	* Add priceList element into document.
	*
	* @param	Object(moysklad_priceList)	$item
	* @return	&$this
	**/
	public function &add_priceList(moysklad_priceList $item) {
		$this->commonAddElement('priceLists', $item);
		return $this;
	}#m add_priceList

	/**
	* Adddemand element into document.
	*
	* @param	Object(moysklad_demand)	$item
	* @return	&$this
	**/
	public function &add_demand(moysklad_demand $item) {
		$this->commonAddElement('deliveries-demand', $item);
		return $this;
	}#m add_demand

	/**
	* Add supply element into document.
	*
	* @param	Object(moysklad_supply)	$item
	* @return	&$this
	**/
	public function &add_supply(moysklad_supply $item) {
		$this->commonAddElement('deliveries-supply', $item);
		return $this;
	}#m add_supply

	/**
	* Add inventory element into document.
	*
	* @param	Object(moysklad_inventory)	$item
	* @return	&$this
	**/
	public function &add_inventory(moysklad_inventory $item) {
		$this->commonAddElement('inventories', $item);
		return $this;
	}#m add_inventory

	/**
	* Add move element into document.
	*
	* @param	Object(moysklad_move)	$item
	* @return	&$this
	**/
	public function &add_move(moysklad_move $item) {
		$this->commonAddElement('moves', $item);
		return $this;
	}#m add_move

	/**
	* Add loss element into document.
	*
	* @param	Object(moysklad_loss)	$item
	* @return	&$this
	**/
	public function &add_loss(moysklad_loss $item) {
		$this->commonAddElement('losses', $item);
		return $this;
	}#m add_loss

	/**
	* Add enter element into document.
	*
	* @param	Object(moysklad_enter)	$item
	* @return	&$this
	**/
	public function &add_enter(moysklad_enter $item) {
		$this->commonAddElement('enters', $item);
		return $this;
	}#m add_enter

	/**
	* Add invoiceIn element into document.
	*
	* @param	Object(moysklad_invoiceIn)	$item
	* @return	&$this
	**/
	public function &add_invoiceIn(moysklad_invoiceIn $item) {
		$this->commonAddElement('invoiceIn', $item);
		return $this;
	}#m add_invoiceIn

	/**
	* Add invoicesOut element into document.
	*
	* @param	Object(moysklad_invoicesOut)	$item
	* @return	&$this
	**/
	public function &add_invoicesOut(moysklad_invoicesOut $item) {
		$this->commonAddElement('invoicesOut', $item);
		return $this;
	}#m add_invoicesOut

	/**
	* Add processing element into document.
	*
	* @param	Object(moysklad_processing)	$item
	* @return	&$this
	**/
	public function &add_processing(moysklad_processing $item) {
		$this->commonAddElement('processings', $item);
		return $this;
	}#m add_processing

	/**
	* Add customerOrder element into document.
	*
	* @param	Object(moysklad_customerOrder)	$item
	* @return	&$this
	**/
	public function &add_customerOrder(moysklad_customerOrder $item) {
		$this->commonAddElement('customerOrders', $item);
		return $this;
	}#m add_customerOrder

	/**
	* Add purchaseOrder element into document.
	*
	* @param	Object(moysklad_purchaseOrder)	$item
	* @return	&$this
	**/
	public function &add_purchaseOrder(moysklad_purchaseOrder $item) {
		$this->commonAddElement('purchaseOrders', $item);
		return $this;
	}#m add_purchaseOrder

	/**
	* Add connector element into document.
	*
	* @param	Object(moysklad_connector)	$item
	* @return	&$this
	**/
	public function &add_connector(moysklad_connector $item) {
		$this->commonAddElement('connectors', $item);
		return $this;
	}#m add_connector

	/**
	* Validate XML by its scherma http://www.moysklad.ru/schema/exchange-1.1.0.xsd
	*
	* Throws(moysklad_exception_novalid)
	**/
	public function &schemaValidate(){
		if (!$this->dom_->schemaValidate('http://www.moysklad.ru/schema/exchange-1.1.0.xsd')){
			throw new moysklad_exception_novalid('Documen does no valid!');
		}
		return $this;
	}#m schemaValidate

	/**
	* Get string representation of XML document.
	*
	* @param	array	$opts Array of options, which must be applyed to DOMDocument
	*					object first. As array of Key=>value. Nothing checked.
	* @return	string
	* Throws(moysklad_exception_novalid)
	**/
	public function saveXML(array $opts = array( 'formatOutput' => true )){
		foreach ($opts as $opt => $val){
			$this->dom_->{$opt} = $val;
		}
		return $this->dom_->saveXML();
	}#m saveXML
}#c moysklad
?>