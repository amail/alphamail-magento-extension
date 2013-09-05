<?php

class Comfirm_AlphaMail_Block_SendLog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sendLogGrid');
        $this->setDefaultSort('send_id');
        $this->setDefaultDir('ASC');
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
                'onclick'   => 'setLocation(\''.$this->getUrl('alphamail/SendLog/RemoveAll', array('_current'=>true)).'\')',
                'class' => ''
            ))
        );

        return parent::_prepareLayout();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('alphamail/send_log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('send_id', array(
            'header'    => Mage::helper('adminhtml')->__('Id'),
        	'width'     => '30px',
            'index'     => 'send_id',
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'width'     => '20px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                0 => 'Queued Internally',
                1 => 'Authentication Error',
                2 => 'Connection Error',
                3 => 'Sent'
            )
        ));
        $this->addColumn('template_name', array(
            'header'    => Mage::helper('adminhtml')->__('Template Name'),
            'width'     => '30px',
            'index'     => 'template_name',
        ));
        $this->addColumn('am_queue_id', array(
            'header'    => Mage::helper('adminhtml')->__('AlphaMail Queue Id'),
        	'width'     => '250px',
            'index'     => 'am_queue_id',
            'default'   => '(not send yet)'
        ));
        $this->addColumn('sent_at', array(
            'header'    => Mage::helper('adminhtml')->__('Sent'),
            'width'     => '30px',
            'index'     => 'sent_at',
            'default'   => '(not sent yet)'
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('adminhtml')->__('Created'),
            'width'     => '30px',
            'index'     => 'created_at',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/EventLog', array('send_id' => $row->getId()));
    }
}
