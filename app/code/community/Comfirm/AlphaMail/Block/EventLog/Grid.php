<?php

class Comfirm_AlphaMail_Block_EventLog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('eventLogGrid');
        $this->setDefaultSort('event_id');
        $this->setDefaultDir('ASC');
        $this->setSendId($this->getRequest()->getParam('send_id', false));
    }
    
    public function  getSearchButtonHtml()
    {
        return parent::getSearchButtonHtml() . $this->getChildHtml('remove_all_button');
    }

    protected function  _prepareLayout()
    {
        $this->setChild('remove_all_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('alphamail')->__('Remove All'),
                'onclick'   => 'setLocation(\''.$this->getUrl('alphamail/EventLog/RemoveAll', array('_current'=>true)).'\')',
                'class' => ''
            ))
        );

        return parent::_prepareLayout();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('alphamail/event_log')->getCollection();

        if($this->getSendId()){
            $collection->addFieldToFilter("send_id", array("eq", $this->getSendId()));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('event_id', array(
            'header'    => Mage::helper('adminhtml')->__('Id'),
        	'width'     => '30px',
            'index'     => 'event_id',
        ));
        $this->addColumn('send_id', array(
            'header'    => Mage::helper('adminhtml')->__('Send Log Id'),
        	'width'     => '60px',
            'index'     => 'send_id',
            'default'   => '(not set)'
        ));
        $this->addColumn('message', array(
            'header'    => Mage::helper('adminhtml')->__('Message'),
        	'width'     => '160px',
            'index'     => 'message',
        ));
        $this->addColumn('type', array(
            'header'    => Mage::helper('adminhtml')->__('Type'),
            'width'     => '160px',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => array(
                0 => 'Info',
                1 => 'Error',
                2 => 'Debug'
            )
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('adminhtml')->__('Created'),
            'width'     => '160px',
            'index'     => 'created_at',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('event_id' => $row->getId()));
    }
}
